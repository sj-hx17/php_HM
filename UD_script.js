document.addEventListener('DOMContentLoaded', function() {
            // Sample data for rooms
            const roomsData = {
                1: generateRooms(1, 101, 110),
                2: generateRooms(2, 201, 210),
                3: generateRooms(3, 301, 310)
            };

            // Sample data for lost and found items
            const lostFoundItems = [];
            
            // Render rooms for all floors
            renderAllFloors();
            
            // Update dashboard summary
            updateDashboardSummary();

            // Room click event for all floors
            document.querySelectorAll('[id^="room-grid-"]').forEach(grid => {
                grid.addEventListener('click', function(e) {
                    const room = e.target.closest('.room');
                    if (room) {
                        const roomNumber = room.dataset.room;
                        const status = room.dataset.status;
                        const lastCleaned = room.dataset.lastCleaned || 'Not available';
                        
                        // Update modal content
                        document.getElementById('modal-room-number').textContent = `Room ${roomNumber}`;
                        document.getElementById('modal-status').textContent = getStatusText(status);
                        document.getElementById('modal-details').textContent = `Last vacant: ${lastCleaned}`;
                        
                        const statusIndicator = document.getElementById('modal-status-indicator');
                        statusIndicator.className = 'w-4 h-4 rounded-full';
                        if (status === 'needs-cleaning') {
                            statusIndicator.classList.add('bg-yellow-500');
                        } else {
                            statusIndicator.classList.add('bg-green-500');
                        }
                        
                        // Show or hide mark as vacant button based on status
                        const markCleanedBtn = document.getElementById('mark-vacant-btn');
                        if (status === 'needs-cleaning') {
                            markCleanedBtn.classList.remove('hidden');
                            markCleanedBtn.dataset.room = roomNumber;
                        } else {
                            markCleanedBtn.classList.add('hidden');
                        }
                        
                        // Show modal
                        document.getElementById('room-modal').classList.remove('hidden');
                    }
                });
            });

            // Close room modal
            document.getElementById('close-modal').addEventListener('click', function() {
                document.getElementById('room-modal').classList.add('hidden');
            });

            // Mark room as vacant
            document.getElementById('mark-vacant-btn').addEventListener('click', function() {
                const roomNumber = this.dataset.room;
                const floor = Math.floor(parseInt(roomNumber) / 100);
                const roomIndex = roomsData[floor].findIndex(room => room.number === parseInt(roomNumber));
                
                if (roomIndex !== -1) {
                    roomsData[floor][roomIndex].status = 'vacant';
                    roomsData[floor][roomIndex].lastCleaned = getCurrentDateTime();
                    renderRoom(floor, roomIndex);
                    updateDashboardSummary();
                    document.getElementById('room-modal').classList.add('hidden');
                    
                    // Show success notification
                    showNotification(`Room ${roomNumber} marked as vacant`);
                }
            });

            // Lost & Found button click
            document.getElementById('lost-found-btn').addEventListener('click', function() {
                document.getElementById('lost-found-modal').classList.remove('hidden');
                renderLostFoundTable();
            });

            // Close lost & found modal
            document.getElementById('close-lost-found').addEventListener('click', function() {
                document.getElementById('lost-found-modal').classList.add('hidden');
            });

            // Submit lost item form
            document.getElementById('lost-item-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const roomNumber = document.getElementById('modal-room-number').textContent.replace('Room ', '');
                const itemDescription = this.querySelector('input[type="text"]').value;
                
                if (itemDescription.trim() !== '') {
                    // Add new item to the list
                    const newItem = {
                        id: lostFoundItems.length + 1,
                        room: roomNumber,
                        item: itemDescription,
                        date: getCurrentDate(),
                        status: 'Pending',
                        image: createSVGImage('generic')
                    };
                    
                    lostFoundItems.unshift(newItem);
                    
                    // Reset form and close modal
                    this.reset();
                    document.getElementById('room-modal').classList.add('hidden');
                    
                    // Update lost & found summary
                    updateLostFoundSummary();
                    
                    // Show success notification
                    showNotification(`Lost item reported for Room ${roomNumber}`);
                    
                    // Show lost & found modal
                    setTimeout(() => {
                        document.getElementById('lost-found-modal').classList.remove('hidden');
                        renderLostFoundTable();
                    }, 500);
                }
            });

            // Search lost items
            document.getElementById('search-lost-items').addEventListener('input', function() {
                renderLostFoundTable(this.value);
            });

            // Logout button click
            document.getElementById('logout-btn').addEventListener('click', function() {
                showNotification('Logging out...');
                setTimeout(() => {
                    alert('You have been logged out.');
                }, 1000);
            });

            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                const roomModal = document.getElementById('room-modal');
                const lostFoundModal = document.getElementById('lost-found-modal');
                
                if (e.target === roomModal) {
                    roomModal.classList.add('hidden');
                }
                
                if (e.target === lostFoundModal) {
                    lostFoundModal.classList.add('hidden');
                }
            });

            // Helper functions
            function generateRooms(floor, start, end) {
                const rooms = [];
                for (let i = start; i <= end; i++) {
                    const statusRandom = Math.random();
                    let status;
                    let lastCleaned = null;
                    
                    if (statusRandom < 0.6) {
                        status = 'needs-cleaning';
                    } else {
                        status = 'vacant';
                        lastCleaned = getRandomPastTime();
                    }
                    
                    rooms.push({
                        number: i,
                        status: status,
                        lastCleaned: lastCleaned
                    });
                }
                return rooms;
            }

            function renderAllFloors() {
                for (let floor = 1; floor <= 3; floor++) {
                    renderFloor(floor);
                }
            }

            function renderFloor(floor) {
                const roomsGrid = document.getElementById(`room-grid-${floor}`);
                roomsGrid.innerHTML = '';
                
                roomsData[floor].forEach((room, index) => {
                    const roomElement = document.createElement('div');
                    roomElement.className = `room ${room.status} rounded-lg p-4 text-center cursor-pointer shadow-sm`;
                    roomElement.dataset.room = room.number;
                    roomElement.dataset.status = room.status;
                    if (room.lastCleaned) {
                        roomElement.dataset.lastCleaned = room.lastCleaned;
                    }
                    
                    roomElement.innerHTML = `
                        <div class="font-bold text-lg">${room.number}</div>
                        <div class="text-xs mt-2">${getStatusIcon(room.status)}</div>
                    `;
                    
                    roomsGrid.appendChild(roomElement);
                });
            }

            function renderRoom(floor, roomIndex) {
                const room = roomsData[floor][roomIndex];
                const roomsGrid = document.getElementById(`room-grid-${floor}`);
                const roomElements = roomsGrid.querySelectorAll('.room');
                
                if (roomElements[roomIndex]) {
                    const roomElement = roomElements[roomIndex];
                    roomElement.className = `room ${room.status} rounded-lg p-4 text-center cursor-pointer shadow-sm`;
                    roomElement.dataset.status = room.status;
                    if (room.lastCleaned) {
                        roomElement.dataset.lastCleaned = room.lastCleaned;
                    }
                    
                    roomElement.innerHTML = `
                        <div class="font-bold text-lg">${room.number}</div>
                        <div class="text-xs mt-2">${getStatusIcon(room.status)}</div>
                    `;
                }
            }

            function renderLostFoundTable(searchTerm = '') {
                const tableBody = document.getElementById('lost-found-table');
                tableBody.innerHTML = '';
                
                let filteredItems = [...lostFoundItems];
                
                // Apply search filter if provided
                if (searchTerm) {
                    const term = searchTerm.toLowerCase();
                    filteredItems = filteredItems.filter(item => 
                        item.item.toLowerCase().includes(term) || 
                        item.room.toString().includes(term) ||
                        item.status.toLowerCase().includes(term)
                    );
                }
                
                if (filteredItems.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No matching items found</td></tr>';
                    return;
                }
                
                filteredItems.forEach(item => {
                    const row = document.createElement('tr');
                    row.className = 'table-row-hover';
                    
                    // Create status badge class based on status
                    let statusClass = 'bg-gray-100 text-gray-800';
                    if (item.status === 'Pending') {
                        statusClass = 'bg-yellow-100 text-yellow-800';
                    } else if (item.status === 'Claimed') {
                        statusClass = 'bg-green-100 text-green-800';
                    } else if (item.status === 'Stored') {
                        statusClass = 'bg-blue-100 text-blue-800';
                    }
                    
                    row.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Room ${item.room}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center space-x-2">
                                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                    ${item.image}
                                </div>
                                <span>${item.item}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formatDate(item.date)}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                ${item.status}
                            </span>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            function updateLostFoundSummary() {
                // Update lost & found counts
                document.getElementById('total-items-count').textContent = lostFoundItems.length;
                
                // Count today's items
                const today = getCurrentDate();
                const todayItems = lostFoundItems.filter(item => item.date === today);
                document.getElementById('today-items-count').textContent = todayItems.length;
                
                // Update recent items list
                const recentItemsContainer = document.getElementById('recent-lost-items');
                recentItemsContainer.innerHTML = '';
                
                if (lostFoundItems.length === 0) {
                    recentItemsContainer.innerHTML = '<div class="text-gray-500 text-center py-4">No lost items reported yet</div>';
                    return;
                }
                
                // Show up to 3 most recent items
                const recentItems = lostFoundItems.slice(0, 3);
                recentItems.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'bg-gray-50 border border-gray-200 rounded-lg p-2 flex items-center space-x-2';
                    
                    itemElement.innerHTML = `
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                            ${item.image}
                        </div>
                        <div class="flex-grow">
                            <div class="font-medium text-sm">${item.item}</div>
                            <div class="text-xs text-gray-500">Room ${item.room}</div>
                        </div>
                    `;      
                    recentItemsContainer.appendChild(itemElement);
                });
            }

            function getStatusText(status) {
                if (status === 'needs-cleaning') return 'Needs Cleaning';
                return 'Cleaned';
            }

            function getStatusIcon(status) {
                if (status === 'needs-cleaning') {
                    return '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>';
                }
                return '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
            }

            function getRandomPastTime() {
                const hours = Math.floor(Math.random() * 12) + 1;
                const minutes = Math.floor(Math.random() * 60);
                const ampm = Math.random() > 0.5 ? 'AM' : 'PM';
                return `Today, ${hours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
            }

            function getCurrentDateTime() {
                const now = new Date();
                const hours = now.getHours() % 12 || 12;
                const minutes = now.getMinutes();
                const ampm = now.getHours() >= 12 ? 'PM' : 'AM';
                return `Today, ${hours}:${minutes.toString().padStart(2, '0')} ${ampm}`;
            }

            function getCurrentDate() {
                return new Date().toISOString().split('T')[0];
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                
                if (date.toDateString() === today.toDateString()) {
                    return 'Today';
                } else if (date.toDateString() === yesterday.toDateString()) {
                    return 'Yesterday';
                } else {
                    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                }
            }

            function showNotification(message) {
                // Create notification element
                const notification = document.createElement('div');
                notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg transform translate-y-10 opacity-0 transition-all duration-300';
                notification.textContent = message;
                document.body.appendChild(notification);
                
                // Show notification
                setTimeout(() => {
                    notification.classList.remove('translate-y-10', 'opacity-0');
                }, 100);
                
                // Hide and remove notification
                setTimeout(() => {
                    notification.classList.add('translate-y-10', 'opacity-0');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }   
        });