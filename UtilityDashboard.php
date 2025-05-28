<?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sent_otp";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch room statuses
    $roomAccess = []; 
    $sql = "SELECT roomno, access FROM room";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $roomAccess[$row["roomno"]] = $row["access"];
        }
    }

    // Start the session
    session_start();
    include("config.php");

    // Fetch room data
    $sql = "SELECT * FROM room";
    $result = mysqli_query($conn, $sql);
    $rooms = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Fetch lost items count
    $lostItemsCount = 0;
    $todayLostItemsCount = 0;
    $lostItemsQuery = "SELECT COUNT(*) as total FROM laf WHERE status = 'Lost'";
    $result = $conn->query($lostItemsQuery);

    if ($result) {
        $row = $result->fetch_assoc();
        $lostItemsCount = $row['total'];
    }

    $today = date('Y-m-d');
    $todayQuery = "SELECT COUNT(*) as today_total FROM laf WHERE status = 'Lost' AND DATE(date) = '$today'";
    $todayResult = $conn->query($todayQuery);

    if ($todayResult) {
        $todayRow = $todayResult->fetch_assoc();
        $todayLostItemsCount = $todayRow['today_total'];
    }

    // Fetch recent lost items
    $recentLostItemsQuery = "SELECT id, item, location, date FROM laf WHERE status = 'Lost' ORDER BY date DESC LIMIT 3";
    $recentLostItemsResult = $conn->query($recentLostItemsQuery);
    $recentLostItems = [];
    if ($recentLostItemsResult) {
        while ($row = $recentLostItemsResult->fetch_assoc()) {
            $recentLostItems[] = $row;
        }
    }

    // Fetch room cleaning stats
    $cleanedRoomsQuery = "SELECT COUNT(*) as total FROM room WHERE access = 'Vacant'";
    $cleanedRoomsResult = $conn->query($cleanedRoomsQuery);
    if ($cleanedRoomsResult) {
        $cleanedRoomsRow = $cleanedRoomsResult->fetch_assoc();
        $cleanedRoomsCount = $cleanedRoomsRow['total'];
    }

    $needsCleaningRoomsQuery = "SELECT COUNT(*) as total FROM room WHERE access = 'Needs Cleaning'";
    $needsCleaningRoomsResult = $conn->query($needsCleaningRoomsQuery);
    if ($needsCleaningRoomsResult) {
        $needsCleaningRoomsRow = $needsCleaningRoomsResult->fetch_assoc();
        $needsCleaningRoomsCount = $needsCleaningRoomsRow['total'];
    }

    $requestCleaningRoomsQuery = "SELECT COUNT(*) as total FROM room WHERE access = 'Request Cleaning'";
    $requestCleaningRoomsResult = $conn->query($requestCleaningRoomsQuery);
    if ($requestCleaningRoomsResult) {
        $requestCleaningRoomsRow = $requestCleaningRoomsResult->fetch_assoc();
        $requestCleaningRoomsCount = $requestCleaningRoomsRow['total'];
    }

    $roomsJson = json_encode($rooms);
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hotel Cleaning Dashboard</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            
            .room-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 15px;
                margin: 0 auto;
            }
            
            .room {
                padding: 20px 10px;
                text-align: center;
                border-radius: 10px;
                font-weight: bold;
                transition: var(--transition);
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
                cursor: not-allowed;
            }
            
            .room.needs-cleaning {
                background-color: var(--needs-cleaning);
                cursor: pointer;
            }

            .room.request-cleaning {
                background-color: var(--request-cleaning);
                cursor: pointer;
            }
            
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.5);
            }
        </style>
    </head>
    <body class="min-h-screen flex flex-col bg-gray-50">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="flex items-center justify-between p-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold text-gray-800">Utility Dashboard</h1>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium text-gray-700"><?php echo $_SESSION['name']; ?></p>
                        <p class="text-xs text-gray-500">Utility Personnel</p>
                    </div>

                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold shadow">
                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                    </div>

                    <button id="user-logout-btn" class="p-2 rounded-full hover:bg-gray-100 text-gray-600">
                        <i class="fa fa-sign-out"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-6">
            <!-- Dashboard Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Cleaning Statistics Report -->
                <div class="bg-white rounded-xl shadow-md p-6 h-full flex flex-col">
                    <h2 class="text-xl font-sbold mb-4 text-gray-800 text-center">Cleaning Statistics</h2>
                    <div class="flex flex-col md:flex-row gap-6 flex-grow items-center">
                        <div class="flex-1 flex flex-col gap-4">
                            <div class="flex flex-wrap gap-4 justify-center">
                                <div class="bg-green-500 text-white rounded-lg p-4 text-center w-28">
                                    <h3 class="text-md font-medium">Cleaned Rooms</h3>
                                    <p class="count text-1xl font-bold"><?php echo $cleanedRoomsCount; ?></p>
                                </div>

                                <div class="bg-yellow-500 text-white rounded-lg p-4 text-center w-28">
                                    <h3 class="text-md font-medium">Needs Cleaning</h3>
                                    <p class="count text-1xl font-bold"><?php echo $needsCleaningRoomsCount; ?></p>
                                </div>

                                <div class="bg-blue-500 text-white rounded-lg p-4 text-center w-28">
                                    <h3 class="text-md font-medium">Request Cleaning</h3>
                                    <p class="count text-1xl font-bold"><?php echo $requestCleaningRoomsCount; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 flex items-center justify-center">
                            <div class="relative w-40 h-40">
                                <svg class="w-full h-full" viewBox="0 0 100 100">
                                    <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                    <circle cx="50" cy="50" r="45" fill="none" 
                                        stroke="#51cf66" stroke-width="8" stroke-linecap="round"
                                        stroke-dasharray="283"
                                        stroke-dashoffset="<?php 
                                            $total = $cleanedRoomsCount + $needsCleaningRoomsCount + $requestCleaningRoomsCount ;
                                            echo $total > 0 ? 283 - (($cleanedRoomsCount / $total) * 283) : 283;
                                        ?>"
                                        transform="rotate(-90 50 50)"/>
                                        
                                    <text x="50" y="50" text-anchor="middle" dy=".3em" 
                                        font-size="20" font-weight="bold" fill="#2d3748">
                                        <?php 
                                            $total = $cleanedRoomsCount + $needsCleaningRoomsCount + $requestCleaningRoomsCount;
                                            echo $total > 0 ? round(($cleanedRoomsCount / $total) * 100) : 0;
                                        ?>%
                                    </text>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lost & Found Summary -->
                <div class="bg-white rounded-xl shadow-md p-6 h-full">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Lost & Found</h3>

                        <button id="lost-found-btn" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg text-sm flex items-center space-x-1 transition duration-200">
                            <i class="fas fa-eye mr-1"></i> View All
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-600 text-sm">Total Lost Items</p>
                                    <p class="text-2xl font-bold"><?php echo $lostItemsCount; ?></p>
                                </div>

                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-box text-purple-500"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 text-sm">Today's Report</p>
                                    <p class="text-2xl font-bold"><?php echo $todayLostItemsCount; ?></p>
                                </div>

                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-calendar-day text-blue-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-600 mb-2">Recent Items</h4>
                        <div id="recent-lost-items" class="space-y-2 overflow-hidden" style="height: 120px;">
                            <?php if (empty($recentLostItems)): ?>
                                <div class="text-gray-500 text-center py-4">No lost items reported yet</div>
                            <?php else: ?>
                                <?php foreach ($recentLostItems as $item): ?>
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-2 flex items-center space-x-2">
                                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-inbox text-gray-500"></i>
                                        </div>

                                        <div class="flex-grow">
                                            <div class="font-medium text-sm"><?php echo htmlspecialchars($item['item']); ?></div>
                                            <div class="text-xs text-gray-500">Found in <?php echo htmlspecialchars($item['location'] ?? 'unknown location'); ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Room Status Dashboard -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h1 class="text-2xl font-bold mb-6 text-center">Room Status Dashboard</h1>
                <div class="room-grid">
                    <?php foreach ($rooms as $room): 
                        $statusClass = strtolower(str_replace(' ', '-', $room['access']));
                        $isClickable = ($room['access'] == 'Needs Cleaning' || $room['access'] == 'Request Cleaning');
                    ?>
                        <div class="room <?php echo $statusClass; ?>" 
                            data-room="<?php echo $room['roomno']; ?>"
                            <?php if ($isClickable): ?>
                                onclick="window.location.href='NDModal.php?id=<?php echo $room['id']; ?>'"
                            <?php else: ?>
                                style="cursor: not-allowed;"
                            <?php endif; ?>>
                            <span class="room-number"><?php echo $room['roomno']; ?></span><br>
                            <span class="room-status"><?php echo $room['access']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>

        <!-- Logout Confirmation Modal -->
        <div id="logout-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Logout</h3>
                    <p class="text-sm text-gray-500 mb-6">Are you sure you want to log out?</p>

                    <div class="flex justify-center space-x-4">
                        <button id="cancel-logout" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>

                        <button id="confirm-logout" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm text-sm font-medium">
                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lost & Found Modal -->
        <div id="lost-found-modal" class="modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold">Lost & Found Items</h3>

                        <button id="close-lost-found" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>

                                <tbody id="lost-found-body" class="bg-white divide-y divide-gray-200">
                                    <!-- Items will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Logout functionality
            document.getElementById('user-logout-btn').addEventListener('click', function() {
                document.getElementById('logout-modal').classList.remove('hidden');
            });

            document.getElementById('cancel-logout').addEventListener('click', function() {
                document.getElementById('logout-modal').classList.add('hidden');
            });

            document.getElementById('confirm-logout').addEventListener('click', function() {
                window.location.href = 'logout.php';
            });

            // Lost & Found View All functionality
            document.getElementById('lost-found-btn').addEventListener('click', function() {
                const lostFoundBody = document.getElementById('lost-found-body');
                lostFoundBody.innerHTML = '<tr><td colspan="3" class="text-center py-4">Loading items...</td></tr>';
                
                document.getElementById('lost-found-modal').classList.remove('hidden');

                fetch('fetch_lost_items.php')
                    .then(response => response.json())
                    .then(data => {
                        lostFoundBody.innerHTML = '';
                        
                        if (data.length === 0) {
                            lostFoundBody.innerHTML = '<tr><td colspan="3" class="text-center py-4">No lost items found</td></tr>';
                            return;
                        }

                        data.forEach(item => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.item || 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.location || 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${item.date || 'N/A'}</td>
                            `;
                            lostFoundBody.appendChild(row);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        lostFoundBody.innerHTML = '<tr><td colspan="3" class="text-center py-4 text-red-500">Error loading items</td></tr>';
                    });
            });

            document.getElementById('close-lost-found').addEventListener('click', function() {
                document.getElementById('lost-found-modal').classList.add('hidden');
            });
        </script>
    </body>
</html>