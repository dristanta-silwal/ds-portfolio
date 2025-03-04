<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

require '../vendor/autoload.php';

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Get SMTP credentials from .env
$smtp_host = $_ENV['SMTP_HOST'];
$smtp_user = $_ENV['SMTP_USER'];
$smtp_pass = $_ENV['SMTP_PASS'];
$smtp_port = $_ENV['SMTP_PORT'];
$sender_email = $_ENV['SMTP_USER']; // Your email (from which emails are sent)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and validate form inputs
  $name = htmlspecialchars($_POST['name']);
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL); // User email (recipient)
  $subject = htmlspecialchars($_POST['subject']);
  $message = nl2br(htmlspecialchars($_POST['message']));

  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email format.');
  }

  // Initialize PHPMailer
  $mail = new PHPMailer(true);

  try {
    // SMTP Settings
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->SMTPAuth = true;
    $mail->Username = $smtp_user;
    $mail->Password = $smtp_pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtp_port;

    // Sender and recipient
    $mail->setFrom($sender_email, "Dristanta Silwal");
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = "Message Received!";
    $mail->Body = "
            <h2>Thank You for Reaching Out!</h2>
            <p>Dear {$name},</p>
            <p>I've received your message:</p>
            <blockquote>{$message}</blockquote>
            <p>I'll get back to you soon!</p>
            <p>Best regards,<br>Dristanta Silwal</p>
        ";

    // Send email
    // Send email
    if ($mail->send()) {
      header('Content-Type: application/json');
      echo json_encode(["status" => "success", "message" => "Message sent successfully"]);
    } else {
      header('Content-Type: application/json');
      echo json_encode(["status" => "error", "message" => "There was an error sending your message."]);
    }
  } catch (Exception $e) {
    echo "Message could not be sent.";
  }
} else {
  echo 'Invalid request!';
}
