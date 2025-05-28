// DOM Elements
const sidebarToggle = document.getElementById('sidebar-toggle');
const sidebar = document.querySelector('.sidebar');
const addStaffBtn = document.getElementById('add-staff-btn');
const emptyAddStaffBtn = document.getElementById('empty-add-staff-btn');
const addStaffModal = document.getElementById('add-staff-modal');
const closeAddModal = document.getElementById('close-add-modal');
const cancelAdd = document.getElementById('cancel-add');
const submitAdd = document.getElementById('submit-add');
const editStaffModal = document.getElementById('edit-staff-modal');
const closeEditModal = document.getElementById('close-edit-modal');
const cancelEdit = document.getElementById('cancel-edit');
const submitEdit = document.getElementById('submit-edit');
const deleteModal = document.getElementById('delete-modal');
const cancelDelete = document.getElementById('cancel-delete');
const confirmDelete = document.getElementById('confirm-delete');

// Ensure this matches your HTML
const logoutBtn = document.getElementById('user-logout-btn');
const userLogoutBtn = document.getElementById('user-logout-btn');
const logoutModal = document.getElementById('logout-modal');
const cancelLogout = document.getElementById('cancel-logout');
const confirmLogout = document.getElementById('confirm-logout');
const searchStaff = document.getElementById('search-staff');
const positionFilter = document.getElementById('position-filter');
const shiftFilter = document.getElementById('shift-filter');

// Staff data array - empty initially
const staffData = [];

// Toggle sidebar on mobile
sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
});

// Add Staff Modal
addStaffBtn.addEventListener('click', () => {
    addStaffModal.classList.remove('hidden');
});

// Also trigger add staff modal from empty state button
if (emptyAddStaffBtn) {
    emptyAddStaffBtn.addEventListener('click', () => {
        addStaffModal.classList.remove('hidden');
    });
}

closeAddModal.addEventListener('click', () => {
    addStaffModal.classList.add('hidden');
});

cancelAdd.addEventListener('click', () => {
    addStaffModal.classList.add('hidden');
});

submitAdd.addEventListener('click', () => {
    // Get form values
    const name = document.getElementById('name').value;
    const position = document.getElementById('position').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const schedule = document.getElementById('schedule').value;
    
    if (name && email && phone) {
        // Create new staff object
        const newStaff = {
            id: staffData.length + 1,
            name: name,
            position: position,
            email: email,
            phone: phone,
            schedule: schedule
        };
        
        // Add to data array
        staffData.push(newStaff);
        
        // Update table
        updateStaffTable();
        
        // Reset form and close modal
        document.getElementById('add-staff-form').reset();
        addStaffModal.classList.add('hidden');
    }
});

// Edit Staff functionality
function setupEditButtons() {
    document.querySelectorAll('.edit-staff-btn').forEach(button => {
        button.addEventListener('click', () => {
            const staffId = button.getAttribute('data-id');
            const staff = staffData.find(s => s.id == staffId);
            
            if (staff) {
                document.getElementById('edit-id').value = staff.id;
                document.getElementById('edit-name').value = staff.name;
                document.getElementById('edit-position').value = staff.position;
                document.getElementById('edit-email').value = staff.email;
                document.getElementById('edit-phone').value = staff.phone;
                document.getElementById('edit-schedule').value = staff.schedule;
                
                editStaffModal.classList.remove('hidden');
            }
        });
    });
}

closeEditModal.addEventListener('click', () => {
    editStaffModal.classList.add('hidden');
});

cancelEdit.addEventListener('click', () => {
    editStaffModal.classList.add('hidden');
});

submitEdit.addEventListener('click', () => {
    const id = document.getElementById('edit-id').value;
    const name = document.getElementById('edit-name').value;
    const position = document.getElementById('edit-position').value;
    const email = document.getElementById('edit-email').value;
    const phone = document.getElementById('edit-phone').value;
    const schedule = document.getElementById('edit-schedule').value;
    
    if (name && email && phone) {
        // Update data
        const staffIndex = staffData.findIndex(s => s.id == id);
        if (staffIndex !== -1) {
            staffData[staffIndex] = {
                id: parseInt(id),
                name: name,
                position: position,
                email: email,
                phone: phone,
                schedule: schedule
            };
            
            // Update table
            updateStaffTable();
            
            // Close modal
            editStaffModal.classList.add('hidden');
        }
    }
});

// Delete Staff functionality
function setupDeleteButtons() {
    document.querySelectorAll('.delete-staff-btn').forEach(button => {
        button.addEventListener('click', () => {
            const staffId = button.getAttribute('data-id');
            document.getElementById('delete-id').value = staffId;
            deleteModal.classList.remove('hidden');
        });
    });
}

cancelDelete.addEventListener('click', () => {
    deleteModal.classList.add('hidden');
});

confirmDelete.addEventListener('click', () => {
    const id = document.getElementById('delete-id').value;
    
    // Remove from data
    const staffIndex = staffData.findIndex(s => s.id == id);
    if (staffIndex !== -1) {
        staffData.splice(staffIndex, 1);
        
        // Update table
        updateStaffTable();
        
        // Close modal
        deleteModal.classList.add('hidden');
    }
});


// Logout Modal
logoutBtn.addEventListener('click', () => {
    logoutModal.classList.remove('hidden');
});

