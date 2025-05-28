// Room data
const roomsData = {
    floor1: [],
    floor2: [],
    floor3: [],
    floor4: []
};

// Lost & Found items
const lostFoundItems = [];

// Task tracking
let completedTasks = 0;

// Initialize room data
function initializeRooms() {
    // Generate rooms for each floor
    for (let i = 1; i <= 4; i++) {
        const floorKey = `floor${i}`;
        const startRoom = i * 100 + 1;
        
        for (let j = 0; j < 10; j++) {
            const roomNumber = startRoom + j;
            const randomStatus = Math.random();
            let status;
            
            if (randomStatus < 0.4) {
                status = 'occupied';
            } else if (randomStatus < 0.7) {
                status = 'vacant';
            } else {
                status = 'needs-cleaning';
            }
            
            roomsData[floorKey].push({
                number: roomNumber,
                status: status,
                details: getRandomRoomDetails(status)
            });
        }
    }
}

function getRandomRoomDetails(status) {
    if (status === 'occupied') {
        return 'Guest checked in yesterday. Do not disturb.';
    } else if (status === 'vacant') {
        return 'Room is clean and ready for new guests.';
    } else {
        const times = ['8:30 AM', '9:45 AM', '10:15 AM', '11:30 AM', '12:00 PM'];
        const randomTime = times[Math.floor(Math.random() * times.length)];
        return `Last guest checked out at ${randomTime}. Room requires standard cleaning.`;
    }
}

// Render rooms for the current floor
function renderRooms(floor = 'floor1') {
    const roomGrid = document.querySelector('.grid');
    roomGrid.innerHTML = '';
    
    roomsData[floor].forEach(room => {
        const roomElement = document.createElement('div');
        roomElement.className = `room ${room.status} rounded-lg p-4 text-white text-center cursor-pointer shadow-md`;
        roomElement.innerHTML = `<span class="text-lg font-medium">${room.number}</span>`;
        roomElement.dataset.room = room.number;
        roomElement.dataset.status = room.status;
        roomElement.dataset.details = room.details;
        
        roomElement.addEventListener('click', () => {
            if (room.status === 'needs-cleaning') {
                openCleaningModal(room);
            } else {
                openInfoModal(room);
            }
        });
        
        roomGrid.appendChild(roomElement);
    });
    
    updateStats();
}

// Update dashboard stats
function updateStats() {
    const currentFloor = document.querySelector('.floor-tab.active').textContent.trim().toLowerCase().replace(' ', '');
    const rooms = roomsData[currentFloor];
    
    const occupied = rooms.filter(room => room.status === 'occupied').length;
    const vacant = rooms.filter(room => room.status === 'vacant').length;
    const needsCleaning = rooms.filter(room => room.status === 'needs-cleaning').length;
    
    document.getElementById('total-rooms').textContent = rooms.length;
    document.getElementById('occupied-count').textContent = occupied;
    document.getElementById('vacant-count').textContent = vacant;
    document.getElementById('cleaning-count').textContent = needsCleaning;
    
    // Calculate total rooms that need cleaning across all floors
    let totalNeedsCleaning = 0;
    for (const floor in roomsData) {
        totalNeedsCleaning += roomsData[floor].filter(room => room.status === 'needs-cleaning').length;
    }
    
    // Update assigned rooms stats
    document.getElementById('assigned-rooms').textContent = totalNeedsCleaning;
    document.getElementById('completed-count').textContent = completedTasks;
    document.getElementById('remaining-count').textContent = totalNeedsCleaning - completedTasks;
}

// Open cleaning modal for rooms that need cleaning
function openCleaningModal(room) {
    const modal = document.getElementById('cleaning-modal');
    const roomNumber = document.getElementById('cleaning-modal-room-number');
    const details = document.getElementById('cleaning-modal-details');
    
    roomNumber.textContent = `Room ${room.number}`;
    details.textContent = room.details;
    modal.classList.remove('hidden');
    
    // Clear input fields
    document.getElementById('item-name').value = '';
    document.getElementById('item-location').value = '';
    
    // Store current room number for actions
    modal.dataset.roomNumber = room.number;
}

// Open info modal for occupied or vacant rooms
function openInfoModal(room) {
    const modal = document.getElementById('info-modal');
    const roomNumber = document.getElementById('info-modal-room-number');
    const statusIndicator = document.getElementById('info-modal-status-indicator');
    const status = document.getElementById('info-modal-status');
    const details = document.getElementById('info-modal-details');
    
    roomNumber.textContent = `Room ${room.number}`;
    
    if (room.status === 'occupied') {
        statusIndicator.className = 'w-4 h-4 rounded-full mr-2 bg-red-500';
        status.textContent = 'Occupied';
    } else {
        statusIndicator.className = 'w-4 h-4 rounded-full mr-2 bg-green-500';
        status.textContent = 'Vacant';
    }
    
    details.textContent = room.details;
    modal.classList.remove('hidden');
}

// Close cleaning modal
function closeCleaningModal() {
    const modal = document.getElementById('cleaning-modal');
    modal.classList.add('hidden');
}

// Close info modal
function closeInfoModal() {
    const modal = document.getElementById('info-modal');
    modal.classList.add('hidden');
}

