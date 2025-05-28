<?php 
    session_start();
    if (!isset($_SESSION['name'])) {
        header("Location: AdminLogin.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            :root {
                --primary: #3b82f6;
                --primary-hover: #2563eb;
                --secondary: #f3f4f6;
                --accent: #e5e7eb;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f9fafb;
            }
            
            .sidebar {
                transition: all 0.3s ease;
                background: linear-gradient(180deg, #1e40af 0%, #3b82f6 100%);
            }
            
            .table-container {
                overflow-x: auto;
                scrollbar-width: thin;
                scrollbar-color: var(--primary) var(--secondary);
            }
            
            .table-container::-webkit-scrollbar {
                height: 8px;
            }
            
            .table-container::-webkit-scrollbar-track {
                background: var(--secondary);
            }
            
            .table-container::-webkit-scrollbar-thumb {
                background-color: var(--primary);
                border-radius: 20px;
            }
            
            .modal {
                transition: opacity 0.3s ease;
            }
            
            .card {
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            }
            
            .btn-primary {
                background-color: var(--primary);
                transition: all 0.2s ease;
            }
            
            .btn-primary:hover {
                background-color: var(--primary-hover);
                transform: translateY(-1px);
            }
            
            .avatar {
                background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            }
        </style>
    </head>
    <body class="bg-gray-50">
        <div class="flex h-screen overflow-hidden">
            <!-- Main Content -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Top Navigation -->
                <header class="bg-white shadow-sm border-b border-gray-200">
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center space-x-4">
                            <button id="sidebar-toggle" class="md:hidden p-2 rounded-md hover:bg-gray-100 text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <div>
                                <h1 class="text-xl font-bold text-gray-800">Staff Management</h1>
                                <p class="text-xs text-gray-500">Admin Dashboard</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">    
                            <div class="flex items-center space-x-3">
                                <div class="text-right hidden md:block">
                                    <p class="text-sm font-medium text-gray-700"><?php echo $_SESSION['name']; ?></p>
                                    <p class="text-xs text-gray-500">Administrator</p>
                                </div>

                                <div class="w-10 h-10 rounded-full avatar flex items-center justify-center text-white font-semibold shadow">
                                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                                </div>
                            </div>

                            <div class="relative">
                                <button id="user-logout-btn" class="p-2 rounded-full hover:bg-gray-100 text-gray-600">
                                    <i class="fa fa-sign-out"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="flex-1 overflow-y-auto p-6">
                    <!-- Dashboard Header -->
                    <div class="mb-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Staff Directory</h2>
                                <p class="text-gray-600">Manage your staff profiles and schedules</p>
                            </div>

                            <div class="mt-4 md:mt-0">
                                <form action="NewEmployee.php" method="POST">
                                    <button type="submit" class="btn-primary text-white px-4 py-2.5 rounded-lg flex items-center space-x-2 transition-all shadow-md hover:shadow-lg">
                                        <i class="fas fa-user-plus"></i>
                                        <span>Add New Staff</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-purple-200 p-4 rounded-xl shadow-sm border border-gray-100 card">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Total Staff</p>
                                        <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                            <?php 
                                                $connect = new mysqli("localhost", "root", "", "sent_otp");
                                                $count = $connect->query("SELECT COUNT(*) FROM otp")->fetch_row()[0];
                                                echo $count;
                                            ?>
                                        </h3>
                                    </div>

                                    <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                                        <i class="fas fa-users fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-200 p-4 rounded-xl shadow-sm border border-gray-100 card">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Front Desk</p>
                                        <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                            <?php 
                                                $count = $connect->query("SELECT COUNT(*) FROM otp WHERE role='Frontdesk'")->fetch_row()[0];
                                                echo $count;
                                            ?>
                                        </h3>
                                    </div>

                                    <div class="p-3 rounded-full bg-green-50 text-green-600">
                                        <i class="fas fa-desktop fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-green-200 p-4 rounded-xl shadow-sm border border-gray-100 card">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Utility Staff</p>
                                        <h3 class="text-2xl font-bold text-gray-800 mt-1">
                                            <?php 
                                                $count = $connect->query("SELECT COUNT(*) FROM otp WHERE role='Utility'")->fetch_row()[0];
                                                echo $count;
                                            ?>
                                        </h3>
                                    </div>

                                    <div class="p-3 rounded-full bg-purple-50 text-purple-600">
                                        <i class="fas fa-tools fa-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="search-staff" placeholder="Search staff by name, email or phone..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3">
                                <div class="relative">
                                    <select id="position-filter" class="appearance-none border border-gray-300 rounded-lg px-4 py-2.5 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700">
                                        <option value="">All Positions</option>
                                        <option value="Frontdesk">Front Desk Staff</option>
                                        <option value="Utility">Utility Staff</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                
                                <div class="relative">
                                    <select id="shift-filter" class="appearance-none border border-gray-300 rounded-lg px-4 py-2.5 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700">
                                        <option value="">All Shifts</option>
                                        <option value="Morning">Morning</option>
                                        <option value="Afternoon">Afternoon</option>
                                        <option value="Night">Night</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Staff Table -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                        <div class="table-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white divide-y divide-gray-200" id="staff-table-body">
                                    <?php
                                        $sql = "SELECT * FROM otp";
                                        $result = $connect->query($sql);

                                        if(!$result){
                                            die("Invalid query: " . $connect->error);
                                        }

                                        while($row = $result->fetch_assoc()){
                                            echo "
                                            <tr class='hover:bg-gray-50'>
                                                <td class='px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900'>$row[id]</td>
                                                <td class='px-6 py-4 whitespace-nowrap'>
                                                    <div class='flex items-center'>
                                                        <div class='flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium'>
                                                            ".strtoupper(substr($row['name'], 0, 1))."
                                                        </div>
                                                        <div class='ml-4'>
                                                            <div class='text-sm font-medium text-gray-900'>$row[name]</div>
                                                            <div class='text-sm text-gray-500'>$row[email]</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class='px-6 py-4 whitespace-nowrap'>
                                                    <div class='text-sm text-gray-900'>$row[role]</div>
                                                </td>
                                                <td class='px-6 py-4 whitespace-nowrap'>
                                                    <div class='text-sm text-gray-900'>$row[email]</div>
                                                    <div class='text-sm text-gray-500'>$row[phone]</div>
                                                </td>
                                                <td class='px-6 py-4 whitespace-nowrap'>
                                                    <span class='px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full ";
                                                    
                                                    // Different badge colors based on shift
                                                    if($row['shift'] == 'Morning') {
                                                        echo "bg-blue-100 text-blue-800";
                                                    } elseif($row['shift'] == 'Afternoon') {
                                                        echo "bg-yellow-100 text-yellow-800";
                                                    } else {
                                                        echo "bg-purple-100 text-purple-800";
                                                    }
                                                    
                                                    echo "'>$row[shift]</span>
                                                </td>
                                                <td class='px-6 py-4 whitespace-nowrap text-right text-sm font-medium'>
                                                    <a href='Edit.php?id=$row[id]' class='text-blue-600 hover:text-blue-900 mr-3'><i class='fas fa-edit'></i></a>
                                                    <a href='#' class='text-red-600 hover:text-red-900 delete-btn' data-id='$row[id]'><i class='fas fa-trash-alt'></i></a>
                                                </td>
                                            </tr>";
                                        }                     
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Logout Confirmation Modal -->
        <div id="logout-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Logout</h3>
                    <p class="text-sm text-gray-500 mb-6">Are you sure you want to log out of the admin panel?</p>
                    
                    <div class="flex justify-center space-x-4">
                        <button id="cancel-logout" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </button>
                        <button id="confirm-logout" class="px-6 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">
                            <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-5 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-trash-alt text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Deletion</h3>
                    <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this staff member? This action cannot be undone.</p>
                    
                    <div class="flex justify-center space-x-4">
                        <button id="cancel-delete" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <a href="#" id="confirm-delete" class="px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-all">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </a>
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

            // Delete confirmation modal
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.getAttribute('data-id');
                    const confirmBtn = document.getElementById('confirm-delete');
                    confirmBtn.setAttribute('href', `Delete.php?id=${userId}`);
                    document.getElementById('delete-modal').classList.remove('hidden');
                });
            });

            document.getElementById('cancel-delete').addEventListener('click', function() {
                document.getElementById('delete-modal').classList.add('hidden');
            });

            // Search functionality
            document.getElementById('search-staff').addEventListener('input', function(e) {
                const searchValue = e.target.value.toLowerCase();
                const rows = document.querySelectorAll('#staff-table-body tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchValue) ? '' : 'none';
                });
            });

            // Filter functionality
            document.getElementById('position-filter').addEventListener('change', filterTable);
            document.getElementById('shift-filter').addEventListener('change', filterTable);

            function filterTable() {
                const positionValue = document.getElementById('position-filter').value;
                const shiftValue = document.getElementById('shift-filter').value;
                const rows = document.querySelectorAll('#staff-table-body tr');
                
                rows.forEach(row => {
                    const position = row.querySelector('td:nth-child(3)').textContent.trim();
                    const shift = row.querySelector('td:nth-child(5) span').textContent.trim();
                    
                    const positionMatch = positionValue === '' || position === positionValue;
                    const shiftMatch = shiftValue === '' || shift === shiftValue;
                    
                    row.style.display = positionMatch && shiftMatch ? '' : 'none';
                });
            }
        </script>
    </body>
</html>