cancelLogout.addEventListener('click', () => {
    logoutModal.classList.add('hidden');
});

confirmLogout.addEventListener('click', () => {
    // Redirect to Welcome Page
    window.location.href = 'Welcome Page.html';
});

document.getElementById('confirm-logout').addEventListener('click', function() {
    // You can use fetch API to call a logout.php script
    fetch('logout.php')
        .then(response => {
            window.location.href = 'Home.php';
        });
});

// Search functionality
searchStaff.addEventListener('input', () => {
    filterStaffTable();
});

// Filter functionality
positionFilter.addEventListener('change', () => {
    filterStaffTable();
});

shiftFilter.addEventListener('change', () => {
    filterStaffTable();
});

function filterStaffTable() {
    const searchTerm = searchStaff.value.toLowerCase();
    const positionValue = positionFilter.value;
    const shiftValue = shiftFilter.value;
    
    const filteredStaff = staffData.filter(staff => {
        const matchesSearch = 
            staff.name.toLowerCase().includes(searchTerm) || 
            staff.email.toLowerCase().includes(searchTerm) ||
            staff.position.toLowerCase().includes(searchTerm);
        
        const matchesPosition = positionValue === '' || staff.position === positionValue;
        const matchesShift = shiftValue === '' || staff.schedule === shiftValue;
        
        return matchesSearch && matchesPosition && matchesShift;
    });
    
    updateStaffTable(filteredStaff);
}

// Logout functionality
document.addEventListener('DOMContentLoaded', function() {
    // Get modal and buttons
    const logoutModal = document.getElementById('logout-modal');
    const logoutBtn = document.getElementById('user-logout-btn');
    const confirmLogoutBtn = document.getElementById('confirm-logout');
    const cancelLogoutBtn = document.getElementById('cancel-logout');

    // Show logout modal when logout button is clicked
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logoutModal.classList.remove('hidden');
        });
    }

    // Hide logout modal when cancel is clicked
    if (cancelLogoutBtn) {
        cancelLogoutBtn.addEventListener('click', function() {
            logoutModal.classList.add('hidden');
        });
    }

    // Handle logout confirmation
    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', function() {
            // Redirect to logout.php which will handle the server-side logout
            window.location.href = 'logout.php';
        });
    }
});

// Helper Functions
function addStaffToTable(staff) {
    // Get initials for avatar
    const initials = staff.name.split(' ').map(n => n[0]).join('');
    
    // Generate random color for avatar
    const colors = ['blue', 'green', 'purple', 'red', 'yellow', 'indigo', 'pink'];
    const colorIndex = Math.floor(Math.random() * colors.length);
    const color = colors[colorIndex];
    
    // Get shift color
    let shiftColor = 'blue';
    if (staff.schedule === 'Afternoon') shiftColor = 'green';
    if (staff.schedule === 'Night') shiftColor = 'purple';
    
    return `
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-10 w-10 rounded-full bg-${color}-100 flex items-center justify-center">
                        <span class="text-${color}-600 font-medium">${initials}</span>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${staff.name}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-${staff.position === 'Front Desk Staff' ? 'blue' : 'green'}-100 text-${staff.position === 'Front Desk Staff' ? 'blue' : 'green'}-800">${staff.position}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${staff.email}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${staff.phone}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-${shiftColor}-100 text-${shiftColor}-800">${staff.schedule}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                    <button class="edit-staff-btn text-indigo-600 hover:text-indigo-900" data-id="${staff.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button class="delete-staff-btn text-red-600 hover:text-red-900" data-id="${staff.id}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

function updateStaffTable(data = staffData) {
    const tableBody = document.getElementById('staff-table-body');
    
    // Check if there are staff members to display
    if (data.length > 0) {
        let tableContent = '';
        data.forEach(staff => {
            tableContent += addStaffToTable(staff);
        });
        tableBody.innerHTML = tableContent;
        
        // Update pagination info
        document.querySelector('.text-sm.text-gray-700').innerHTML = `
            Showing <span class="font-medium">1</span> to <span class="font-medium">${data.length}</span> of <span class="font-medium">${data.length}</span> results
        `;
        
        // Setup event listeners for the new buttons
        setupEditButtons();
        setupDeleteButtons(); 
    } else {
        // Show empty state
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <p class="text-gray-500 text-lg font-medium mb-1">No staff profiles found</p>
                        <p class="text-gray-400 mb-4">Get started by adding your first staff member</p>
                        <button id="empty-add-staff-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>Add Profile</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        
        // Update pagination info
        document.querySelector('.text-sm.text-gray-700').innerHTML = `
            Showing <span class="font-medium">0</span> results
        `;
        
        // Re-attach event listener to the empty state button
        document.getElementById('empty-add-staff-btn').addEventListener('click', () => {
            addStaffModal.classList.remove('hidden');
        });
    }
}

// Close modals when clicking outside
window.addEventListener('click', (e) => {
    if (e.target === addStaffModal) {
        addStaffModal.classList.add('hidden');
    }
    if (e.target === editStaffModal) {
        editStaffModal.classList.add('hidden');
    }
    if (e.target === deleteModal) {
        deleteModal.classList.add('hidden');
    }
    if (e.target === logoutModal) {
        logoutModal.classList.add('hidden');
    }
});