<?php

/**
 *  PHP Mikrotik Billing (https://github.com/hotspo/)
 *  by https://t.me/ibnux
 **/

// Helper function to get the real thread ID (if available)
function getRealThreadID()
{
    $threadId = null;
    if (function_exists('pthread_self')) {
        $threadId = pthread_self();
    }
    return $threadId;
}

class Mutex
{
    private $file;
    private $handle;

    public function __construct()
    {
        $this->file = sys_get_temp_dir() . '/mutex.lock';
    }

    public function acquire()
    {
        $this->handle = fopen($this->file, 'w+');
        if (!flock($this->handle, LOCK_EX)) {
            throw new Exception('Failed to acquire mutex');
        }
    }

    public function release()
    {
        if ($this->handle) {
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
            $this->handle = null;
        }
    }
}

class Message
{
    private static $smsCache = [];
    public static $invoiceCache = array();
    private static $mutex; // Mutex for thread-safe caching
    private static $cacheFile = '';
    private static $maxCacheSize = 1000; // Maximum number of cached entries
    private static $cacheClearTime = '00:00:00'; // Time to clear the cache (midnight)

    public static function sendTelegram($txt)
    {
        global $config;
        run_hook('send_telegram'); #HOOK
        if (!empty($config['telegram_bot']) && !empty($config['telegram_target_id'])) {
            return Http::getData('https://api.telegram.org/bot' . $config['telegram_bot'] . '/sendMessage?chat_id=' . $config['telegram_target_id'] . '&text=' . urlencode($txt));
        }
    }

    public static function sendSMS($phone, $txt)
    {
        global $config;
        run_hook('send_sms'); #HOOK
    
        // Get the current process ID and thread ID
        $processId = getmypid();
        $threadId = getRealThreadID();
    
        // Acquire the mutex
        self::acquireMutex();
    
        // Load the cache from a file
        self::$cacheFile = sys_get_temp_dir() . '/sms_cache.json';
        if (file_exists(self::$cacheFile)) {
            self::$smsCache = json_decode(file_get_contents(self::$cacheFile), true);
        }
    
        // Check if SMS was sent to the same customer within the last 120 seconds
        if (isset(self::$smsCache[$phone])) {
            $lastSentTime = self::$smsCache[$phone];
            if (time() - $lastSentTime < 120) {
                // Release the mutex
                self::releaseMutex();
                return "SMS not sent. Sent within the last 120 seconds."; // Do not send SMS if sent within the last 120 seconds
            }
        }
    
        $response = '';
    
        if (!empty($config['sms_url'])) {
            if (strlen($config['sms_url']) > 4 && substr($config['sms_url'], 0, 4) != "http") {
                if (strlen($txt) > 160) {
                    $txts = str_split($txt, 160);
                    try {
                        $mikrotik = Mikrotik::info($config['sms_url']);
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                        foreach ($txts as $txt) {
                            $response = Mikrotik::sendSMS($client, $phone, $txt);
                        }
                    } catch (Exception $e) {
                        $response = "Error: " . $e->getMessage();
                    }
                } else {
                    try {
                        $mikrotik = Mikrotik::info($config['sms_url']);
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                        $response = Mikrotik::sendSMS($client, $phone, $txt);
                    } catch (Exception $e) {
                        $response = "Error: " . $e->getMessage();
                    }
                }
            } else {
                $smsurl = str_replace('[number]', urlencode($phone), $config['sms_url']);
                $smsurl = str_replace('[text]', urlencode($txt), $smsurl);
                $response = Http::getData($smsurl);
            }
    
            // Update the SMS cache with the current timestamp
            self::$smsCache[$phone] = time();
    
            // Limit the cache size
            if (count(self::$smsCache) > self::$maxCacheSize) {
                array_shift(self::$smsCache); // Remove the oldest entry
            }
        }
    
        // Save the cache to a file
        file_put_contents(self::$cacheFile, json_encode(self::$smsCache));
    
        // Release the mutex
        self::releaseMutex();
    
        return $response;
    }

