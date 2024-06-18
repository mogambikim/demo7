<?php
/**
 *  PHP Mikrotik Billing (https://github.com/hotspo/)
 *  by https://t.me/ibnux
 **/
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require $root_path . 'system/autoload/mail/Exception.php';
require $root_path . 'system/autoload/mail/PHPMailer.php';
require $root_path . 'system/autoload/mail/SMTP.php';

class Message
{
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
        }

        return $response;
    }

    public static function sendWhatsapp($phone, $txt)
    {
        global $config;
        run_hook('send_whatsapp'); #HOOK

        if (!empty($config['wa_url'])) {
            $waurl = str_replace('[number]', urlencode(Lang::phoneFormat($phone)), $config['wa_url']);
            $waurl = str_replace('[text]', urlencode($txt), $waurl);
            Http::getData($waurl);
        }
    }

    public static function sendEmail($to, $subject, $body)
    {
        global $config;
        if(empty($body)){
            return "";
        }
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
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
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
        }
    }

    public static function sendPackageNotification($customer, $package, $price, $message, $via)
    {
        global $u;
        $msg = str_replace('[[name]]', $customer['fullname'], $message);
        $msg = str_replace('[[username]]', $customer['username'], $msg);
        $msg = str_replace('[[package]]', $package, $msg);
        $msg = str_replace('[[price]]', $price, $msg);
        if ($u) {
            $msg = str_replace('[[expired_date]]', Lang::dateAndTimeFormat($u['expiration'], $u['time']), $msg);
        }
    
        if (
            !empty($customer['phonenumber']) && strlen($customer['phonenumber']) > 5
            && !empty($message) && in_array($via, ['sms', 'wa', 'email', 'both', 'sms_email', 'email_wa', 'all'])
        ) {
            if ($via == 'sms' || $via == 'both' || $via == 'sms_email' || $via == 'all') {
                Message::sendSMS($customer['phonenumber'], $msg);
            }
            if ($via == 'wa' || $via == 'both' || $via == 'email_wa' || $via == 'all') {
                Message::sendWhatsapp($customer['phonenumber'], $msg);
            }
            if ($via == 'email' || $via == 'sms_email' || $via == 'email_wa' || $via == 'all') {
                $subject = "Package Notification";
                Message::sendEmail($customer['email'], $subject, $msg);
            }
        }
    
        return "$via: $msg";
    }

    public static function sendBalanceNotification($phone, $name, $balance, $balance_now, $message, $customer, $via)
    {
        $msg = str_replace('[[username]]', $customer['username'], $message);
        $msg = str_replace('[[name]]', $name, $msg);
        $msg = str_replace('[[current_balance]]', Lang::moneyFormat($balance_now), $msg);
        $msg = str_replace('[[balance]]', Lang::moneyFormat($balance), $msg);
    
        if (
            !empty($phone) && strlen($phone) > 5
            && !empty($message) && in_array($via, ['sms', 'wa', 'email', 'both', 'sms_email', 'email_wa', 'all'])
        ) {
            if ($via == 'sms' || $via == 'both' || $via == 'sms_email' || $via == 'all') {
                Message::sendSMS($phone, $msg);
            }
            if ($via == 'wa' || $via == 'both' || $via == 'email_wa' || $via == 'all') {
                Message::sendWhatsapp($phone, $msg);
            }
            if ($via == 'email' || $via == 'sms_email' || $via == 'email_wa' || $via == 'all') {
                $subject = "Balance Notification";
                Message::sendEmail($customer['email'], $subject, $msg);
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
    
        if ($config['user_notification_payment'] == 'sms' || $config['user_notification_payment'] == 'both' || $config['user_notification_payment'] == 'sms_email' || $config['user_notification_payment'] == 'all') {
            Message::sendSMS($phoneNumber, $textInvoice);
        }
        if ($config['user_notification_payment'] == 'wa' || $config['user_notification_payment'] == 'both' || $config['user_notification_payment'] == 'email_wa' || $config['user_notification_payment'] == 'all') {
            Message::sendWhatsapp($phoneNumber, $textInvoice);
        }
        if ($config['user_notification_payment'] == 'email' || $config['user_notification_payment'] == 'sms_email' || $config['user_notification_payment'] == 'email_wa' || $config['user_notification_payment'] == 'all') {
            $subject = "Invoice Paid Notification";
            Message::sendEmail($cust['email'], $subject, $textInvoice);
        }
    }

    public static function sendAccountCreateNotification($phone, $name, $username, $password, $message, $via)
    {
        $msg = str_replace('[[name]]', $name, $message);
        $msg = str_replace('[[user_name]]', $username, $msg);
        $msg = str_replace('[[user_password]]', $password, $msg);
    
        if (
            !empty($phone) && strlen($phone) > 5
            && !empty($message) && in_array($via, ['sms', 'wa', 'email', 'both', 'sms_email', 'email_wa', 'all'])
        ) {
            if ($via == 'sms' || $via == 'both' || $via == 'sms_email' || $via == 'all') {
                Message::sendSMS($phone, $msg);
            }
            if ($via == 'wa' || $via == 'both' || $via == 'email_wa' || $via == 'all') {
                Message::sendWhatsapp($phone, $msg);
            }
            if ($via == 'email' || $via == 'sms_email' || $via == 'email_wa' || $via == 'all') {
                $subject = "Account Created Notification";
                Message::sendEmail($phone, $subject, $msg);
            }
        }
        return "$via: $msg";
    }

    public static function sendUnknownPayment($phone, $amount, $message, $via)
    {
        if (!empty($message)) {
            $msg = str_replace('[[amount]]', $amount, $message);
            $msg = str_replace('[[phone]]', $phone, $msg);
    
            if (!empty($phone) && strlen($phone) > 5) {
                if ($via == 'sms' || $via == 'both' || $via == 'sms_email' || $via == 'all') {
                    Message::sendSMS($phone, $msg);
                }
                if ($via == 'wa' || $via == 'both' || $via == 'email_wa' || $via == 'all') {
                    Message::sendWhatsapp($phone, $msg);
                }
                if ($via == 'email' || $via == 'sms_email' || $via == 'email_wa' || $via == 'all') {
                    $subject = "Unknown Payment Notification";
                    Message::sendEmail($phone, $subject, $msg);
                }
            }
        }
        return "$via: $msg";
    }
    public static function sendRouterStatusNotification($router, $message, $via)
    {
        global $config;

        // Send notification based on preferred method
        if (
            !empty($router['notification_phone']) && strlen($router['notification_phone']) > 5
            && !empty($message) && in_array($via, ['sms', 'wa', 'email', 'both', 'sms_email', 'email_wa', 'all'])
        ) {
            if ($via == 'sms' || $via == 'both' || $via == 'sms_email' || $via == 'all') {
                self::sendSMS($router['notification_phone'], $message);
            }
            if ($via == 'wa' || $via == 'both' || $via == 'email_wa' || $via == 'all') {
                self::sendWhatsapp($router['notification_phone'], $message);
            }
            if ($via == 'email' || $via == 'sms_email' || $via == 'email_wa' || $via == 'all') {
                $subject = "Router Status Notification";
                self::sendEmail($router['notification_email'], $subject, $message); // Assuming you have a notification email field
            }
        }

        return "$via: $message";
    }
}