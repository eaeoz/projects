<?php
// header('Content-Type: text/html; charset=utf-8');
ob_start(); // Start output buffering
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './logs/php_errors.log');
file_put_contents('./logs/post_data.log', print_r($_POST, true), FILE_APPEND);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validate email
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gecersiz e-posta adresi.'
    ]);
    exit; // Stop further execution
}
$subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

$response = []; // Initialize $response

$mail = new PHPMailer(true);
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->CharSet = 'UTF-8';
$mail->Encoding = 'base64';
$mail->isSMTP();
$mail->Host = 'smtp.yandex.com';
$mail->SMTPAuth = true;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
$mail->Username = 'sedatergoz@yandex.com';
$mail->Password = 'yandex_app_password';
$mail->setFrom('sedatergoz@yandex.com', 'Sedat');
$mail->addAddress($email);
$mail->isHTML(true);
$mail->Subject = 'Iletisim Formu';
$mail->msgHTML('
    <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f2f2f2;
                    color: #333;
                    padding-top: 20px;
                    padding-bottom: 20px;
                }
                h1 {
                    color: #4CAF50;
                    text-align: center;
                }
                .kutu {
                    background-color: #fff;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    width: 80%;
                    margin: 40px auto;
                }
                .kutu p {
                    margin-bottom: 10px;
                    text-align: left;
                }
                .kutu span {
                    font-weight: bold;
                    color: #FF0000;
                    text-align: left;
                    background-color: #f2f2f2;
                    block-size: 30px;
                }
            </style>
        </head>
        <body>
            <h1>Gelen Veriler</h1>
            <div class="kutu">
                <p><span>Ad Soyad:</span> ' . $name . '</p>
                <p><span>Telefon Numarası:</span> ' . $phone . '</p>
                <p><span>Email Adresi:</span> ' . $email . '</p>
                <p><span>Konu:</span> ' . $subject . '</p>
                <p><span>Mesaj:</span> ' . $message . '</p>
            </div>
        </body>
    </html>
');

$mail->Debugoutput = function ($str, $level) {
    if ($level > 2) return;
    file_put_contents('./logs/phpmailer/logs.txt', date('Y-m-d H:i:s') . " [{$level}] {$str}\n", FILE_APPEND);
    if (!str_contains($str, 'Error') && !str_contains($str, 'Failed')) {
        return;
    }
    file_put_contents('./logs/phpmailer/errors.txt', date('Y-m-d H:i:s') . " {$str}\n", FILE_APPEND);
};

// file_put_contents('./logs/captcha_response.log', "POST Data: " . $_POST['captchaAnswer'] . " - " . "Session Data(actual we use for confirmation): " . $_SESSION['oldCaptchaSolution'] . " - " . $_SESSION['captchaSolution'] . PHP_EOL, FILE_APPEND); // log to test responses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['captchaAnswer']) && ((int) $_SESSION['oldCaptchaSolution'] == $_POST['captchaAnswer'])) {
        try {
            if ($mail->send()) {
                $response = [
                    'status' => 'success',
                    'message' => 'Mail gonderimi basarili.'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Mail gonderilemedi. Hata: ' . $mail->ErrorInfo
                ];
            }
        } catch (Exception $e) {
            $response = [
                'status' => 'error',
                'message' => 'Mail gonderilemedi. Hata: ' . $e->getMessage()
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Captcha yanlis.'
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'Gonderilen veride captcha dogrulanamadi.'
    ];
}

$response['response_from'] = "iletisim";
// file_put_contents('./logs/json_response.log', json_encode($response) . PHP_EOL, FILE_APPEND); // Log the JSON response
echo json_encode($response); // Send the JSON response
exit; // Stop further script execution

ob_end_clean(); // Clean the output buffer