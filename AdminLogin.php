<?php
  session_start();
  include("config.php");

  // Retrieve session data and reset
  $login_error = $_SESSION['login_error'] ?? null;
  $email_value = $_SESSION['email_value'] ?? '';
  $field_errors = $_SESSION['field_errors'] ?? [];

  unset($_SESSION['login_error']);
  unset($_SESSION['email_value']);
  unset($_SESSION['field_errors']);

  // Submit Function
  if (isset($_POST["submit"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM otp WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
      $row = mysqli_fetch_assoc($result);
      
      // Check if user has Admin role
      if ($row['role'] !== 'Admin') {
          $_SESSION['login_error'] = 'Access restricted to Admin users only.';
          $_SESSION['email_value'] = $email;
          $_SESSION['field_errors'] = ['email' => 'error'];
          header("Location: " . $_SERVER['PHP_SELF']);
          exit();
      }
      
      // Optional: block unverified users
      if ($row['status'] !== 'verified') {
          $_SESSION['login_error'] = 'Account not verified. Please report to Admin.';
          $_SESSION['email_value'] = $email;
          $_SESSION['field_errors'] = ['email' => 'error'];
          header("Location: " . $_SERVER['PHP_SELF']);
          exit();
      }

      // Securely check hashed password
      if (password_verify($password, $row["password"])) {
          $_SESSION["name"] = $row["name"];
          $_SESSION["email"] = $row["email"];
          $_SESSION["role"] = $row["role"]; // Store role in session
          header("Location: AdminDashboard.php");
          exit();
      } else {
          $_SESSION['login_error'] = 'Incorrect password. Please try again.';
          $_SESSION['email_value'] = $email;
          $_SESSION['field_errors'] = ['password' => 'error'];
          header("Location: " . $_SERVER['PHP_SELF']);
          exit();
      }
    } else {
      $_SESSION['login_error'] = 'Email not found. Please report to Admin.';
      $_SESSION['email_value'] = $email;
      $_SESSION['field_errors'] = ['email' => 'error'];
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
    <title>Admin Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="AFU_styles.css" />
    <style>
      .password-container {
        position: relative;
      }
      
      .toggle-password {
        position: absolute;
        right: 12px;
        top: 72%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #4a90e2;
        font-size: 1.25rem;
        z-index: 10;
      }

      .password-container input {
        padding-right: 2.5rem;
      }

      .input-box {
        position: relative;
      }

      .error-message {
        position: absolute;
        bottom: -1.25rem;
        left: 0;
        color: red;
        font-size: 0.875rem;
        display: block;
      }

      .login-container {
        display: flex;
        flex-direction: column;
        justify-content: center;
      }

      .form-header {
        text-align: center;
      }

      .login-form {
        max-width: 400px;
        margin: 0 auto;
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div class="w-full max-w-4xl mx-4 flex flex-col md:flex-row rounded-xl overflow-hidden shadow-lg mt-10">
      <!-- Left side - Logo -->
      <div class="w-full md:w-2/5 bg-blue-400 flex items-center justify-center p-6">
        <div class="text-center">
          <div class="logo-pulse mb-4">
            <img src="nobglogo.png" width="300" height="300" class="text-white mx-auto" />
          </div>

          <h1 class="text-3xl font-bold text-white mb-2">Admin</h1>
          <p class="text-blue-100 text-lg">Portal</p>

          <div class="mt-6 p-4 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
            <p class="text-blue-50 text-sm">
              "Modernizing the Art of <i>'Oops, I Forgot That!'</i>—bringing
              quick, clever solutions to life's little slip-ups."
            </p>
          </div>
        </div>
      </div>

      <!-- Right side - Login Form -->
      <div class="login-container w-full md:w-3/5 p-6">
        <form id="loginForm" class="login-form space-y-4" method="POST" action="">
          <div class="form-header mb-6">
            <h2 class="text-2xl font-bold text-blue-800">Welcome Back</h2>
            <p class="text-blue-600 mt-1 text-sm">
              Please sign in to your account
            </p>
          </div>

          <!-- Email Input -->
          <div class="input-box">
            <label for="email" class="block text-sm font-medium text-blue-700 mb-1">Email</label>
            <input
              type="email" id="email" name="email" value="<?php echo htmlspecialchars($email_value); ?>"
              class="form-input w-full px-3 py-2 border <?php echo !empty($field_errors['email']) ? 'border-red-500' : 'border-blue-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-blue-800 text-sm"
              placeholder="you@example.com" required/>

            <?php if (!empty($field_errors['email'])): ?>
              <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
          </div>

          <!-- Password Input -->
          <div class="input-box password-container">
            <label for="password" class="block text-sm font-medium text-blue-700 mb-1">Password</label>
            <input
              type="password" name="password" id="password"
              class="form-input w-full px-3 py-2 border <?php echo !empty($field_errors['password']) ? 'border-red-500' : 'border-blue-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-blue-800 text-sm"
              placeholder="Enter your password" required/>

            <i class='bx bx-hide toggle-password' id="togglePassword"></i>

            <?php if (!empty($field_errors['password'])): ?>
              <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
          </div>

          <div class="flex justify-end">
            <a href="ForgotPassword.php" class="text-xs font-medium text-blue-600 hover:text-blue-800">Forgot password?</a>
          </div>

          <button type="submit" name="submit" class="btn-primary w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Sign In
          </button>

          <a href="Home.php" class="mt-4 w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-blue-700 bg-white hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-400 text-center"> ← Back to Home </a>
        </form>
      </div>
    </div>
    <script>
      /* Toggle password visibility */
      const togglePassword = document.querySelector("#togglePassword");
      const passwordInput = document.querySelector("#password");

      if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function () {
          const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
          passwordInput.setAttribute("type", type);
          this.classList.toggle("bx-show");
          this.classList.toggle("bx-hide");
        });
      }
    </script>
  </body>
</html>
