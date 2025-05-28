<?php
    session_start();

    // Include your database connection file
    include("config.php"); 
    $id = $_GET['id'] ?? '';
    $room = null;
    $errorMessage = "";
    $successMessage = "";

    // Fetch room data if ID is provided
    if ($id) {
        $sql = "SELECT * FROM room WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
    }

    // Handle form submission to update room details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $roomno = $_POST['roomno'];
        $access = $_POST['access'];
        $name = $_POST['name'];

        // Update the room details in the database
        $update_sql = "UPDATE room SET name = ?, roomno = ?, access = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $roomno, $access, $id);
        
        if ($update_stmt->execute()) {
            $successMessage = "Room Updated Successfully";

            // Redirect back to the dashboard
            header("Location: FrontdeskDashboard.php"); 
            exit();
        } else {
            $errorMessage = "Error updating record: " . $update_stmt->error;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Room's Information Update</title>
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
            }
            
            .input-box input:focus,
            .input-box select:focus {
                border-color: #3C91E6;
                box-shadow: 0 0 10px rgba(60, 145, 230, 0.3);
            }
            
            .input-box input::placeholder {
                color: #999;
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
            <h1>Edit Room Information</h1>
            <?php if ($room): ?>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $room['id']; ?>">
                    <div class="input-box">
                        <label for="name">Guest Information</label>
                        <input type="text" id="name" class="form-control" name="name" value="<?php echo htmlspecialchars($room['name']); ?>" required>
                        <i class='bx bx-user'></i>
                    </div>

                    <div class="input-box">
                        <label for="roomno">Room Details</label>
                        <input type="text" id="roomno" name="roomno" value="<?php echo $room['roomno']; ?>" required>
                        <i class='bx bx-home'></i>
                    </div>
                    
                    <div class="input-box">
                        <label for="access">Access</label>
                        <select id="access" name="access" required>
                            <option value="Occupied" <?php echo ($room['access'] == 'Occupied') ? 'selected' : ''; ?>>Occupied</option>
                            <option value="Needs Cleaning" <?php echo ($room['access'] == 'Needs Cleaning') ? 'selected' : ''; ?>>Needs Cleaning</option>
                            <option value="Request Cleaning" <?php echo ($room['access'] == 'Request Cleaning') ? 'selected' : ''; ?>>Request Cleaning</option>
                        </select>
                        <i class='bx bx-check-circle'></i>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-submit">Update Room</button>
                        <a class="btn btn-cancel" href="FrontdeskDashboard.php" style="display: flex; justify-content: center; align-items: center;">Cancel</a>
                    </div>
                </form>
            <?php else: ?>
                <p>Room not found.</p>
            <?php endif; ?>
        </div>
    </body>
</html>