<?php
// Start the session
session_start();
include("config.php");

// Fetch room data from the database
$sql = "SELECT * FROM room";
$result = mysqli_query($conn, $sql);
$rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Room Status Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary: #2b8a3e;
                --occupied: #ff6b6b;
                --vacant: #51cf66;
                --needs-cleaning: #fcc419;
                --request-cleaning:rgb(105, 190, 250);
                --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                --transition: all 0.3s ease;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 20px;
                line-height: 1.6;
                background-color: #f8f9fa;
                color: #333;
                text-align: center;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
                position: relative;
            }
            
            header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            h1 {
                color: var(black);
                font-size: 2.5rem;
                margin-bottom: 10px;
                position: relative;
                display: inline-block;
            }
            
            .status-legend {
                display: flex;
                justify-content: center;
                gap: 30px;
                margin-bottom: 30px;
                padding: 15px;
                background-color: white;
                border-radius: 10px;
                box-shadow: var(--shadow);
            }
            
            .floor-section {
                margin-bottom: 40px;
            }
            
            .floor-title {
                font-size: 1.8rem;
                font-weight: bold;
                margin-bottom: 20px;
                color: var(--primary);
                text-align: center;
            }
            
            .room-grid {
                display: grid;
                grid-column: 10;
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 15px;
                margin: 0 auto;
            }
            
            .room {
                padding: 20px 10px;
                text-align: center;
                border-radius: 10px;
                font-weight: bold;
                cursor: pointer;
                transition: var(--transition);
                position: relative;
                overflow: hidden;
                box-shadow: var(--shadow);
                color: white;
                font-size: 1.1rem;
            }
            
            .room.occupied {
                background-color: var(--occupied);
                cursor: not-allowed;
                opacity: 0.8;
            }
            
            .room.vacant {
                background-color: var(--vacant);
            }
            
            .room.needs-cleaning {
                background-color: var(--needs-cleaning);
                cursor: not-allowed;
                opacity: 0.8;
            }

            .room.request-cleaning {
                background-color: var(--request-cleaning);
                cursor: not-allowed;
                opacity: 0.8;
            }
            
            /* Modal styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
                justify-content: center;
                align-items: center;
            }
            
            .modal-content {
                background-color: white;
                padding: 20px;
                border-radius: 10px;
                width: 80%;
                max-width: 500px;
                position: relative;
            }
            
            .close {
                position: absolute;
                top: 10px;
                right: 20px;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
            
            .status-notice {
                position: absolute;
                bottom: 5px;
                left: 0;
                right: 0;
                font-size: 0.8rem;
                color: #fff;
                background-color: rgba(0,0,0,0.3);
                padding: 3px;
            }
            
            .back-button {
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: transparent;
                border: 2px solid rgba(0, 0, 0, 0.8);
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                justify-content: center;
                align-items: center;
                cursor: pointer;
                transition: all 0.3s;
                color: rgba(0, 0, 0, 0.8);
                text-decoration: none;
                z-index: 1001;
            }
            
            .back-button:hover {
                background: rgba(0, 0, 0, 0.1);
                border-color: black;
                color: black;
                transform: translateY(-3px) scale(1.1);
            }
            
            .back-button i {
                font-size: 18px;
            }
        </style>
    </head>
    <body>
        <div class="container">
                
            <header>
                <h1>Room Status Dashboard</h1>
            </header>
            
            <div class="floor-section" id="floor-1">
                <div class="room-grid">
                    <?php foreach ($rooms as $room): 
                        $isClickable = ($room['access'] == 'Vacant');
                        $statusClass = strtolower(str_replace(' ', '-', $room['access']));
                    ?>
                        <div class="room <?php echo $statusClass; ?>" 
                            data-room="<?php echo $room['roomno']; ?>"
                            <?php if ($isClickable): ?>
                                onclick="window.location.href='EditCheckin.php?id=<?php echo $room['id']; ?>'"
                            <?php endif; ?>>
                            <span class="room-number"><?php echo $room['roomno']; ?></span><br>
                            <span class="room-status"><?php echo $room['access']; ?></span>
                            <?php if (!$isClickable): ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <a href="FrontdeskDashboard.php" class="back-button" title="Back">
            <i class='fas fa-chevron-left'></i>
        </a>
        
        <!-- Modal for Room Details -->
        <div class="modal" id="roomModal">
            <div class="modal-content">
                <span class="close" id="closeModal">&times;</span>
                <h2>Room Details</h2>
                <p id="roomDetails"></p>
            </div>
        </div>
        <script>
            // Only add click handlers to vacant rooms
            document.querySelectorAll('.room.vacant').forEach(room => {
                room.addEventListener('click', function() {
                    const roomNumber = this.getAttribute('data-room');
                    document.getElementById('roomDetails').textContent = `Loading details for Room ${roomNumber}...`;
                    document.getElementById('roomModal').style.display = 'flex';
                    
                    // Here you could fetch more details via AJAX if needed
                });
            });

            // Close modal
            document.getElementById('closeModal').addEventListener('click', function() {
                document.getElementById('roomModal').style.display = 'none';
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', function(event) {
                if (event.target == document.getElementById('roomModal')) {
                    document.getElementById('roomModal').style.display = 'none';
                }
            });
        </script>
    </body>
</html>