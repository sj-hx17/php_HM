<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sign Up Form</title>
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></link>   
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
            
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Poppins', sans-serif;
            }
            
            body {
                background: url(Homepage.png); 
                background-size: cover; 
                background-position: center; 
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                position: relative;
                font-family: Arial, sans-serif;
                overflow: scroll; 
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
            
            .container {
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .container {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 10px;
                padding: 30px;
                width: 300px;
                text-align: center;
            }

            .wrapper:hover {
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
                transform: translateY(-5px);
            }
            
            .wrapper h1 {
                font-size: 32px;
                color: #333;
                text-align: center;
                margin-bottom: 30px;
                font-weight: 600;
            }
            
            .input-box {
                position: relative;
                width: 100%;
                height: 50px;
                margin: 10px 0;
            }
            
            .input-box input {
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                border: 2px solid rgba(0, 0, 0, 0.1);
                outline: none;
                border-radius: 30px;
                color: #333;
                padding: 20px 45px 20px 20px;
                font-size: 16px;
                transition: all 0.3s;
            }
            
            .input-box input:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }
            .input-box input::placeholder {
                color: #999;
            }
            
            .input-box i {
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 18px;
                color: #777;
            }
            
            .password-container {
                position: relative;
            }
            
            .toggle-password {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #777;
                z-index: 2;
                font-size: 18px;
            }
            
            .btn {
                width: 100%;
                height: 45px;
                background: #3C91E6;
                border: none;
                outline: none;
                border-radius: 30px;
                cursor: pointer;
                font-size: 16px;
                color: #fff;
                font-weight: 600;
                margin-top: 20px;
                transition: all 0.3s;
            }
            
            .btn:hover {
                background: #2a7bc8;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(42, 123, 200, 0.4);
            }
            
            .login-link {
                text-align: center;
                margin-top: 20px;
                color: #666;
                font-size: 14px;
            }
            
            .login-link a {
                color: #3C91E6;
                text-decoration: none;
                font-weight: 500;
                transition: all 0.3s;
            }
            
            .login-link a:hover {
                text-decoration: underline;
                color: #2a7bc8;
            }

            /* Back button styles */
            .back-button {
                position: absolute;
                bottom: 20px;
                right: 20px;
                background: rgba(255, 255, 255, 0.8);
                border: none;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                transition: all 0.3s;
            }

            .back-button:hover {
                background: #3C91E6;
                color: white;
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(60, 145, 230, 0.3);
            }

            .back-button i {
                font-size: 20px;
            }

            .shift-select {
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                border: 2px solid rgba(0, 0, 0, 0.1);
                outline: none;
                border-radius: 30px;
                color: #333;
                padding: 0 45px 0 20px;
                font-size: 16px;
                transition: all 0.3s;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                cursor: pointer;
            }

            .shift-select:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }

            .shift-select option {
                padding: 10px;
                background: white;
                color: #333;
            }

            .back-button {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: transparent;
                border: 2px solid rgba(255, 255, 255, 0.8);
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                transition: all 0.3s;
                color: rgba(255, 255, 255, 0.8);
                text-decoration: none;
            }

            .back-button:hover {
                background: rgba(255, 255, 255, 0.2);
                border-color: white;
                color: white;
                transform: translateY(-3px) scale(1.1);
            }

            .back-button i {
                font-size: 24px;
            }

            .role-select {
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                border: 2px solid rgba(0, 0, 0, 0.1);
                outline: none;
                border-radius: 30px;
                color: #333;
                padding: 0 45px 0 20px;
                font-size: 16px;
                transition: all 0.3s;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                cursor: pointer;
            }

            .role-select:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }

            .role-select option {
                padding: 10px;
                background: white;
                color: #333;
            }
            
            @media (max-width: 500px) {
                .wrapper {
                    width: 90%;
                    padding: 30px;
                }
            }
            .error-message {
                color: #DB504A;
                font-size: 12px;
                margin-top: 5px;
                display: none;
            }
            .input-box input.error {
                border-color: #DB504A;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <form action="send.php" method="POST">
                <h1>Register Employee</h1>
                
                <div class="input-box">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <i class='bx bx-user'></i>
                </div>
                
                <div class="input-box">
                    <input type="number" name="phone" placeholder="Phone Number" required>
                    <i class='bx bx-phone'></i>
                    <div class="error-message" id="phoneError"></div>
                </div>
                
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bx-envelope'></i>
                    <div class="error-message" id="emailError"></div>
                </div>
                
                <div class="input-box password-container">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <i class='bx bx-hide toggle-password' id="togglePassword"></i>
                </div>

                <div class="input-box">
                    <select name="shift" class="shift-select" required>
                        <option value="" disabled selected>Select Shift</option>
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Night">Night</option>
                    </select>

                    <i class='bx bx-time'></i>
                    <div class="error-message" id="shiftError"></div>
                </div>

                <div class="input-box">
                    <select name="role" class="role-select" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="Frontdesk">Frontdesk</option>
                        <option value="Utility">Utility</option>
                        <option value="Admin">Admin</option>
                    </select>

                    <i class='bx bx-user-circle'></i>
                    <div class="error-message" id="roleError"></div>
                </div>
                
                <input type="hidden" name="otp" id="otp">
                <input type="hidden" name="subject" value="Received OTP">
                
                <button type="submit" name="send" class="btn">Add Profile</button>
            
            </form>
        </div>

        <a href="AdminDashboard.php" class="back-button">
            <i class='bx bx-chevron-left'></i>
        </a>
        <script>
            // Show Password
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('bx-show');
                this.classList.toggle('bx-hide');
            });

            // Generate OTP
            function generateRandomNumber() {
                let min = 100000;
                let max = 999999;
                let randomNumber = Math.floor(Math.random() * (max - min + 1)) + min;
                return randomNumber;
            }
            
            document.getElementById('otp').value = generateRandomNumber();

            document.querySelector('form').addEventListener('submit', function(e) {
                const email = document.querySelector('input[name="email"]').value;
                const phone = document.querySelector('input[name="phone"]').value;
                let isValid = true;

                // Email Structure Validation
                if (!email.includes('@')) {
                    document.getElementById('emailError').textContent = 'Invalid email format';
                    document.getElementById('emailError').style.display = 'block';
                    document.querySelector('input[name="email"]').classList.add('error');
                    isValid = false;
                }

                // Phone Structure Validation
                if (phone.length < 10) {
                    document.getElementById('phoneError').textContent = 'Phone number too short';
                    document.getElementById('phoneError').style.display = 'block';
                    document.querySelector('input[name="phone"]').classList.add('error');
                    isValid = false;
                }


                // Shift Validation
                if (!shift) {
                    document.getElementById('shiftError').textContent = 'Please select a shift';
                    document.getElementById('shiftError').style.display = 'block';
                    document.querySelector('select[name="shift"]').classList.add('error');
                    isValid = false;
                }

                // Role Validation
                if (!role) {
                    document.getElementById('roleError').textContent = 'Please select a role';
                    document.getElementById('roleError').style.display = 'block';
                    document.querySelector('select[name="role"]').classList.add('error');
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        </script>
    </body>
</html>