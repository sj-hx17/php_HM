<?php 
    session_start();
    if (!isset($_SESSION['name'])) {
        header("Location: FrontdeskLogin.php");
        exit();
    }

    // Database connection
    $connect = new mysqli('localhost', 'root', '', 'sent_otp');
    if ($connect->connect_error) {
        die("Connection failed: " . $connect->connect_error);
    }

    $name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
    $initial = !empty($name) ? strtoupper(substr($name, 0, 1)) : '';

    // Handle search functionality
    $search = '';
    if (isset($_GET['search'])) {
        $search = $connect->real_escape_string($_GET['search']);
    }

    // Count the number of vacant rooms
    $vacantCount = 0;
    $vacantQuery = "SELECT COUNT(*) as count FROM room WHERE access = 'Vacant'";
    $vacantResult = $connect->query($vacantQuery);
    if ($vacantResult) {
        $vacantRow = $vacantResult->fetch_assoc();
        $vacantCount = $vacantRow['count'];
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Frontdesk Dashboard</title>
        <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="style.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            /* Profile Initial Circle Styles */
            .profile-initial {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
                color: white;
                font-weight: bold;
                font-size: 18px;
                margin-right: 10px;
            }
            
            .profile-container {
                display: flex;
                align-items: center;
            }
            
            .profile-name {
                font-size: 14px;
                font-weight: 500;
                margin-right: 10px;
            }

            .clickable-box {
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .clickable-box:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Enhanced Logout Button Styles */
            #user-logout-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background-color: transparent;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                color: #6b7280;
                position: relative;
            }
            
            #user-logout-btn:hover {
                background-color: #f3f4f6;
                color: #ef4444;
                transform: scale(1.05);
            }
            
            #user-logout-btn .tooltip {
                visibility: hidden;
                width: 80px;
                background-color: #333;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                opacity: 0;
                transition: opacity 0.3s;
                font-size: 12px;
            }
            
            #user-logout-btn:hover .tooltip {
                visibility: visible;
                opacity: 1;
            }
            
            /* Modal Styles to match the image */
            .logout-modal {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            .logout-modal.active {
                opacity: 1;
                visibility: visible;
            }
            
            .logout-modal-content {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 380px;
                overflow: hidden;
                transform: translateY(-20px);
                transition: transform 0.3s ease;
            }
            
            .logout-modal.active .logout-modal-content {
                transform: translateY(0);
            }
            
            .logout-modal-body {
                padding: 24px;
                text-align: center;
            }
            
            .logout-icon {
                font-size: 24px;
                color: #ef4444;
                margin-bottom: 16px;
            }
            
            .logout-modal-title {
                font-size: 18px;
                font-weight: 600;
                color: #111827;
                margin-bottom: 8px;
            }
            
            .logout-modal-text {
                font-size: 14px;
                color: #6b7280;
                margin-bottom: 24px;
            }
            
            .logout-modal-footer {
                display: flex;
                justify-content: center;
                gap: 12px;
                padding: 16px 24px;
                border-top: 1px solid #e5e7eb;
            }
            
            .logout-btn {
                padding: 8px 16px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .logout-btn-cancel {
                background-color: #ffffff;
                color: #4b5563;
                border: 1px solid #d1d5db;
            }
            
            .logout-btn-cancel:hover {
                background-color: #f9fafb;
            }
            
            .logout-btn-confirm {
                background-color: #ef4444;
                color: white;
                border: 1px solid #ef4444;
            }
            
            .logout-btn-confirm:hover {
                background-color: #dc2626;
            }

            .no-style {
                text-decoration: none;
                color: inherit;
            }

            /* Add to your style section */
            .flex-shrink-0 {
                transition: all 0.3s ease;
            }

            .flex-shrink-0:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            }

            .status-badge {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
                font-weight: 600;
                border-radius: 9999px;
            }

            .status-checked-in {
                background-color: #d1fae5;
                color: #065f46;
            }

            .status-pending {
                background-color: #fef3c7;
                color: #92400e;
            }

            /* Enhanced Modal Styles */
            .modal {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }
            
            .modal.active {
                opacity: 1;
                visibility: visible;
            }
            
            .modal-content {
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 400px;
                overflow: hidden;
                transform: translateY(-20px);
                transition: transform 0.3s ease;
            }
            
            .modal.active .modal-content {
                transform: translateY(0);
            }
            
            .modal-header {
                padding: 20px;
                border-bottom: 1px solid #e5e7eb;
                text-align: center;
            }
            
            .modal-body {
                padding: 20px;
                text-align: center;
            }
            
            .modal-footer {
                padding: 20px;
                display: flex;
                justify-content: center;
                gap: 12px;
                border-top: 1px solid #e5e7eb;
            }
            
            .btn {
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .btn-cancel {
                background-color: #f3f4f6;
                color: #4b5563;
                border: 1px solid #e5e7eb;
            }
            
            .btn-cancel:hover {
                background-color: #e5e7eb;
            }
            
            .btn-logout {
                background-color: #ef4444;
                color: white;
                border: 1px solid #ef4444;
            }
            
            .btn-logout:hover {
                background-color: #dc2626;
            }
            
            .logout-icon {
                font-size: 24px;
                margin-bottom: 16px;
                color: #ef4444;
            }

            /* Table Container Styles */
            .table-container {
                width: 100%;
                overflow: hidden;
                border-radius: 0.75rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                background-color: white;
            }

            /* Table Header Styles */
            .table-header {
                background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                border-bottom: 1px solid #e2e8f0;
                padding: 1rem 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .table-title {
                font-size: 1.125rem;
                font-weight: 600;
                color: #1e293b;
                display: flex;
                align-items: center;
            }

            .table-title i {
                margin-right: 0.75rem;
                color: #4f46e5;
            }

            /* Table Controls */
            .table-controls {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .table-search {
                position: relative;
                width: 16rem;
            }

            .table-search input {
                width: 100%;
                padding: 0.5rem 0.75rem 0.5rem 2.5rem;
                border: 1px solid #cbd5e1;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                transition: all 0.2s ease;
            }

            .table-search input:focus {
                outline: none;
                border-color: #818cf8;
                box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.2);
            }

            .table-search i {
                position: absolute;
                left: 0.75rem;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
            }

            .table-button {
                display: flex;
                align-items: center;
                padding: 0.5rem 1rem;
                border-radius: 0.375rem;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all 0.2s ease;
            }

            .table-button i {
                margin-right: 0.5rem;
            }

            /* Table Styles */
            .data-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            .data-table thead th {
                background-color: #f8fafc;
                color: #64748b;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                padding: 0.75rem 1.5rem;
                text-align: left;
                border-bottom: 1px solid #e2e8f0;
            }

            .data-table tbody tr {
                transition: all 0.2s ease;
            }

            .data-table tbody tr:hover {
                background-color: #f8fafc;
            }

            .data-table td {
                padding: 1rem 1.5rem;
                border-bottom: 1px solid #e2e8f0;
                font-size: 0.875rem;
                color: #334155;
                vertical-align: middle;
            }

            /* Guest Avatar Styles */
            .guest-avatar {
                width: 2.5rem;
                height: 2.5rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 1rem;
                color: white;
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                margin-right: 1rem;
                flex-shrink: 0;
                transition: all 0.2s ease;
            }

            .guest-avatar:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3), 0 2px 4px -1px rgba(99, 102, 241, 0.1);
            }

            .clear-search {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background-color: #e2e8f0;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .clear-search:hover {
                background-color: #cbd5e1;
                color: #333;
            }

            /* Status Badge Styles */
            .status-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: capitalize;
            }

            .status-badge i {
                margin-right: 0.25rem;
                font-size: 0.875rem;
            }

            .status-checked-in {
                background-color: #dcfce7;
                color: #166534;
            }

            .status-pending {
                background-color: #fef9c3;
                color: #854d0e;
            }

            .status-checked-out {
                background-color: #e2e8f0;
                color: #334155;
            }

            /* Action Buttons */
            .action-buttons {
                display: flex;
                justify-content: flex-end;
                gap: 0.5rem;
            }

            .action-button {
                width: 2rem;
                height: 2rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .action-button:hover {
                transform: scale(1.1);
            }

            .edit-button {
                color: #3b82f6;
                background-color: #dbeafe;
            }

            .edit-button:hover {
                background-color: #bfdbfe;
            }

            .checkin-button {
                color: #10b981;
                background-color: #d1fae5;
            }

            .checkin-button:hover {
                background-color: #a7f3d0;
            }

            .delete-btn {
            color: #dc2626; 
            transition: color 0.3s ease;
            }

            .delete-btn:hover {
            color: #7f1d1d;
            }

            /* Responsive Adjustments */
            @media (max-width: 1024px) {
                .table-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 1rem;
                }
                
                .table-controls {
                    width: 100%;
                    flex-wrap: wrap;
                }
                
                .table-search {
                    width: 100%;
                }
            }

            @media (max-width: 768px) {
                .data-table {
                    display: block;
                    overflow-x: auto;
                    white-space: nowrap;
                }
                
                .table-pagination {
                    flex-direction: column;
                    gap: 1rem;
                    align-items: flex-start;
                }
            }
        </style>
    </head>
    <body>
        <section id="sidebar">
            <a href="#" class="brand">
                <i class='bx bxs-building'></i>
                <span class="text">HoLoFo MaEm</span>
            </a>

            <ul class="side-menu top">
                <li class="active">
                    <a href="FrontdeskDashboard.html">
                        <i class='bx bxs-dashboard' ></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="LAF.php">
                        <i class='bx bxs-doughnut-chart' ></i>
                        <span class="text">Lost And Found</span>
                    </a>
                </li>
            </ul>
        </section>

        <section id="content">
            <nav>
                <i class='bx bx-menu' ></i>
                <a href="#" class="nav-link">Categories</a>

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="GET">
                    <div class="form-input">
                        <input type="search" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                    </div>
                </form>

                <!-- Enhanced Logout Button with Tooltip -->
                <input type="checkbox" id="switch-mode" hidden>
                <label for="switch-mode" class="switch-mode"></label>

                <div class="profile-container">
                    <span class="profile-name"><?php echo htmlspecialchars($name); ?></span>
                    <div class="profile-initial"><?php echo $initial; ?></div>
                </div>

                <button id="user-logout-btn" class="p-2 rounded-full hover:bg-gray-100 text-gray-600">
                    <i class="fa fa-sign-out"></i>
                </button>
            </nav>

            <main>
                <!-- Your existing content here -->
                <div class="head-title">
                    <div class="left">
                        <h1>Frontdesk Dashboard</h1>
                    </div>
                </div>

                <ul class="box-info">
                    <li>
                        <i class='bx bxs-calendar-check' ></i>
                        <span class="text">
                            <h3><?php echo $vacantCount; ?></h3>
                            <p>Rooms Available</p>
                        </span>
                    </li>

                    <li class="clickable-box" id="utility-staff-checkin" onclick="window.location.href='Vacant.php'">
                        <i class='bx bxs-group' ></i>
                        <span class="text">
                            <h3>New Check In +</h3>
                        </span>
                    </li>
                </ul>

                <div class="table-data w-full">
                    <div class="order bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden w-full">
                        <div class="head bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                Check In Information
                            </h3>
                        </div>

                        <div class="overflow-x-auto w-full">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/4">Guest Information</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Room Details</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Access</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-1/6">Actions</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php
                                        $sql = "SELECT * FROM room";

                                        if (!empty($search)) {
                                            $sql .= " WHERE name LIKE '%$search%' OR roomno LIKE '%$search%' OR status LIKE '%$search%'";
                                        }

                                        $result = $connect->query($sql);
                                        $count = $connect->query("SELECT COUNT(*) FROM room" . (!empty($search) ? " WHERE name LIKE '%$search%' OR roomno LIKE '%$search%' OR status LIKE '%$search%'" : ""))->fetch_row()[0];

                                        if(!$result){
                                            die("Invalid query: " . $connect->error);
                                        }

                                        if ($result->num_rows === 0) {
                                            echo "<tr><td colspan='5' class='px-6 py-4 text-center text-gray-500'>No guests found matching your search criteria.</td></tr>";
                                        } else {
                                            while($row = $result->fetch_assoc()){
                                                $statusClass = '';
                                                $statusText = $row['status'];
                                                
                                                if(strtolower($statusText) == 'checked in') {
                                                    $statusClass = 'bg-green-100 text-green-800';
                                                    $statusIcon = 'bx bx-check-circle';
                                                } elseif(strtolower($statusText) == 'pending') {
                                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                                    $statusIcon = 'bx bx-time';
                                                } elseif(strtolower($statusText) == 'checked out') {
                                                    $statusClass = 'bg-gray-100 text-gray-800';
                                                    $statusIcon = 'bx bx-log-out';
                                                } else {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                    $statusIcon = 'bx bx-info-circle';
                                                }
                                                
                                                echo "
                                                <tr class='hover:bg-gray-50 transition-colors duration-150'>
                                                    <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>{$row['id']}</td>

                                                    <td class='px-6 py-4'>
                                                        <div class='flex items-center'>
                                                            <div class='ml-4'>
                                                                <div class='text-sm font-medium text-gray-900'>{$row['name']}</div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class='px-6 py-4'>
                                                        <div class='flex items-center'>
                                                            <div class='ml-3'>
                                                                <div class='text-sm font-medium text-gray-900'>Room {$row['roomno']}</div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class='px-6 py-4'>
                                                        <div class='flex items-center'>
                                                            <div class='ml-4'>
                                                                <div class='text-sm font-medium text-gray-900'>{$row['access']}</div>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td class='px-6 py-4 text-right'>
                                                        <div class='flex justify-end space-x-2'>
                                                            <a href='EditCheckIn.php?id={$row['id']}' class='text-blue-600 hover:text-blue-900 mr-3'><i class='fas fa-edit'></i></a>
                                                            <a href='#' onclick=\"markRoomAsNeedsCleaning({$row['id']})\" class='text-red-600 hover:text-red-900'><i class='fas fa-sign-out-alt'></i></a>
                                                        </div>
                                                    </td>
                                                </tr>";
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>

        <!-- Enhanced Logout Confirmation Modal -->
        <div id="logout-modal" class="logout-modal">
            <div class="logout-modal-content">
                <div class="logout-modal-body">
                    <div class="logout-icon">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>

                    <h3 class="logout-modal-title">Confirm Logout</h3>
                    <p class="logout-modal-text">Are you sure you want to log out of the frontdesk panel?</p>
                    
                    <div class="logout-modal-footer">
                        <button id="cancel-logout" class="logout-btn logout-btn-cancel">Cancel</button>
                        <button id="confirm-logout" class="logout-btn logout-btn-confirm">Log Out</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Logout functionality
            document.addEventListener('DOMContentLoaded', function() {
                const logoutBtn = document.getElementById('user-logout-btn');
                const logoutModal = document.getElementById('logout-modal');
                const cancelLogout = document.getElementById('cancel-logout');
                const confirmLogout = document.getElementById('confirm-logout');
                
                // Show modal when logout button is clicked
                logoutBtn.addEventListener('click', function() {
                    logoutModal.classList.add('active');
                });
                
                // Hide modal when cancel button is clicked
                cancelLogout.addEventListener('click', function() {
                    logoutModal.classList.remove('active');
                });
                
                // Redirect to logout.php when confirm button is clicked
                confirmLogout.addEventListener('click', function() {
                    window.location.href = 'logout.php';
                });
                
                // Close modal when clicking outside the modal content
                logoutModal.addEventListener('click', function(e) {
                    if (e.target === logoutModal) {
                        logoutModal.classList.remove('active');
                    }
                });
            });

            function markRoomAsNeedsCleaning(roomId) {
                if (confirm("Are you sure you want to mark this room as 'Needs Cleaning' and remove the guest name?")) {
                    fetch('updateRoomStatus.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=' + roomId
                    })

                    .then(response => {
                        if (response.ok) {
                            alert("Room marked as 'Needs Cleaning' successfully.");
                            location.reload();
                        } else {
                            throw new Error('Network response was not ok');
                        }
                    })

                    .catch(error => {
                        console.error('Error:', error);
                        alert("An error occurred while updating the room status.");
                    });
                }
            }
        </script>
        <script src="script.js"></script>
    </body>
</html>