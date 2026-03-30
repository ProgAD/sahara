<?php
session_start();

// 1. Core Requirements
require_once '../../config/db.php';
$mailConfig = require_once '../../config/mail.php';
require_once '../../vendor/autoload.php'; // Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// --- ACTION 1: SEND OTP ---
if ($action === 'send_otp') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if user exists
    $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'This email is already registered.']);
        exit;
    }

    $otp = rand(1000, 9999);
    $_SESSION['temp_user'] = [
        'name' => $name, 
        'email' => $email, 
        'otp' => $otp,
        'verified' => false
    ];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $mailConfig['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $mailConfig['username'];
        $mail->Password   = $mailConfig['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $mailConfig['port'];

        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($email, $name);

        $mail->isHTML(true);
        $mail->Subject = "Verify your SAHARA Account ($otp)";

        // Branded Email Template
        $mail->Body = "
        <html>
        <body style='margin:0; padding:0; background-color:#FEF7ED; font-family: sans-serif;'>
            <div style='max-width:600px; margin:20px auto; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #eee;'>
                <div style='background:#292524; padding:40px; text-align:center;'>
                    <h1 style='color:#F97316; margin:0; letter-spacing:2px;'>SAHARA</h1>
                </div>
                <div style='padding:40px; text-align:center;'>
                    <h2 style='color:#292524;'>Verify Your Identity</h2>
                    <p style='color:#57534E;'>Hello $name, use the code below to complete your registration.</p>
                    <div style='margin:30px 0; padding:20px; border:3px solid #F97316; border-radius:15px; display:inline-block;'>
                        <span style='font-size:48px; font-weight:bold; letter-spacing:10px; color:#292524;'>$otp</span>
                    </div>
                    <p style='font-size:12px; color:#A8A29E;'>Valid for 5 minutes. Secure & Time-sensitive.</p>
                </div>
                <div style='background:#292524; padding:20px; text-align:center; color:#A8A29E; font-size:12px;'>
                    <p>SAHARA - The Social Welfare Society of IIT Madras BS Degree</p>
                </div>
            </div>
        </body>
        </html>";

        $mail->send();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Mail error: {$mail->ErrorInfo}"]);
    }
}

// --- ACTION 2: VERIFY OTP ---
elseif ($action === 'verify_otp') {
    $user_otp = $_POST['otp'] ?? '';
    $stored = $_SESSION['temp_user'] ?? null;

    if ($stored && $user_otp == $stored['otp']) {
        $_SESSION['temp_user']['verified'] = true;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect OTP code.']);
    }
}

// --- ACTION 3: SAVE PASSWORD & CREATE ACCOUNT ---
elseif ($action === 'save_password') {
    $password = $_POST['password'] ?? '';
    $stored = $_SESSION['temp_user'] ?? null;

    if (!$stored || !$stored['verified']) {
        echo json_encode(['status' => 'error', 'message' => 'Verification required.']);
        exit;
    }

    $name = mysqli_real_escape_string($conn, $stored['name']);
    $email = mysqli_real_escape_string($conn, $stored['email']);
    $hashed_pass = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (name, email, password, role, is_active) 
            VALUES ('$name', '$email', '$hashed_pass', 'user', 1)";
    
    if ($conn->query($sql)) {
        session_destroy(); // Clear registration session
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
}
?>