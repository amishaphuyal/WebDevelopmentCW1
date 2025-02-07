<?php
session_start();
include './includes/con.php'; // Database connection

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email exists in the users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate a unique reset token
            $token = bin2hex(random_bytes(32));

            // Store the reset token in the database
            $stmt = $conn->prepare("INSERT INTO password_reset_tokens (email, token) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $token);
            $stmt->execute();

            // Create a reset link
            $resetLink = "http://localhost/html/reset_password.php?token=$token";

            // Send email (ensure mail() is configured on your server)
            $to = $email;
            $subject = "Password Reset Request";
            $message = "Click the link below to reset your password:\n\n$resetLink\n\nIf you didn't request this, please ignore this email.";
            $headers = "From: noreply@frontlinehospital.com\r\n";

            if (mail($to, $subject, $message, $headers)) {
                $success = "A password reset link has been sent to your email.";
            } else {
                $error = "Failed to send the reset email. Please try again.";
            }
        } else {
            $error = "No account found with this email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .container h2 {
            color: #111827;
            margin-bottom: 1rem;
        }
        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            background-color: #2563eb;
            color: white;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        .message {
            margin-top: 1rem;
            font-size: 0.875rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
        }
        .error { background-color: #fef2f2; color: #ef4444; }
        .success { background-color: #ecfdf5; color: #22c55e; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <p>Enter your email to receive a password reset link.</p>

        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>