// Mark room as cleaned
function markRoomAsCleaned() {
    const modal = document.getElementById('cleaning-modal');
    const roomNumber = parseInt(modal.dataset.roomNumber);
    
    // Find the room in roomsData
    for (const floor in roomsData) {
        const roomIndex = roomsData[floor].findIndex(room => room.number === roomNumber);
        if (roomIndex !== -1 && roomsData[floor][roomIndex].status === 'needs-cleaning') {
            roomsData[floor][roomIndex].status = 'vacant';
            roomsData[floor][roomIndex].details = 'Room is clean and ready for new guests.';
            
            // Update the UI
            const currentFloor = document.querySelector('.floor-tab.active').textContent.trim().toLowerCase().replace(' ', '');
            if (floor === currentFloor) {
                const roomElement = document.querySelector(`.room[data-room="${roomNumber}"]`);
                if (roomElement) {
                    roomElement.className = 'room vacant rounded-lg p-4 text-white text-center cursor-pointer shadow-md';
                    roomElement.dataset.status = 'vacant';
                }
            }
            
            // Increment completed tasks counter
            completedTasks++;
            
            updateStats();
            closeCleaningModal();
            
            // Show success message
            alert(`Room ${roomNumber} has been marked as cleaned.`);
            break;
        }
    }
}

// Report lost item
function reportLostItem() {
    const modal = document.getElementById('cleaning-modal');
    const roomNumber = modal.dataset.roomNumber;
    const itemName = document.getElementById('item-name').value.trim();
    const itemLocation = document.getElementById('item-location').value.trim();
    
    if (!itemName || !itemLocation) {
        alert('Please fill in both item description and location.');
        return;
    }
    
    // Get current date in YYYY-MM-DD format
    const today = new Date();
    const dateString = today.toISOString().split('T')[0];
    
    // Add to lost & found items
    lostFoundItems.unshift({
        date: dateString,
        room: roomNumber,
        item: itemName,
        location: itemLocation,
        status: 'Pending'
    });
    
    // Update lost & found stats
    document.getElementById('items-reported').textContent = 
        parseInt(document.getElementById('items-reported').textContent) + 1;
    document.getElementById('pending-collection').textContent = 
        parseInt(document.getElementById('pending-collection').textContent) + 1;
    
    // Clear inputs and close modal
    document.getElementById('item-name').value = '';
    document.getElementById('item-location').value = '';
    
    alert(`Lost item reported for Room ${roomNumber}.`);
    closeCleaningModal();
}

// Open lost & found modal
function openLostFoundModal() {
    const modal = document.getElementById('lost-found-modal');
    const table = document.getElementById('lost-found-table');
    
    // Clear existing rows
    table.innerHTML = '';
    
    // Add items to table or show empty message
    if (lostFoundItems.length === 0) {
        const emptyRow = document.createElement('tr');
        const emptyCell = document.createElement('td');
        emptyCell.colSpan = 5;
        emptyCell.className = 'px-6 py-4 text-center text-gray-500';
        emptyCell.textContent = 'No items reported yet';
        emptyRow.appendChild(emptyCell);
        table.appendChild(emptyRow);
    } else {
        lostFoundItems.forEach(item => {
            const row = document.createElement('tr');
            
            const dateCell = document.createElement('td');
            dateCell.className = 'px-6 py-4 whitespace-nowrap';
            dateCell.textContent = item.date;
            
            const roomCell = document.createElement('td');
            roomCell.className = 'px-6 py-4 whitespace-nowrap';
            roomCell.textContent = item.room;
            
            const itemCell = document.createElement('td');
            itemCell.className = 'px-6 py-4 whitespace-nowrap';
            itemCell.textContent = item.item;
            
            const locationCell = document.createElement('td');
            locationCell.className = 'px-6 py-4 whitespace-nowrap';
            locationCell.textContent = item.location;
            
            const statusCell = document.createElement('td');
            statusCell.className = 'px-6 py-4 whitespace-nowrap';
            
            const statusBadge = document.createElement('span');
            statusBadge.className = item.status === 'Pending' 
                ? 'px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800'
                : 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800';
            statusBadge.textContent = item.status;
            
            statusCell.appendChild(statusBadge);
            
            row.appendChild(dateCell);
            row.appendChild(roomCell);
            row.appendChild(itemCell);
            row.appendChild(locationCell);
            row.appendChild(statusCell);
            
            table.appendChild(row);
        });
    }
    
    modal.classList.remove('hidden');
}

// Close lost & found modal
function closeLostFoundModal() {
    const modal = document.getElementById('lost-found-modal');
    modal.classList.add('hidden');
}

// Logout function
function logout() {
    if (confirm('Are you sure you want to log out?')) {
        alert('You have been logged out successfully.');
        // In a real app, this would redirect to login page
        // window.location.href = 'login.html';
    }
}

// Initialize the app
function init() {
    initializeRooms();
    renderRooms('floor1');
    
    // Set up event listeners
    document.getElementById('close-cleaning-modal').addEventListener('click', closeCleaningModal);
    document.getElementById('close-info-modal').addEventListener('click', closeInfoModal);
    document.getElementById('close-info-btn').addEventListener('click', closeInfoModal);
    document.getElementById('mark-cleaned').addEventListener('click', markRoomAsCleaned);
    document.getElementById('report-item').addEventListener('click', reportLostItem);
    document.getElementById('view-lost-found').addEventListener('click', openLostFoundModal);
    document.getElementById('close-lost-found-modal').addEventListener('click', closeLostFoundModal);
    document.getElementById('logout-btn').addEventListener('click', logout);
    
    // Floor tab switching
    const floorTabs = document.querySelectorAll('.floor-tab');
    floorTabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            floorTabs.forEach(t => t.classList.remove('active'));
            floorTabs.forEach(t => t.classList.add('bg-gray-200'));
            tab.classList.add('active');
            tab.classList.remove('bg-gray-200');
            
            const floor = `floor${index + 1}`;
            renderRooms(floor);
            
            // Update floor title
            document.querySelector('h2').textContent = `Floor ${index + 1}`;
        });
    });
}

// Start the app when the page loads
window.addEventListener('DOMContentLoaded', init);