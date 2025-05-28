<?php
session_start();

 function generateCode()
    {
        $characters = 6;

        $possible = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
        $code = '';
        $i = 0;
        while ($i < $characters) {
            $code .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $i++;
        }
        //SessionManager::set('secaccesscode', md5($code));
        $_SESSION['secaccesscode'] = $code;
        return $code;
    }
$otp = generateCode();

$user_Email = 'Navi Mumbai Web <no-reply@navimumbaiweb.com>';
$headers = 'From: ' . $user_Email . '' . "\r\n" . //remove this line if line above this is un-commented
    'Reply-To: ' . $user_Email . '' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$to_Email = "manojmadhavanp@gmail.com";

$subject = "Navi Mumbai Web Verification Code";
$user_Message = "Verification Code: " . $otp;

$sentMail = mail($to_Email, $subject, $user_Message, $headers);

$retmsg = NULL;
if (!$sentMail) {
    $retmsg = '<p>Failed to send OTP</p>';
} else {
    $retmsg = '<p>OTP SENT</p>';
}