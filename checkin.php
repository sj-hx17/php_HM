<?php
    session_start();
    if (!isset($_SESSION['name'])) {
        header("Location: FrontdeskDashboard.php");
        exit();
    }

    // Database connection
    $servername = "localhost"; 
    $username = "root"; 
    $password = ""; 
    $dbname = "sent_otp"; 

    // Create connection
    $connection = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Get parameters if using GET method
    $name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '';
    $roomno = isset($_GET['roomno']) ? htmlspecialchars($_GET['roomno']) : '';

    // Declare success flag
    $success = false;

    // Form Submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
        $roomno = isset($_POST['roomno']) ? htmlspecialchars($_POST['roomno']) : '';
        $status = isset($_POST['status']) ? htmlspecialchars($_POST['status']) : '';
        
        // Process the check-in data here
        $sql = "INSERT INTO room (name, roomno, status) VALUES ('$name', '$roomno', '$status')";
        
        if ($connection->query($sql) === TRUE) {
            // Set success flag to true
            $success = true;
        } else {
            echo "Error: " . $sql . "<br>" . $connection->error;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check In Utility Staff</title>
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
            height: 100vh;
            position: relative;
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
        
        .input-box input, 
        .input-box select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
             background-color: rgba(255, 255, 255, 0.95);
             color: #333;
             border: 2px solid rgba(0, 0, 0, 0.1);
             border-radius: 30px;
             padding: 0 20px;
             font-size: 16px;
             width: 100%;
             height: 50px;
             outline: none;
             transition: all 0.3s;
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

        .back-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #2a7bc8;
            color: white;
            border: none;
            border-radius: 30px;
            width: 55px;
            height: 50px;
            display: flex;
            align-items: center;
            overflow: hidden;
            padding: 0 20px;
            transition: width 0.3s ease, background-color 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            z-index: 1000;
            text-decoration: none; 
        }

        .back-button i {
            font-size: 20px;
            flex-shrink: 0;
            transition: margin-right 0.3s ease;
        }
        
        .back-button span {
            opacity: 0;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            text-align: center;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }
        
        .back-button:hover {
            width: 210px;
            background-color: orange;
        }
        
        .back-button:hover span {
            opacity: 1;
        }

        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .modal-content p {
            font-size: 18px;
            font-weight: 500;
            color: #333;
            margin-bottom: 20px;
        }

        #okButton {
            background-color: #3C91E6;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        #okButton:hover {
            background-color: #2a7bc8;
        }

        @media (max-width: 500px) {
            .back-button {
                width: 50px;
                height: 45px;
                bottom: 20px;
                right: 20px;
            }

            .back-button:hover {
                width: 180px;
            }

            .back-button i {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Check In</h1>

        <?php if ($success): ?>
            <p class="success-message" style="color: green; text-align: center; font-weight: 500;">
                Successfully checked in <?php echo htmlspecialchars($name); ?> for room <?php echo htmlspecialchars($roomno); ?>!
            </p>
            
        <?php else: ?>
            <form method="POST" action="checkin.php">
                <div class="input-box">
                    <input type="text" id="name" name="name" placeholder="Guest Information" required>
                    <i class='bx bx-user'></i>
                </div>

                <div class="input-box">
                    <select id="roomno" name="roomno" required>
                        <option value="" disabled selected>Select Room</option>
                        <optgroup label="1st Floor">
                            <?php for ($i = 101; $i <= 110; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </optgroup>

                        <optgroup label="2nd Floor">
                            <?php for ($i = 201; $i <= 210; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </optgroup>

                        <optgroup label="3rd Floor">
                            <?php for ($i = 301; $i <= 310; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </optgroup>
                    </select>
                    <i class='bx bx-home'></i>
                </div>

                <div class="input-box">
                    <select id="status" name="status" required>
                        <option value="" disabled selected>Select Status</option>
                        <option value="Clean">Clean</option>
                        <option value="Dirty">Dirty</option>
                    </select>
                    <i class='bx bx-check-circle'></i>
                </div>

                <button type="submit" class="btn">Complete Check-In</button>
            </form>
        <?php endif; ?>
    </div>

    <a href="FrontdeskDashboard.php" class="back-button">
        <i class='bx bx-chevron-left'></i>
        <span>Back</span>
    </a>

    <?php if ($success): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.createElement("div");
            modal.classList.add("modal");
            modal.innerHTML = `
                <div class="modal-content">
                    <p>Successfully checked in ${<?php echo json_encode($name); ?>} for room ${<?php echo json_encode($roomno); ?>}!</p>
                    <button id="okButton">OK</button>
                </div>
            `;
            document.body.appendChild(modal);

            const okBtn = document.getElementById("okButton");
            modal.style.display = "block";

            okBtn.addEventListener("click", function () {
                // Redirect to dashboard
                window.location.href = "FrontdeskDashboard.php"; 
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>