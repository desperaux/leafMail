<?php
// Include Composer's autoload
require 'vendor/autoload.php';

use Leaf\Mail;

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize inputs
    $host = filter_var($_POST['host'], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $port = filter_var($_POST['port'], FILTER_SANITIZE_NUMBER_INT);
    $encryption = filter_var($_POST['encryption'], FILTER_SANITIZE_STRING);
    $from = filter_var($_POST['from'], FILTER_SANITIZE_EMAIL);
    $fromName = filter_var($_POST['fromName'], FILTER_SANITIZE_STRING);
    $to = filter_var($_POST['to'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $messageText = $_POST['messageText']; // Plain text
    $messageHtml = $_POST['messageHtml']; // HTML content

    // Configure Leaf Mail with form data
    Mail::config([
        'host' => $host,
        'username' => $username,
        'password' => $password,
        'port' => $port,
        'encryption' => $encryption,
        'from' => $from,
        'fromName' => $fromName,
    ]);

    // Add attachments
    $attachments = [];
    if (!empty($_FILES['attachments']['name'][0])) {
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmpName) {
            $fileName = $_FILES['attachments']['name'][$key];
            $filePath = $_FILES['attachments']['tmp_name'][$key];
            $attachments[] = [
                'name' => $fileName,
                'path' => $filePath,
            ];
        }
    }

    // Send the email as both HTML and plain text with attachments
    try {
        if (Mail::send($to, $subject, [
            'html' => $messageHtml,
            'text' => $messageText
        ], true, $attachments)) {
            echo "<p class='success'>Email sent successfully!</p>";
        } else {
            echo "<p class='error'>Failed to send email. Please check your configuration and try again.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
