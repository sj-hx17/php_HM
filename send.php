<?php
    session_start();
    include("config.php");

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    // DB connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sent_otp";
    $connect = new mysqli($servername, $username, $password, $dbname);

    if (isset($_POST['send'])) {
        // Collect form data
        $name     = $_POST['name'];
        $email    = $_POST['email'];
        $phone    = trim(preg_replace('/[^0-9]/', '', $_POST['phone']));
        $shift    = $_POST['shift'];
        $role     = $_POST['role'];
        $password = trim($_POST["password"]);
        $otp      = $_POST['otp'];
        $ip       = $_SERVER['REMOTE_ADDR'];

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Validation
        if (strlen($password) < 8) {
            echo "<script>alert('Password must be at least 8 characters long.'); window.history.back();</script>";
            exit();
        }

        if (strlen($phone) != 11) {
            echo "<script>alert('Phone number must be exactly 11 digits.'); window.history.back();</script>";
            exit();
        }

        // Check for duplicate email
        $emailCheck = $connect->prepare("SELECT id FROM otp WHERE email = ?");
        $emailCheck->bind_param("s", $email);
        $emailCheck->execute();
        $emailCheck->store_result();

        if ($emailCheck->num_rows > 0) {
            echo "<script>alert('This email is already registered.'); window.history.back();</script>";
            exit();
        }

        // Check for duplicate phone
        $phoneCheck = $connect->prepare("SELECT id FROM otp WHERE phone = ?");
        $phoneCheck->bind_param("s", $phone);
        $phoneCheck->execute();
        $phoneCheck->store_result();

        if ($phoneCheck->num_rows > 0) {
            echo "<script>alert('This phone number is already registered.'); window.history.back();</script>";
            exit();
        }

        // Insert user using prepared statement
        $stmt = $connect->prepare("INSERT INTO otp (name, email, phone, shift, role, password, otp, status, otp_send_time, ip) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)");
        $stmt->bind_param("ssssssss", $name, $email, $phone, $shift, $role, $hashedPassword, $otp, $ip);

        if ($stmt->execute()) {
            // Send email
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = '2023302843@dhvsu.edu.ph';  
                $mail->Password = 'htqt mepk ywqz rscr';   
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('2023302843@dhvsu.edu.ph', 'HoLoFoMaEM');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = $_POST['subject'];
                $mail->Body = "Your OTP verification code is: <strong>" . htmlspecialchars($otp) . "</strong>";

                $mail->send();

                echo "<script>alert('Verification code has been sent to your email.'); window.location.href='verify.php';</script>";
                exit();
            } catch (Exception $e) {
                echo "<script>alert('Email failed to send: {$mail->ErrorInfo}'); window.location.href='signup.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Failed to add profile. Please try again.'); window.location.href='signup.php';</script>";
            exit();
        }

        // Close statements
        $stmt->close();
        $emailCheck->close();
        $phoneCheck->close();
        $connect->close();
    }
?>