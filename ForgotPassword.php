<?php
    session_start();
    include("config.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    // Load and clear success message
    $success_message = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);

    // Load previous email input and error
    $email_value = $_SESSION['email_value'] ?? '';
    $field_errors = $_SESSION['field_errors'] ?? [];
    unset($_SESSION['field_errors'], $_SESSION['email_value']);

    if (isset($_POST['submit'])) {
        $email = $_POST['email'];
        $_SESSION['email_value'] = $email;

        $sql = "SELECT * FROM otp WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $token = bin2hex(random_bytes(50));
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

            $sql = "INSERT INTO password_resets (email, token, expiry) VALUES ('$email', '$token', '$expiry')";
            mysqli_query($conn, $sql);

            $reset_link = "http://localhost/HM/ResetPassword.php?token=$token";
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = '2023302843@dhvsu.edu.ph';
                $mail->Password   = 'htqt mepk ywqz rscr';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('2023302843@dhvsu.edu.ph', 'HoLoFoMaEM');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $mail->Body    = "Click the link to reset your password: <a href='$reset_link'>$reset_link</a>";

                $mail->send();
                $_SESSION['success_message'] = "A password reset link has been sent to your email.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $_SESSION['field_errors'] = ['email' => 'error'];
            $_SESSION['login_error'] = 'Email not found.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Forgot Password</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" />
        <style>
            body {
                background: url(Homepage.png) no-repeat center center fixed;
                background-size: cover;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: 'Poppins', sans-serif;
            }

            .wrapper {
                width: 420px;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 30px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                transition: all 0.3s ease;
            }

            .wrapper:hover {
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
                transform: translateY(-5px);
            }

            .wrapper h1 {
                font-size: 28px;
                color: #333;
                text-align: center;
                margin-bottom: 20px;
                font-weight: 600;
            }

            .input-box {
                position: relative;
                width: 100%;
                margin-bottom: 15px;
            }

            .input-box input {
                width: 100%;
                height: 50px;
                background: rgba(255, 255, 255, 0.8);
                border: 2px solid rgba(0, 0, 0, 0.1);
                outline: none;
                border-radius: 30px;
                color: #333;
                padding: 0 20px;
                font-size: 16px;
                transition: all 0.3s ease;
            }

            .input-box input:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }

            .input-box i {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 18px;
                color: #777;
            }

            .btn {
                width: 100%;
                height: 45px;
                background: #3C91E6;
                border: none;
                border-radius: 30px;
                cursor: pointer;
                font-size: 16px;
                color: #fff;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn:hover {
                background: #2a7bc8;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(42, 123, 200, 0.4);
            }

            .back-to-login {
                text-align: center;
                margin-top: 20px;
            }

            .back-to-login a {
                color: #3C91E6;
                text-decoration: none;
                font-weight: 500;
            }

            .back-to-login a:hover {
                text-decoration: underline;
            }

            .error-message {
                color: #DB504A;
                background-color: #FFE0E0;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 15px;
                text-align: center;
                font-size: 14px;
            }

            .success-message {
                color: #4CAF50;
                background-color: #E8F5E9;
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 15px;
                text-align: center;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <form action="" method="POST">
                <h1>Forgot Password</h1>
                <p class="text-center text-blue-800 text-lg mb-4 font-semibold" style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                    Enter your email to receive a reset link.
                </p>

                <?php if (!empty($success_message)): ?>
                    <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <?php if (!empty($field_errors['email'])): ?>
                    <div class="error-message"><?php echo $_SESSION['login_error'] ?? 'Invalid input'; ?></div>
                <?php endif; ?>

                <div class="input-box">
                    <input type="email" name="email" placeholder="Enter your email" required value="<?php echo htmlspecialchars($email_value); ?>">
                    <i class='bx bx-envelope'></i>
                </div>

                <button type="submit" name="submit" class="btn">Send Reset Link</button>

                <div class="back-to-login">
                    <button class="back-button" onclick="goBack()">← Back to Login</button>
                </div>
            </form>
        </div>
        <script>
            function goBack() {
                window.history.back();
            }
        </script>
    </body>
</html>