    public static function sendWhatsapp($phone, $txt)
    {
        global $config;
        run_hook('send_whatsapp'); #HOOK

        // Get the current process ID and thread ID
        $processId = getmypid();
        $threadId = getRealThreadID();

        if (!empty($config['wa_url'])) {            $waurl = str_replace('[number]', urlencode(Lang::phoneFormat($phone)), $config['wa_url']);
            $waurl = str_replace('[text]', urlencode($txt), $waurl);
            Http::getData($waurl);
        }
    }
    public static function sendEmail($to, $subject, $body)
    {
        global $config;
        run_hook('send_email'); #HOOK
        if (empty($config['smtp_host'])) {
            $attr = "";
            if (!empty($config['mail_from'])) {
                $attr .= "From: " . $config['mail_from'] . "\r\n";
            }
            if (!empty($config['mail_reply_to'])) {
                $attr .= "Reply-To: " . $config['mail_reply_to'] . "\r\n";
            }
            mail($to, $subject, $body, $attr);
        } else {
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Host       = $config['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['smtp_user'];
            $mail->Password   = $config['smtp_pass'];
            $mail->SMTPSecure = $config['smtp_ssltls'];
            $mail->Port       = $config['smtp_port'];
            if (!empty($config['mail_from'])) {
                $mail->setFrom($config['mail_from']);
            }
            if (!empty($config['mail_reply_to'])) {
                $mail->addReplyTo($config['mail_reply_to']);
            }
            $mail->isHTML(false);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();
            die();
        }
    }
    public static function sendPackageNotification($customer, $package, $price, $message, $via)
    {
        global $u;
        $processId = getmypid();
        $threadId = getRealThreadID();

        $msg = str_replace('[[name]]', $customer['fullname'], $message);
        $msg = str_replace('[[username]]', $customer['username'], $msg);
        $msg = str_replace('[[package]]', $package, $msg);
        $msg = str_replace('[[price]]', $price, $msg);
        if($u){
            $msg = str_replace('[[expired_date]]', Lang::dateAndTimeFormat($u['expiration'], $u['time']), $msg);
        }
        if (
            !empty($customer['phonenumber']) && strlen($customer['phonenumber']) > 5
            && !empty($message) && in_array($via, ['sms', 'wa'])
        ) {
            if ($via == 'sms') {
                Message::sendSMS($customer['phonenumber'], $msg);
            } else if ($via == 'wa') {
                Message::sendWhatsapp($customer['phonenumber'], $msg);
            }
        }
        return "$via: $msg";
    }

    public static function sendBalanceNotification($phone, $name, $balance, $balance_now, $message, $customer, $via)
    {
        $processId = getmypid();
        $threadId = getRealThreadID();
        $msg = str_replace('[[username]]', $customer['username'], $message);
        $msg = str_replace('[[name]]', $name, $message);
        $msg = str_replace('[[current_balance]]', Lang::moneyFormat($balance_now), $msg);
        $msg = str_replace('[[balance]]', Lang::moneyFormat($balance), $msg);
        if (
            !empty($phone) && strlen($phone) > 5
            && !empty($message) && in_array($via, ['sms', 'wa'])
        ) {
            if ($via == 'sms') {
                Message::sendSMS($phone, $msg);
            } else if ($via == 'wa') {
                Message::sendWhatsapp($phone, $msg);
            }
        }
        return "$via: $msg";
    }

    public static function sendInvoice($cust, $trx)
    {
        global $config;
        $textInvoice = Lang::getNotifText('invoice_paid');
        $textInvoice = str_replace('[[company_name]]', $config['CompanyName'], $textInvoice);
        $textInvoice = str_replace('[[address]]', $config['address'], $textInvoice);
        $textInvoice = str_replace('[[phone]]', $config['phone'], $textInvoice);
        $textInvoice = str_replace('[[invoice]]', $trx['invoice'], $textInvoice);
        $textInvoice = str_replace('[[date]]', Lang::dateAndTimeFormat($trx['recharged_on'], $trx['recharged_time']), $textInvoice);
        if (!empty($trx['note'])) {
            $textInvoice = str_replace('[[note]]', $trx['note'], $textInvoice);
        }
        $gc = explode("-", $trx['method']);
        $textInvoice = str_replace('[[payment_gateway]]', trim($gc[0]), $textInvoice);
        $textInvoice = str_replace('[[payment_channel]]', trim($gc[1]), $textInvoice);
        $textInvoice = str_replace('[[type]]', $trx['type'], $textInvoice);
        $textInvoice = str_replace('[[plan_name]]', $trx['plan_name'], $textInvoice);
        $textInvoice = str_replace('[[plan_price]]',  Lang::moneyFormat($trx['price']), $textInvoice);
        $textInvoice = str_replace('[[name]]', $cust['fullname'], $textInvoice);
        $textInvoice = str_replace('[[note]]', $cust['note'], $textInvoice);
        $textInvoice = str_replace('[[user_name]]', $trx['username'], $textInvoice);
        $textInvoice = str_replace('[[user_password]]', $cust['password'], $textInvoice);
        $textInvoice = str_replace('[[username]]', $trx['username'], $textInvoice);
        $textInvoice = str_replace('[[password]]', $cust['password'], $textInvoice);
        $textInvoice = str_replace('[[expired_date]]', Lang::dateAndTimeFormat($trx['expiration'], $trx['time']), $textInvoice);
        $textInvoice = str_replace('[[footer]]', $config['note'], $textInvoice);

        $phoneNumber = $cust['phonenumber'];

        // Get the current process ID and thread ID
        $processId = getmypid();
        $threadId = getRealThreadID();

        // Acquire the mutex
        self::acquireMutex();

        // Load the cache from a file
        self::$cacheFile = sys_get_temp_dir() . '/invoice_cache.json';
        if (file_exists(self::$cacheFile)) {
            self::$invoiceCache = json_decode(file_get_contents(self::$cacheFile), true);
        }

        // Check if an invoice was sent to the same customer within the last 120 seconds
        if (isset(self::$invoiceCache[$phoneNumber])) {
            $lastSentTime = self::$invoiceCache[$phoneNumber];
            if (time() - $lastSentTime < 120) {
                // Release the mutex
                self::releaseMutex();
                return; // Do not send invoice if sent within the last 120 seconds
            }
        }

        if ($config['user_notification_payment'] == 'sms') {
            Message::sendSMS($phoneNumber, $textInvoice);
        } else if ($config['user_notification_payment'] == 'wa') {
            Message::sendWhatsapp($phoneNumber, $textInvoice);
        }

        // Update the invoice cache with the current timestamp
        self::$invoiceCache[$phoneNumber] = time();

        // Limit the cache size
        if (count(self::$invoiceCache) > self::$maxCacheSize) {
            array_shift(self::$invoiceCache); // Remove the oldest entry
        }

        // Save the caches to files
        file_put_contents(self::$cacheFile, json_encode(self::$invoiceCache));

        // Release the mutex
        self::releaseMutex();
    }

    public static function sendAccountCreateNotification($phone, $name, $username, $password, $message, $via)
    {
        $processId = getmypid();
        $threadId = getRealThreadID();

        $msg = str_replace('[[name]]', $name, $message);
        $msg = str_replace('[[user_name]]', $username, $msg);
        $msg = str_replace('[[user_password]]', $password, $msg);
        if (
            !empty($phone) && strlen($phone) > 5
            && !empty($message) && in_array($via, ['sms', 'wa'])
        ) {
            if ($via == 'sms') {
                Message::sendSMS($phone, $msg);
            } else if ($via == 'wa') {
                Message::sendWhatsapp($phone, $msg);
            }
        }
        return "$via: $msg";
    }

    public static function sendUnknownPayment($phone, $amount, $message, $via)
    {
        $processId = getmypid();
        $threadId = getRealThreadID();

        if (!empty($message)) {
            $msg = str_replace('[[amount]]', $amount, $message);
            $msg = str_replace('[[phone]]', $phone, $msg);
            if (!empty($phone) && strlen($phone) > 5) {
                if ($via == 'sms') {
                    Message::sendSMS($phone, $msg);
                } else if ($via == 'wa') {
                    Message::sendWhatsapp($phone, $msg);
                }
            }
        }
        return "$via: $msg";
    }

    private static function acquireMutex()
    {
        if (!self::$mutex) {
            self::$mutex = new Mutex();
        }
        self::$mutex->acquire();
    }

    private static function releaseMutex()
    {
        if (self::$mutex) {
            self::$mutex->release();
        }
    }

    public static function clearCache()
    {
        self::$smsCache = [];
        self::$invoiceCache = [];
        file_put_contents(sys_get_temp_dir() . '/sms_cache.json', json_encode(self::$smsCache));
        file_put_contents(sys_get_temp_dir() . '/invoice_cache.json', json_encode(self::$invoiceCache));
    }

    public static function checkCacheClearTime()
    {
        $currentTime = date('H:i:s');
        if ($currentTime === self::$cacheClearTime) {
            self::clearCache();
        }
    }
}