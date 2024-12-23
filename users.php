<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiPanel - User Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@supabase/supabase-js@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        /* Reuse existing styles */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }

        .dashboard-card {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 0.5rem;
            padding: 1rem;
            backdrop-filter: blur(8px);
            transition: transform 0.2s;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
        }

        .sidebar {
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
        }

        .sidebar.collapsed {
            width: 5rem;
            transform: translateX(0);
        }

        .sidebar.collapsed .nav-text {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar.collapsed .logo-text {
            opacity: 0;
            visibility: hidden;
        }

        .main-content {
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 5rem;
        }

        /* User management specific styles */
        .user-card {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .user-card:hover {
            transform: translateX(5px);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.2);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(16, 185, 129, 0.2);
            color: #10B981;
        }

        .status-banned {
            background-color: rgba(239, 68, 68, 0.2);
            color: #EF4444;
        }

        .status-disabled {
            background-color: rgba(107, 114, 128, 0.2);
            color: #6B7280;
        }

        .filter-dropdown {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 0.5rem;
            padding: 0.5rem;
            color: white;
        }

        .search-input {
            background: rgba(30, 41, 59, 0.9);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            color: white;
            width: 100%;
        }

        .action-button {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: translateY(-2px);
        }

        /* New styles for additional features */
        .announcement-badge {
            background: rgba(59, 130, 246, 0.2);
            color: #60A5FA;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .nav-item {
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            background: rgba(59, 130, 246, 0.1);
        }

        .nav-item.active {
            background: rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-900 to-black min-h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 bg-gray-900 border-r border-blue-900/30">
        <div class="flex justify-between items-center p-4">
            <div>
                <h1 class="text-2xl font-bold text-white logo-text animate__animated animate__fadeIn">📺 MiPanel</h1>
                <p class="text-blue-400 text-sm logo-text">IPTV Admin</p>
            </div>
            <button onclick="toggleSidebar()" class="text-white hover:bg-gray-800 p-2 rounded transition-all duration-300 transform hover:scale-110">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                </svg>
            </button>
        </div>

        <nav class="mt-8">
            <a href="#" class="nav-item active flex items-center text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="nav-text">Users</span>
            </a>
            <a href="#" class="nav-item flex items-center text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="nav-text">Announcements</span>
            </a>
            <a href="#" class="nav-item flex items-center text-white">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="nav-text">Settings</span>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="main-content ml-64 p-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-white animate__animated animate__fadeIn">User Management</h1>
                <p class="text-blue-400">Manage your IPTV users and subscriptions</p>
            </div>
            <div class="flex items-center space-x-4">
                <button onclick="openAddUserModal()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all duration-300 transform hover:scale-105">
                    Add New User
                </button>
                <button onclick="exportUserData()" class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600 transition-all duration-300">
                    Export Data
                </button>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <select id="statusFilter" class="filter-dropdown w-full" onchange="filterUsers()">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="banned">Banned</option>
                    <option value="disabled">Disabled</option>
                </select>
            </div>
            <div>
                <select id="planFilter" class="filter-dropdown w-full" onchange="filterUsers()">
                    <option value="all">All Plans</option>
                    <option value="basic">Basic</option>
                    <option value="premium">Premium</option>
                    <option value="ultimate">Ultimate</option>
                </select>
            </div>
            <div>
                <select id="sortBy" class="filter-dropdown w-full" onchange="sortUsers()">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name">Name A-Z</option>
                    <option value="expiry">Expiry Date</option>
                </select>
            </div>
            <div>
                <input type="text" id="searchInput" placeholder="Search users..." class="search-input" oninput="searchUsers()">
            </div>
        </div>

        <!-- Users List -->
        <div id="usersList" class="space-y-4">
            <!-- User cards will be dynamically inserted here -->
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-6">
            <div class="text-gray-400">
                Showing <span id="showingCount">0</span> of <span id="totalCount">0</span> users
            </div>
            <div class="flex space-x-2">
                <button onclick="previousPage()" class="action-button bg-gray-700 text-white hover:bg-gray-600">Previous</button>
                <button onclick="nextPage()" class="action-button bg-gray-700 text-white hover:bg-gray-600">Next</button>
            </div>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-gray-900 p-6 rounded-lg w-full max-w-md">
            <h2 id="modalTitle" class="text-2xl font-bold text-white mb-4">Add New User</h2>
            <form id="userForm" onsubmit="handleUserSubmit(event)">
                <div class="space-y-4">
                    <div>
                        <label class="text-gray-300">First Name</label>
                        <input type="text" id="firstName" class="search-input mt-1" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Last Name</label>
                        <input type="text" id="lastName" class="search-input mt-1" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Username</label>
                        <input type="text" id="username" class="search-input mt-1" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Email</label>
                        <input type="email" id="email" class="search-input mt-1" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Phone Number</label>
                        <input type="tel" id="phone" class="search-input mt-1" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Subscription Plan</label>
                        <select id="plan" class="filter-dropdown w-full mt-1">
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="ultimate">Ultimate</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-300">Expiry Date</label>
                        <input type="date" id="expiryDate" class="search-input mt-1" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Announcement</label>
                        <textarea id="announcement" class="search-input mt-1" rows="3" placeholder="Add a specific announcement for this user"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeUserModal()" class="action-button bg-gray-700 text-white">Cancel</button>
                    <button type="submit" class="action-button bg-blue-600 text-white hover:bg-blue-700">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // User management functionality
        let users = [];
        let currentPage = 1;
        const usersPerPage = 10;
        let filteredUsers = [];

        // Load user preferences from cookies
        document.addEventListener('DOMContentLoaded', function() {
            loadPreferences();
            loadUsers();
        });

        function loadPreferences() {
            const statusFilter = getCookie('statusFilter') || 'all';
            const planFilter = getCookie('planFilter') || 'all';
            const sortBy = getCookie('sortBy') || 'newest';
            
            document.getElementById('statusFilter').value = statusFilter;
            document.getElementById('planFilter').value = planFilter;
            document.getElementById('sortBy').value = sortBy;
        }

        function setCookie(name, value, days = 30) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/`;
        }

        function getCookie(name) {
            const value = `; ${document.cookie}`;
            const parts = value.split(`; ${name}=`);
            if (parts.length === 2) return parts.pop().split(';').shift();
        }

        async function loadUsers() {
            try {
                // Simulate API call - replace with actual endpoint
                const response = await fetch('/api/users');
                users = await response.json();
                filteredUsers = [...users];
                applyFilters();
                renderUsers();
            } catch (error) {
                console.error('Error loading users:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load users',
                    icon: 'error'
                });
            }
        }

        function renderUsers() {
            const usersList = document.getElementById('usersList');
            usersList.innerHTML = '';

            const start = (currentPage - 1) * usersPerPage;
            const end = start + usersPerPage;
            const paginatedUsers = filteredUsers.slice(start, end);

            paginatedUsers.forEach(user => {
                const userCard = `
                    <div class="user-card">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-white">${user.firstName} ${user.lastName}</h3>
                                <p class="text-gray-400">${user.username}</p>
                                <p class="text-gray-400">${user.email}</p>
                                <p class="text-gray-400">${user.phone}</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="status-badge status-${user.status.toLowerCase()}">${user.status}</span>
                                ${user.announcement ? `<span class="announcement-badge">Has Announcement</span>` : ''}
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-400">Plan: ${user.plan}</p>
                                <p class="text-sm text-gray-400">Expires: ${new Date(user.expiryDate).toLocaleDateString()}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="editUser('${user.id}')" class="action-button bg-blue-600 text-white hover:bg-blue-700">Edit</button>
                                <button onclick="toggleUserStatus('${user.id}')" class="action-button bg-yellow-600 text-white hover:bg-yellow-700">
                                    ${user.status === 'Active' ? 'Disable' : 'Enable'}
                                </button>
                                <button onclick="banUser('${user.id}')" class="action-button bg-red-600 text-white hover:bg-red-700">Ban</button>
                            </div>
                        </div>
                        ${user.announcement ? `
                        <div class="mt-4 p-3 bg-blue-900/20 rounded-lg">
                            <p class="text-sm text-blue-400">${user.announcement}</p>
                        </div>
                        ` : ''}
                    </div>
                `;
                usersList.innerHTML += userCard;
            });

            document.getElementById('showingCount').textContent = paginatedUsers.length;
            document.getElementById('totalCount').textContent = filteredUsers.length;
        }

        function applyFilters() {
            const statusFilter = document.getElementById('statusFilter').value;
            const planFilter = document.getElementById('planFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            filteredUsers = users.filter(user => {
                const matchesStatus = statusFilter === 'all' || user.status.toLowerCase() === statusFilter;
                const matchesPlan = planFilter === 'all' || user.plan.toLowerCase() === planFilter;
                const matchesSearch = user.username.toLowerCase().includes(searchTerm) || 
                                    user.email.toLowerCase().includes(searchTerm) ||
                                    user.firstName.toLowerCase().includes(searchTerm) ||
                                    user.lastName.toLowerCase().includes(searchTerm) ||
                                    user.phone.includes(searchTerm);
                
                return matchesStatus && matchesPlan && matchesSearch;
            });

            sortUsers();
            currentPage = 1;
            renderUsers();

            // Save preferences
            setCookie('statusFilter', statusFilter);
            setCookie('planFilter', planFilter);
        }

        function sortUsers() {
            const sortBy = document.getElementById('sortBy').value;
            
            filteredUsers.sort((a, b) => {
                switch(sortBy) {
                    case 'newest':
                        return new Date(b.createdAt) - new Date(a.createdAt);
                    case 'oldest':
                        return new Date(a.createdAt) - new Date(b.createdAt);
                    case 'name':
                        return `${a.firstName} ${a.lastName}`.localeCompare(`${b.firstName} ${b.lastName}`);
                    case 'expiry':
                        return new Date(a.expiryDate) - new Date(b.expiryDate);
                    default:
                        return 0;
                }
            });

            setCookie('sortBy', sortBy);
            renderUsers();
        }

        function searchUsers() {
            applyFilters();
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                renderUsers();
            }
        }

        function nextPage() {
            const maxPage = Math.ceil(filteredUsers.length / usersPerPage);
            if (currentPage < maxPage) {
                currentPage++;
                renderUsers();
            }
        }

        async function toggleUserStatus(userId) {
            try {
                // Simulate API call
                const user = users.find(u => u.id === userId);
                const newStatus = user.status === 'Active' ? 'Disabled' : 'Active';
                
                await Swal.fire({
                    title: 'Confirm Status Change',
                    text: `Are you sure you want to ${newStatus.toLowerCase()} this user?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        // Update user status
                        user.status = newStatus;
                        renderUsers();
                        
                        Swal.fire({
                            title: 'Success',
                            text: `User has been ${newStatus.toLowerCase()}`,
                            icon: 'success'
                        });
                    }
                });
            } catch (error) {
                console.error('Error toggling user status:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to update user status',
                    icon: 'error'
                });
            }
        }

        async function banUser(userId) {
            try {
                await Swal.fire({
                    title: 'Ban User',
                    text: 'Are you sure you want to ban this user?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, ban user',
                    cancelButtonText: 'Cancel'
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        const user = users.find(u => u.id === userId);
                        user.status = 'Banned';
                        renderUsers();
                        
                        Swal.fire({
                            title: 'Success',
                            text: 'User has been banned',
                            icon: 'success'
                        });
                    }
                });
            } catch (error) {
                console.error('Error banning user:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to ban user',
                    icon: 'error'
                });
            }
        }

        function openAddUserModal() {
            document.getElementById('modalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userModal').classList.remove('hidden');
            document.getElementById('userModal').classList.add('flex');
        }

        function closeUserModal() {
            document.getElementById('userModal').classList.add('hidden');
            document.getElementById('userModal').classList.remove('flex');
        }

        async function handleUserSubmit(event) {
            event.preventDefault();
            
            const userData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                username: document.getElementById('username').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                plan: document.getElementById('plan').value,
                expiryDate: document.getElementById('expiryDate').value,
                announcement: document.getElementById('announcement').value
            };

            try {
                // Simulate API call
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                // Add new user to the list
                const newUser = {
                    id: Date.now().toString(),
                    ...userData,
                    status: 'Active',
                    createdAt: new Date().toISOString()
                };
                
                users.unshift(newUser);
                closeUserModal();
                applyFilters();
                
                Swal.fire({
                    title: 'Success',
                    text: 'User has been added successfully',
                    icon: 'success'
                });
            } catch (error) {
                console.error('Error adding user:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to add user',
                    icon: 'error'
                });
            }
        }

        async function exportUserData() {
            try {
                const csv = convertToCSV(filteredUsers);
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'users_export.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                console.error('Error exporting data:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to export user data',
                    icon: 'error'
                });
            }
        }

        function convertToCSV(users) {
            const headers = ['First Name', 'Last Name', 'Username', 'Email', 'Phone', 'Status', 'Plan', 'Expiry Date', 'Created At', 'Announcement'];
            const rows = users.map(user => [
                user.firstName,
                user.lastName,
                user.username,
                user.email,
                user.phone,
                user.status,
                user.plan,
                new Date(user.expiryDate).toLocaleDateString(),
                new Date(user.createdAt).toLocaleDateString(),
                user.announcement || ''
            ]);
            
            return [headers, ...rows]
                .map(row => row.join(','))
                .join('\n');
        }

        // Sidebar toggle functionality
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    </script>
</body>
</html>
