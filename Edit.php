<?php 
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sent_otp";

    $connection = new mysqli($servername, $username, $password, $dbname);

    $id= "";
    $name = "";
    $email = "";
    $phone = "";
    $shift = "";
    $role = "";

    $errorMessage = "";
    $successMessage = "";

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        // GET method: Show the data of the client
        if (!isset($_GET["id"])){
            header("location: AdminDashboard.php");
            exit;
        }

        $id = $_GET["id"];
        
        $sql = "SELECT * FROM otp WHERE id=$id";
        $result = $connection->query($sql);
        $row = $result->fetch_assoc();

        if(!$row){
            header("location: AdminDashboard.php");
            exit; 
        }

        $name = $row["name"];
        $email = $row["email"];
        $phone = $row["phone"];
        $shift = $row["shift"];
        $role = $row["role"];

    } else {
        //POST method: Update the data of the client
        $id = $_POST["id"];
        $name = $_POST["name"];
        $email = $_POST["email"];
        $phone = $_POST["phone"];
        $shift = $_POST["shift"];
        $role = $_POST["role"];

        do {

            if(empty($id) || empty($name) || empty($email) || empty($phone) || empty($shift) || empty($role)) {
                $errorMessage="All the fields are required";
                break;
            }

            $sql = "UPDATE otp SET name = '$name', email = '$email', phone = '$phone', shift = '$shift', role = '$role' WHERE id = $id";

            $result = $connection->query($sql);

            if(!$result){
                $errorMessage = "Invalid query: " . $connection->error;
                break;
            }

            $successMessage = "Employee Updated Correctly";

            header("location: AdminDashboard.php");
            exit;
        } while (false);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee's Information Update</title>
        <link rel="stylesheet" href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                min-height: 100vh;
                position: relative;
                overflow: scroll; 
            }
            
            .wrapper {
                width: 420px;
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 20px;
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
                margin-bottom: 8px;
            }
            
            .input-box label {
                display: block;
                margin-bottom: 2px;
                color: #333;
                font-weight: 500;
                padding-left: 15px;
            }
            
            .input-box input,
            .input-box select {
                width: 100%;
                height: 50px;
                background: rgba(255, 255, 255, 0.8);
                border: 2px solid rgba(0, 0, 0, 0.1);
                outline: none;
                border-radius: 30px;
                color: #333;
                padding: 0 20px;
                font-size: 16px;
                transition: all 0.3s;

                /* Remove default arrow */
                appearance: none; 

                /* For Safari */
                -webkit-appearance: none; 

                /* For Firefox */
                -moz-appearance: none; 
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23777'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;

                /* Adjust this value to move the arrow */
                background-position: right 15px center; 

                /* Add space for the arrow */
                padding-right: 40px; 
            }

            /* Adjust the icon position for select elements */
            .input-box select + i {
                /* Move the icon slightly to the left (from 20px to 15px) */
                right: 15px; 
            }
            
            .input-box input:focus,
            .input-box select:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }
            
            .input-box i {
                position: absolute;
                right: 20px;
                top: 55px;
                transform: translateY(-50%);
                font-size: 18px;
                color: #777;
            }
            
            .button-group {
                display: flex;
                gap: 15px;
                margin-top: 15px;

                /* This centers the buttons horizontally */
                justify-content: center; 

                /* This centers the buttons vertically */
                align-items: center; 

                /* Ensure it takes full width */
                width: 100%; 
            }
            
            .btn {
                height: 45px;
                border: none;
                outline: none;
                border-radius: 30px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                transition: all 0.3s;

                /* Added padding for better button proportions */
                padding: 0 25px; 

                /* Center text horizontally */
                text-align: center; 

                /* Enable flexbox for the button */
                display: flex; 

                /* Center content vertically */
                align-items: center; 

                /* Center content horizontally */
                justify-content: center; 
            }

            /* Keep all other button styles the same */
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

                /* Remove underline from link */
                text-decoration: none; 
            }
            
            .btn-cancel:hover {
                background: rgba(60, 145, 230, 0.1);
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(42, 123, 200, 0.2);
            }

            /* Remove dropdown arrow from regular input fields */
            .no-arrow {
                /* Remove any background image */
                background-image: none !important; 

                /* Reset padding to normal */
                padding-right: 20px !important; 
            }

            /* For number input specifically - removes spinner in some browsers */
            input[type="number"]::-webkit-inner-spin-button,
            input[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            /* Keep the arrow for select elements */
            .input-box select {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='%23777'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 15px center;
                padding-right: 40px;
            }

            /* Back button styles */
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

            .error-message {
                color: #DB504A;
                font-size: 12px;
                margin-top: 5px;
                padding-left: 15px;
                display: none;
            }

            .input-box input.error,
            .input-box select.error {
                border-color: #DB504A;
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
            <h2>Edit Employee</h2>
            <form method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <div class="input-box">
                    <label for="name">Name</label>
                    <input type="text" id="name" class="form-control no-arrow" name="name" value="<?php echo $name; ?>">
                    <i class='bx bx-user'></i>
                </div>

                <div class="input-box">
                    <label for="email">Email</label>
                    <input type="text" id="email" class="form-control no-arrow" name="email" value="<?php echo $email; ?>">
                    <i class='bx bx-envelope'></i>
                </div>

                <div class="input-box">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" class="form-control no-arrow" name="phone" value="<?php echo $phone; ?>">
                    <i class='bx bx-phone'></i>
                </div>

                <div class="input-box">
                    <label for="shift">Shift</label>
                    <select id="shift" name="shift" required>
                        <option value="" disabled>Select Shift</option>
                        <option value="Morning" <?php echo ($shift == 'Morning') ? 'selected' : ''; ?>>Morning</option>
                        <option value="Afternoon" <?php echo ($shift == 'Afternoon') ? 'selected' : ''; ?>>Afternoon</option>
                        <option value="Night" <?php echo ($shift == 'Night') ? 'selected' : ''; ?>>Night</option>
                    </select>
                </div>

                <div class="input-box">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="" disabled>Select Role</option>
                        <option value="Frontdesk" <?php echo ($role == 'Frontdesk') ? 'selected' : ''; ?>>Frontdesk</option>
                        <option value="Utility" <?php echo ($role == 'Utility') ? 'selected' : ''; ?>>Utility</option>
                        <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-submit">Update Employee</button>
                    <a class="btn btn-cancel" href="AdminDashboard.php" style="display: flex; justify-content: center; align-items: center;">Cancel</a>
                </div>
            </form>
        </div>
    </body>
</html>