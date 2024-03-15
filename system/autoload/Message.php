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
            error_log("[$processId:$threadId] SMS cache loaded from file: " . self::$cacheFile);
        }

        // Check if SMS was sent to the same customer within the last 120 seconds
        if (isset(self::$smsCache[$phone])) {
            $lastSentTime = self::$smsCache[$phone];
            if (time() - $lastSentTime < 120) {
                error_log("[$processId:$threadId] SMS not sent to $phone within 120 seconds. Last sent at: " . date('Y-m-d H:i:s', $lastSentTime));
                // Release the mutex
                self::releaseMutex();
                return; // Do not send SMS if sent within the last 120 seconds
            }
        }

        if (!empty($config['sms_url'])) {
            if (strlen($config['sms_url']) > 4 && substr($config['sms_url'], 0, 4) != "http") {
                if (strlen($txt) > 160) {
                    $txts = str_split($txt, 160);
                    try {
                        $mikrotik = Mikrotik::info($config['sms_url']);
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                        foreach ($txts as $txt) {
                            Mikrotik::sendSMS($client, $phone, $txt);
                            error_log("[$processId:$threadId] SMS sent to $phone using Mikrotik.");
                        }
                    } catch (Exception $e) {
                        // ignore, add to logs
                        error_log("[$processId:$threadId] Failed to send SMS using Mikrotik.\n" . $e->getMessage());
                    }
                } else {
                    try {
                        $mikrotik = Mikrotik::info($config['sms_url']);
                        $client = Mikrotik::getClient($mikrotik['ip_address'], $mikrotik['username'], $mikrotik['password']);
                        Mikrotik::sendSMS($client, $phone, $txt);
                        error_log("[$processId:$threadId] SMS sent to $phone using Mikrotik.");
                    } catch (Exception $e) {
                        // ignore, add to logs
                        error_log("[$processId:$threadId] Failed to send SMS using Mikrotik.\n" . $e->getMessage());
                    }
                }
            } else {
                $smsurl = str_replace('[number]', urlencode($phone), $config['sms_url']);
                $smsurl = str_replace('[text]', urlencode($txt), $smsurl);
                $response = Http::getData($smsurl);
                error_log("[$processId:$threadId] SMS sent to $phone using external URL. Response: $response");
            }

            // Update the SMS cache with the current timestamp
            self::$smsCache[$phone] = time();
            error_log("[$processId:$threadId] SMS cache updated for $phone. Timestamp: " . date('Y-m-d H:i:s', self::$smsCache[$phone]));
        }

        // Save the cache to a file
        file_put_contents(self::$cacheFile, json_encode(self::$smsCache));
        error_log("[$processId:$threadId] SMS cache saved to file: " . self::$cacheFile);

        // Release the mutex
        self::releaseMutex();
    }

    public static function sendWhatsapp($phone, $txt)
    {
        global $config;
        run_hook('send_whatsapp'); #HOOK

        // Get the current process ID and thread ID
        $processId = getmypid();
        $threadId = getRealThreadID();

        if (!empty($config['wa_url'])) {
            $waurl = str_replace('[number]', urlencode($phone), $config['wa_url']);
            $waurl = str_replace('[text]', urlencode($txt), $waurl);
            $response = Http::getData($waurl);
            error_log("[$processId:$threadId] WhatsApp message sent to $phone. Response: $response");
        }
    }

    public static function sendPackageNotification($phone, $name, $package, $price, $message, $via)
    {
        $processId = getmypid();
        $threadId = getRealThreadID();

        $msg = str_replace('[[name]]', $name, $message);
        $msg = str_replace('[[package]]', $package, $msg);
        $msg = str_replace('[[price]]', $price, $msg);
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
        error_log("[$processId:$threadId] Package notification sent via $via to $phone: $msg");
        return "$via: $msg";
    }

    public static function sendBalanceNotification($phone, $name, $balance, $balance_now, $message, $via)
    {
        $processId = getmypid();
        $threadId = getRealThreadID();

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
        error_log("[$processId:$threadId] Balance notification sent via $via to $phone: $msg");
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
        $gc = explode("-", $trx['method']);
        $textInvoice = str_replace('[[payment_gateway]]', trim($gc[0]), $textInvoice);
        $textInvoice = str_replace('[[payment_channel]]', trim($gc[1]), $textInvoice);
        $textInvoice = str_replace('[[type]]', $trx['type'], $textInvoice);
        $textInvoice = str_replace('[[plan_name]]', $trx['plan_name'], $textInvoice);
        $textInvoice = str_replace('[[plan_price]]', Lang::moneyFormat($trx['price']), $textInvoice);
        $textInvoice = str_replace('[[name]]', $cust['fullname'], $textInvoice);
        $textInvoice = str_replace('[[user_name]]', $trx['username'], $textInvoice);
        $textInvoice = str_replace('[[user_password]]', $cust['password'], $textInvoice);
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
            error_log("[$processId:$threadId] Invoice cache loaded from file: " . self::$cacheFile);
        }

        // Check if SMS was sent to the same customer within the last 120 seconds
        if (isset(self::$smsCache[$phoneNumber])) {
            $lastSentTime = self::$smsCache[$phoneNumber];
            if (time() - $lastSentTime < 120) {
                error_log("[$processId:$threadId] Invoice SMS not sent to $phoneNumber within 120 seconds. Last SMS sent at: " . date('Y-m-d H:i:s', $lastSentTime));
                // Release the mutex
                self::releaseMutex();
                return; // Do not send SMS if sent within the last 120 seconds
            }
        }

        // Check if an invoice was sent to the same customer within the last 120 seconds
        if (isset(self::$invoiceCache[$phoneNumber])) {
            $lastSentTime = self::$invoiceCache[$phoneNumber];
            if (time() - $lastSentTime < 120) {
                error_log("[$processId:$threadId] Invoice SMS not sent to $phoneNumber within 120 seconds. Last invoice sent at: " . date('Y-m-d H:i:s', $lastSentTime));
                // Release the mutex
                self::releaseMutex();
                return; // Do not send invoice if sent within the last 120 seconds
            }
        }

        if ($config['user_notification_payment'] == 'sms') {
            Message::sendSMS($phoneNumber, $textInvoice);
            error_log("[$processId:$threadId] Invoice SMS sent to $phoneNumber.");
        } else if ($config['user_notification_payment'] == 'wa') {
            Message::sendWhatsapp($phoneNumber, $textInvoice);
            error_log("[$processId:$threadId] Invoice WhatsApp message sent to $phoneNumber.");
        }

        // Update the invoice cache with the current timestamp
        self::$invoiceCache[$phoneNumber] = time();
        error_log("[$processId:$threadId] Invoice cache updated for $phoneNumber. Timestamp: " . date('Y-m-d H:i:s', self::$invoiceCache[$phoneNumber]));

        // Update the SMS cache with the current timestamp
        self::$smsCache[$phoneNumber] = time();
        error_log("[$processId:$threadId] SMS cache updated for $phoneNumber. Timestamp: " . date('Y-m-d H:i:s', self::$smsCache[$phoneNumber]));

        // Save the caches to files
        file_put_contents(self::$cacheFile, json_encode(self::$invoiceCache));
        error_log("[$processId:$threadId] Invoice cache saved to file: " . self::$cacheFile);
        file_put_contents(sys_get_temp_dir() . '/sms_cache.json', json_encode(self::$smsCache));
        error_log("[$processId:$threadId] SMS cache saved to file: " . sys_get_temp_dir() . '/sms_cache.json');

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
        error_log("[$processId:$threadId] Account creation notification sent via $via to $phone: $msg");
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
        error_log("[$processId:$threadId] Unknown payment notification sent via $via to $phone: $msg");
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
}