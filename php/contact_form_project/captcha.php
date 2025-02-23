<?php
ob_start();
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', './logs/php_errors.log');
// if post is not empty array then:
// if (!empty($_POST)) {
//     file_put_contents('./logs/post_data.log', print_r($_POST, true), FILE_APPEND);
// }

// Generate a random math problem
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$operator = '+';
// $captchaChallenge = "$num1 $operator $num2 = ?";
$captchaText = "$num1 $operator $num2 = ?";
$newCaptchaSolution = $num1 + $num2;
// Create a new image
$image = imagecreate(200, 50);
// Set the background color
$background_color = imagecolorallocate($image, 255, 255, 255);
// Set the text color
$text_color = imagecolorallocate($image, 0, 0, 0);
// Write the captcha challenge to the image
imagestring($image, 5, 10, 15, $captchaText, $text_color);
imagepng($image);
$image_data = ob_get_contents();
ob_end_clean();
// Encode the image data as base64
$captchaChallenge = base64_encode($image_data);
// Clean up
imagedestroy($image);
// Get the previously stored CAPTCHA solution before setting a new one
$oldCaptchaSolution = $_SESSION['captchaSolution'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['captchaAnswer'])) {
        // file_put_contents('./logs/captcha_response.log', json_encode($_POST['captchaAnswer']) . PHP_EOL, FILE_APPEND); // log to test responses
        if ((int) $_POST['captchaAnswer'] === $oldCaptchaSolution) {
            $response = [
                'status' => 'success',
                'message' => 'Captcha cevabi dogru.'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Captcha cevabi yanlis.'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Captcha cevabi gonderilmedi. Hata: ' . $e->getMessage(),
            'captchaChallenge' => $captchaChallenge
        ];
    }
} else {
    $response = [
        'status' => 'info',
        'message' => 'captcha kodu uretildi',
        'captchaChallenge' => $captchaChallenge
    ];
}

// Add the old CAPTCHA solution to the response
$response['captchaSolution'] = $oldCaptchaSolution;
$response['response_from'] = "captcha";

// to test - Add new CAPTCHA solution to the session
// $response['newCaptchaSolution'] = $newCaptchaSolution;

// Store the new CAPTCHA solution for the next request
$_SESSION['captchaSolution'] = $newCaptchaSolution;
$_SESSION['oldCaptchaSolution'] = $oldCaptchaSolution;

// file_put_contents('./logs/json_response.log', json_encode($response) . PHP_EOL, FILE_APPEND); // Log the JSON response
echo json_encode($response); // Send the JSON response
exit; // Stop further script execution