<?php
    session_start();

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sent_otp";

    $connect = new mysqli($servername, $username, $password, $dbname);
    $email = "";
    $stored_otp = "";
    $otp_send_time = "";
    $message = "";

    $ip_address = $_SERVER['REMOTE_ADDR'];
    $sql = "SELECT email, otp, otp_send_time FROM otp WHERE ip = ? AND status = 'pending' ORDER BY otp_send_time DESC LIMIT 1";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param("s", $ip_address);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $stored_otp = $row['otp'];
        $otp_send_time = $row['otp_send_time'];
    } else {
        $message = "No pending OTP found for this device.";
    }

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['verify'])) {
        $entered_otp = trim($_POST['otp']);
        $current_time = time();
        $otp_time = strtotime($otp_send_time);
        $expiry_time = 5 * 60; // 5 minutes

        if (empty($stored_otp) || empty($otp_send_time)) {
            $message = "OTP data is missing. Please request a new OTP.";
        } elseif (($current_time - $otp_time) > $expiry_time) {
            $message = "OTP has expired. Please request a new one.";
        } elseif ($entered_otp === $stored_otp) {
            $update_sql = "UPDATE otp SET status = 'verified' WHERE email = ? AND ip = ?";
            $update_stmt = $connect->prepare($update_sql);
            $update_stmt->bind_param("ss", $email, $ip_address);
            if ($update_stmt->execute()) {
                $_SESSION['email_verified'] = true;
                $_SESSION['user_email'] = $email;
                header("Location: AdminDashboard.php");
                exit();
            } else {
                $message = "Error updating OTP status.";
            }
        } else {
            $message = "Invalid OTP. Please try again.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>OTP Verification</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            :root {
                --primary-color: #4e73df;
                --secondary-color: #f8f9fc;
                --success-color: #1cc88a;
                --danger-color: #e74a3b;
                --warning-color: #f6c23e;
                --info-color: #36b9cc;
            }

            body {
                background: url(Homepage.png);
                background-size: cover;
                background-position: center;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                font-family: Arial, sans-serif;
                overflow: scroll;
            }

            .otp-container {
                width: 420px;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 30px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                text-align: center;
            }

            .otp-header {
                margin-bottom: 2rem;
                color: var(--primary-color);
            }

            .otp-header h3 {
                font-weight: 700;
            }

            .otp-header p {
                color: #6c757d;
                font-size: 0.9rem;
            }

            .otp-icon {
                font-size: 2.5rem;
                margin-bottom: 1.5rem;
                color: var(--primary-color);
            }

            .input-group-text {
                background-color: var(--secondary-color);
                border-right: none;
            }

            .form-control {
                border-left: none;
                padding: 12px 15px;
            }

            .form-control:focus {
                box-shadow: none;
                border-color: #ced4da;
            }

            .btn-verify {
                background-color: var(--primary-color);
                border: none;
                padding: 12px;
                font-weight: 600;
            }

            .btn-verify:hover {
                background-color: #3a5ccc;
                transform: translateY(-2px);
            }

            .alert {
                border-radius: 8px;
                padding: 12px 15px;
                margin-bottom: 1.5rem;
            }

            .resend-link {
                margin-top: 1.5rem;
                display: block;
                color: var(--primary-color);
                text-decoration: none;
                font-size: 0.9rem;
            }

            .resend-link:hover {
                text-decoration: underline;
            }

            .countdown {
                color: #6c757d;
                font-size: 0.85rem;
                margin-top: 0.5rem;
            }
        </style>
    </head>
    <body>
    <div class="otp-container">
        <div class="otp-header">
            <div class="otp-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3>OTP Verification</h3>
            <p>Enter the 6-digit code sent to your email</p>
        </div>

        <?php if ($email): ?>
            <div class="alert alert-info" role="alert">
                <strong>OTP sent to:</strong> <?php echo htmlspecialchars($email); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-key"></i></span>
                <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6" required>
            </div>
            <div class="countdown" id="countdown">Code expires in 04:59</div>

            <button type="submit" name="verify" class="btn btn-primary btn-verify w-100 mt-3">
                Verify OTP <i class="fas fa-arrow-right ms-2"></i>
            </button>

        </form>

        <?php if ($message): ?>
            <div class="alert alert-warning mt-3" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
    </div>
        <script>
            const otpSendTime = <?php echo json_encode($otp_send_time); ?>;
            const expiryTime = 5 * 60 * 1000;
            let timePassed = new Date().getTime() - new Date(otpSendTime).getTime();
            let timeLeft = expiryTime - timePassed;

            const countdownElement = document.getElementById('countdown');
            const resendLink = document.getElementById('resend-link');
            let timer;

            function updateCountdown() {
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    countdownElement.textContent = "Code expired";
                    countdownElement.style.color = "var(--danger-color)";
                    return;
                }

                const minutes = Math.floor(timeLeft / 60000);
                const seconds = Math.floor((timeLeft % 60000) / 1000);
                countdownElement.textContent = `Code expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
                timeLeft -= 1000;
            }

            updateCountdown();
            timer = setInterval(updateCountdown, 1000);

            resendLink.addEventListener('click', function (e) {
                e.preventDefault();
                alert('A new OTP has been sent to your email!');
                timeLeft = expiryTime;
                clearInterval(timer);
                updateCountdown();
                timer = setInterval(updateCountdown, 1000);
            });
        </script>
    </body>
</html>