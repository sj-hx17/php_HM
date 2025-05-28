<?php
    session_start();
    include("config.php");

    $token = $_GET['token'] ?? '';
    $success_message = '';
    $error_message = '';

    if (isset($_POST['submit'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $sql = "SELECT * FROM password_resets WHERE token='$token' AND expiry > NOW()";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            if (strlen($new_password) < 8) {
                $error_message = "Password must be at least 8 characters long.";
            } elseif ($new_password !== $confirm_password) {
                $error_message = "Passwords do not match.";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $row = mysqli_fetch_assoc($result);
                $email = $row['email'];

                $sql = "UPDATE otp SET password='$hashed_password' WHERE email='$email'";
                mysqli_query($conn, $sql);

                $sql = "DELETE FROM password_resets WHERE token='$token'";
                mysqli_query($conn, $sql);

                $success_message = "Your password has been reset successfully. Redirecting to login...";
            }
        } else {
            $error_message = "Invalid or expired token.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Reset Password</title>
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

            * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

            body {
                background: url(Homepage.png);
                background-size: cover;
                background-position: center;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
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

            .wrapper h2 {
                font-size: 28px;
                color: #333;
                text-align: center;
                margin-bottom: 10px;
                font-weight: 600;
            }

            .input-box {
                position: relative;
                width: 100%;
                margin-bottom: 15px;
            }

            .input-box label {
                display: block;
                margin-bottom: 2px;
                color: #333;
                font-weight: 500;
                padding-left: 15px;
            }

            .input-box input {
                width: 100%;
                height: 50px;
                background: rgba(255, 255, 255, 0.8);
                border: 2px solid rgba(0, 0, 0, 0.1);
                outline: none;
                border-radius: 30px;
                color: #333;
                padding: 0 45px 0 20px;
                font-size: 16px;
                transition: all 0.3s;
            }

            .input-box input:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }
            
            .input-box i.toggle-password {
                position: absolute;
                right: 15px; 
                top: 70%;
                transform: translateY(-50%); 
                cursor: pointer;
                color: #555;
            }

            .button-group {
                display: flex;
                gap: 15px;
                margin-top: 15px;
                justify-content: center;
            }

            .btn {
                height: 45px;
                border: none;
                outline: none;
                border-radius: 30px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                padding: 0 25px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-submit {
                background: #3C91E6;
                color: #fff;
            }

            .btn-submit:hover {
                background: #2a7bc8;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(42, 123, 200, 0.4);
            }

            .btn-cancel {
                background: rgba(255, 255, 255, 0.8);
                color: #3C91E6;
                border: 2px solid #3C91E6;
                text-decoration: none;
            }

            .btn-cancel:hover {
                background: rgba(60, 145, 230, 0.1);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(42, 123, 200, 0.2);
            }

            .success-message, .error-message {
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 15px;
                text-align: center;
                font-size: 14px;
            }

            .success-message {
                color: #4CAF50;
                background-color: #E8F5E9;
            }

            .error-message {
                color: #DB504A;
                background-color: #FFE0E0;
            }

            

            @media (max-width: 500px) {
                .wrapper {
                    width: 90%;
                    padding: 25px;
                }

                .button-group {
                    flex-direction: column;
                    gap: 10px;
                }
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <h2>Reset Password</h2>

            <?php if (!empty($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-box">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required minlength="8" />
                    <i class='bx bx-hide toggle-password' onclick="togglePassword('new_password', this)"></i>
                </div>

                <div class="input-box">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required />
                    <i class='bx bx-hide toggle-password' onclick="togglePassword('confirm_password', this)"></i>
                </div>

                <div class="button-group">
                    <button type="submit" name="submit" class="btn btn-submit">Reset Password</button>
                </div>
            </form>
        </div>
        <?php if (!empty($success_message)): ?>
        <script>
            setTimeout(() => {
                window.location.href = 'Home.php';
            }, 3000);
        </script>
        <?php endif; ?>
        <script>
            function togglePassword(fieldId, icon) {
                const input = document.getElementById(fieldId);
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                icon.classList.toggle('bx-show');
                icon.classList.toggle('bx-hide');
            }
        </script>
    </body>
</html>