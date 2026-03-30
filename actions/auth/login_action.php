<?php
session_start();
require_once '../../config/db.php';
$mailConfig = require_once '../../config/mail.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// --- ACTION: SEND LOGIN OTP ---
if ($action === 'send_login_otp') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND is_active = 1");
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'No active account found with this email.']);
        exit;
    }

    $otp = rand(1000, 9999);
    $_SESSION['login_otp'] = $otp;
    $_SESSION['login_email'] = $email;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $mailConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailConfig['username'];
        $mail->Password = $mailConfig['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mailConfig['port'];

        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Your SAHARA Login OTP";
        $mail->Body = "Your login OTP is: <b>$otp</b>. Valid for 5 minutes.";

        $mail->send();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Email failed.']);
    }
}

// --- ACTION: VERIFY LOGIN OTP ---
elseif ($action === 'verify_login_otp') {
    $otp = $_POST['otp'] ?? '';
    if (isset($_SESSION['login_otp']) && $otp == $_SESSION['login_otp']) {
        $email = $_SESSION['login_email'];
        $user = $conn->query("SELECT id, name, email, phone, role FROM users WHERE email = '$email'")->fetch_assoc();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['role'] = $user['role'];

        unset($_SESSION['login_otp']);
        echo json_encode(['status' => 'success', 'role' => $user['role']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid OTP.']);
    }
}

// --- ACTION: PASSWORD LOGIN ---
elseif ($action === 'password_login') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'] ?? '';

    $result = $conn->query("SELECT * FROM users WHERE email = '$email' AND is_active = 1");
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Account not found.']);
        exit;
    }

    $user = $result->fetch_assoc();

    // Check if password exists (user might have only registered via OTP)
    if (empty($user['password'])) {
        echo json_encode(['status' => 'switch_to_otp', 'message' => 'Password not set for this account. Please login with OTP.']);
        exit;
    }

    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['role'] = $user['role'];

        echo json_encode(['status' => 'success', 'role' => $user['role']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect password.']);
    }
}