<?php
    session_start();
    include("config.php");
    $id = $_GET['id'] ?? '';
    $room = null;
    $errorMessage = "";
    $successMessage = "";

    if ($id) {
        $sql = "SELECT * FROM room WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $room = $result->fetch_assoc();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['mark_done'])) {
            $newAccess = '';
            $hideLostItems = false;
            
            if ($room['access'] === 'Needs Cleaning') {
                $newAccess = 'Vacant';
            } elseif ($room['access'] === 'Request Cleaning') {
                $newAccess = 'Occupied';
                $hideLostItems = true;
            }
            
            if (!empty($newAccess)) {
                $update_sql = "UPDATE room SET access = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $newAccess, $id);
                
                if ($update_stmt->execute()) {
                    $successMessage = "Room status updated to $newAccess successfully";
                    header("Location: UtilityDashboard.php");
                    exit();
                } else {
                    $errorMessage = "Error updating room status: " . $update_stmt->error;
                }
            }
        } elseif (isset($_POST['report_item'])) {
            $item = $_POST['item'];
            $location = $room['roomno'];
            
            $insert_sql = "INSERT INTO laf (item, location, date, status) VALUES (?, ?, NOW(), 'Lost')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $item, $location);
            
            if ($insert_stmt->execute()) {
                $successMessage = "Lost item reported successfully";
                $_POST['item'] = '';
            } else {
                $errorMessage = "Error reporting lost item: " . $insert_stmt->error;
            }
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
            
            .wrapper h1 {
                font-size: 30px;
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
                transition: all 0.3s;
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
                transition: all 0.3s;
                padding: 0 25px;
                text-align: center;
            }

            .btn-done {
                background: #10b981;
                color: #fff;
            }
            
            .btn-report {
                background: #3b82f6;
                color: #fff;
            }
            
            .btn-cancel {
                background: rgba(255, 255, 255, 0.8);
                color: #3C91E6;
                border: 2px solid #3C91E6;
            }
            
            hr {
                border: 0;
                height: 1px;
                background: rgba(0, 0, 0, 0.1);
                margin: 20px 0;
            }
            
            h3 {
                color: #333;
                margin-bottom: 15px;
                font-size: 20px;
            }
            
            .hidden {
                display: none !important;
            }
            
            .disabled-section {
                opacity: 0.5;
                pointer-events: none;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <h1>ROOM <?php echo htmlspecialchars($room['roomno'] ?? ''); ?></h1>
            
            <?php if (!empty($errorMessage)): ?>
                <div style="color: #DB504A; margin-bottom: 15px; text-align: center;"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($successMessage)): ?>
                <div style="color: #10b981; margin-bottom: 15px; text-align: center;"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            
            <?php if ($room): ?>
                <form method="POST">
                    <div style="display: flex; align-items: center; gap: 5px; margin-bottom: 20px;">
                        <?php 
                        $statusColor = '';
                        if ($room['access'] === 'Needs Cleaning') $statusColor = '#f59e0b';
                        elseif ($room['access'] === 'Request Cleaning') $statusColor = '#3b82f6';
                        elseif ($room['access'] === 'Vacant') $statusColor = '#10b981';
                        elseif ($room['access'] === 'Occupied') $statusColor = '#ef4444';
                        ?>
                        <div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo $statusColor; ?>;"></div>
                        <span style="color: #333; font-weight: 500;"><?php echo htmlspecialchars($room['access']); ?></span>
                    </div>
                    
                    <?php if ($room['access'] == 'Needs Cleaning' || $room['access'] == 'Request Cleaning'): ?>
                        <div class="button-group">
                            <button type="submit" name="mark_done" class="btn btn-done">Mark as Done</button>
                        </div>
                    <?php endif; ?>
                    
                    <hr>
                    
                    <div id="lost-items-section" class="<?php echo $room['access'] === 'Request Cleaning' ? 'hidden' : ''; ?>">
                        <h3>Report Lost Item</h3>
                        <div class="input-box">
                            <input type="text" id="item" name="item" placeholder="Item..." 
                                value="<?php echo htmlspecialchars($_POST['item'] ?? ''); ?>" 
                                <?php echo $room['access'] === 'Request Cleaning' ? 'disabled' : ''; ?>>
                        </div>

                        <div class="button-group">
                            <button type="submit" name="report_item" class="btn btn-report" 
                                <?php echo $room['access'] === 'Request Cleaning' ? 'disabled' : ''; ?>>
                                Submit Report
                            </button>
                            <a class="btn btn-cancel" href="UtilityDashboard.php">Cancel</a>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <p>Room not found.</p>
            <?php endif; ?>
        </div>
    </body>
</html>