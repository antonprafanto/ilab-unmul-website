<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ILab UNMUL</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c5530;
            --secondary-color: #f8f9fa;
            --accent-color: #ffc107;
            --text-dark: #333;
            --text-light: #666;
            --sidebar-width: 250px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--primary-color);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 2rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: var(--accent-color);
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 0.75rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            padding: 2rem;
        }

        .top-bar {
            background: white;
            padding: 1rem 2rem;
            margin: -2rem -2rem 2rem -2rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-icon.users { background: #28a745; }
        .stat-icon.equipment { background: #17a2b8; }
        .stat-icon.reservations { background: #ffc107; color: #333; }
        .stat-icon.messages { background: #dc3545; }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .data-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .data-table .table {
            margin-bottom: 0;
        }

        .data-table .table th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .data-table .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #e9ecef;
        }

        .data-table .table tbody tr:hover {
            background: var(--secondary-color);
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 0.125rem;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.approved { background: #d1e7dd; color: #0f5132; }
        .status-badge.rejected { background: #f8d7da; color: #721c24; }
        .status-badge.completed { background: #cff4fc; color: #055160; }

        .modal-header {
            background: var(--primary-color);
            color: white;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .loading {
            text-align: center;
            padding: 2rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .top-bar {
                padding: 1rem;
                margin: -2rem -2rem 2rem -2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>ILab UNMUL Admin</h3>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#dashboard" class="menu-link active" data-page="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#reservations" class="menu-link" data-page="reservations"><i class="fas fa-calendar-check"></i> Reservations</a></li>
            <li><a href="#equipment" class="menu-link" data-page="equipment"><i class="fas fa-flask"></i> Equipment</a></li>
            <li><a href="#users" class="menu-link" data-page="users"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="#news" class="menu-link" data-page="news"><i class="fas fa-newspaper"></i> News</a></li>
            <li><a href="#messages" class="menu-link" data-page="messages"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="#settings" class="menu-link" data-page="settings"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="#" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-primary d-md-none me-3" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title" id="pageTitle">Dashboard</h1>
            </div>
            <div class="user-menu">
                <span>Welcome, <strong id="adminName">Administrator</strong></span>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#profile">Profile</a></li>
                        <li><a class="dropdown-item" href="#" onclick="logout()">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div id="pageContent">
            <!-- Dashboard Content -->
            <div id="dashboard-content" class="page-content">
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon users">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-number" id="totalUsers">0</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon equipment">
                                <i class="fas fa-flask"></i>
                            </div>
                            <div class="stat-number" id="totalEquipment">0</div>
                            <div class="stat-label">Equipment</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon reservations">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-number" id="totalReservations">0</div>
                            <div class="stat-label">Reservations</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon messages">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="stat-number" id="totalMessages">0</div>
                            <div class="stat-label">Messages</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="data-table">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Recent Reservations</th>
                                            <th>User</th>
                                            <th>Equipment</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentReservations">
                                        <tr>
                                            <td colspan="6" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="data-table">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Quick Stats</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody id="quickStats">
                                        <tr>
                                            <td colspan="2" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Other page contents will be loaded dynamically -->
            <div id="reservations-content" class="page-content" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Reservations Management</h3>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="reservationStatusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                        <button class="btn btn-primary" onclick="loadReservations()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Equipment</th>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reservationsTable">
                                <tr>
                                    <td colspan="8" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Equipment Management -->
            <div id="equipment-content" class="page-content" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3>Equipment Management</h3>
                    <button class="btn btn-primary" onclick="showAddEquipmentModal()">
                        <i class="fas fa-plus"></i> Add Equipment
                    </button>
                </div>
                <div class="data-table">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Price/Hour</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="equipmentTable">
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation Status Modal -->
    <div class="modal fade" id="reservationStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Reservation Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="statusUpdateForm">
                        <input type="hidden" id="reservationId">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="newStatus" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" id="statusNotes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateReservationStatus()">Update Status</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        class AdminDashboard {
            constructor() {
                this.apiUrl = '../api/';
                this.currentPage = 'dashboard';
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.loadDashboardData();
                this.checkAuthStatus();
            }

            setupEventListeners() {
                // Menu navigation
                document.querySelectorAll('.menu-link').forEach(link => {
                    link.addEventListener('click', (e) => {
                        e.preventDefault();
                        const page = e.target.closest('.menu-link').dataset.page;
                        this.navigateToPage(page);
                    });
                });

                // Status filter for reservations
                const statusFilter = document.getElementById('reservationStatusFilter');
                if (statusFilter) {
                    statusFilter.addEventListener('change', () => this.loadReservations());
                }
            }

            async apiCall(endpoint, options = {}) {
                const defaultOptions = {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                };

                const config = { ...defaultOptions, ...options };

                try {
                    const response = await fetch(this.apiUrl + endpoint, config);
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || 'API request failed');
                    }
                    
                    return data;
                } catch (error) {
                    console.error('API Error:', error);
                    this.showNotification('Error: ' + error.message, 'error');
                    throw error;
                }
            }

            checkAuthStatus() {
                // Check if user is logged in and is admin
                const user = localStorage.getItem('ilab_user');
                if (!user) {
                    window.location.href = '../login.php';
                    return;
                }

                const userData = JSON.parse(user);
                if (userData.role !== 'admin') {
                    window.location.href = '../index.php';
                    return;
                }

                document.getElementById('adminName').textContent = userData.full_name;
            }

            navigateToPage(page) {
                // Update active menu
                document.querySelectorAll('.menu-link').forEach(link => {
                    link.classList.remove('active');
                });
                document.querySelector(`[data-page="${page}"]`).classList.add('active');

                // Hide all page contents
                document.querySelectorAll('.page-content').forEach(content => {
                    content.style.display = 'none';
                });

                // Show selected page content
                const pageContent = document.getElementById(`${page}-content`);
                if (pageContent) {
                    pageContent.style.display = 'block';
                } else {
                    document.getElementById('dashboard-content').style.display = 'block';
                }

                // Update page title
                const pageTitle = page.charAt(0).toUpperCase() + page.slice(1);
                document.getElementById('pageTitle').textContent = pageTitle;

                this.currentPage = page;

                // Load page-specific data
                switch(page) {
                    case 'reservations':
                        this.loadReservations();
                        break;
                    case 'equipment':
                        this.loadEquipment();
                        break;
                    case 'users':
                        this.loadUsers();
                        break;
                    case 'news':
                        this.loadNews();
                        break;
                    case 'messages':
                        this.loadMessages();
                        break;
                }
            }

            async loadDashboardData() {
                try {
                    // Load statistics
                    const [reservationsStats, equipmentStats, usersResponse, messagesResponse] = await Promise.all([
                        this.apiCall('reservation.php?action=stats'),
                        this.apiCall('equipment.php?action=statistics'),
                        this.apiCall('auth.php?action=users&limit=1'),
                        this.apiCall('contact.php?limit=1')
                    ]);

                    // Update stat cards
                    if (reservationsStats.success) {
                        const stats = reservationsStats.data;
                        document.getElementById('totalReservations').textContent = stats.total_reservations || 0;
                    }

                    if (equipmentStats.success) {
                        const stats = equipmentStats.data;
                        document.getElementById('totalEquipment').textContent = stats.total || 0;
                    }

                    // Load recent reservations
                    this.loadRecentReservations();
                    this.loadQuickStats();

                } catch (error) {
                    console.error('Failed to load dashboard data:', error);
                }
            }

            async loadRecentReservations() {
                try {
                    const response = await this.apiCall('reservation.php?limit=5');
                    if (response.success) {
                        this.renderRecentReservations(response.data);
                    }
                } catch (error) {
                    console.error('Failed to load recent reservations:', error);
                }
            }

            renderRecentReservations(reservations) {
                const tbody = document.getElementById('recentReservations');
                if (reservations.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No reservations found</td></tr>';
                    return;
                }

                tbody.innerHTML = reservations.map(reservation => `
                    <tr>
                        <td>#${reservation.id}</td>
                        <td>${reservation.user_name}</td>
                        <td>${reservation.equipment_name}</td>
                        <td>${reservation.start_date}</td>
                        <td><span class="status-badge ${reservation.status}">${reservation.status}</span></td>
                        <td>
                            <button class="btn btn-primary btn-action" onclick="admin.updateReservationStatusModal(${reservation.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            async loadReservations() {
                try {
                    const status = document.getElementById('reservationStatusFilter')?.value || '';
                    const url = `reservation.php${status ? `?status=${status}` : ''}`;
                    const response = await this.apiCall(url);
                    
                    if (response.success) {
                        this.renderReservationsTable(response.data);
                    }
                } catch (error) {
                    console.error('Failed to load reservations:', error);
                }
            }

            renderReservationsTable(reservations) {
                const tbody = document.getElementById('reservationsTable');
                if (reservations.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center">No reservations found</td></tr>';
                    return;
                }

                tbody.innerHTML = reservations.map(reservation => `
                    <tr>
                        <td>#${reservation.id}</td>
                        <td>${reservation.user_name}</td>
                        <td>${reservation.equipment_name}</td>
                        <td>${reservation.title}</td>
                        <td>${reservation.start_date} - ${reservation.end_date}</td>
                        <td>${reservation.start_time} - ${reservation.end_time}</td>
                        <td><span class="status-badge ${reservation.status}">${reservation.status}</span></td>
                        <td>
                            <button class="btn btn-primary btn-action" onclick="admin.updateReservationStatusModal(${reservation.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-info btn-action" onclick="admin.viewReservationDetails(${reservation.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            updateReservationStatusModal(reservationId) {
                document.getElementById('reservationId').value = reservationId;
                const modal = new bootstrap.Modal(document.getElementById('reservationStatusModal'));
                modal.show();
            }

            async updateReservationStatus() {
                const reservationId = document.getElementById('reservationId').value;
                const status = document.getElementById('newStatus').value;
                const notes = document.getElementById('statusNotes').value;

                try {
                    const response = await this.apiCall('reservation.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            action: 'update_status',
                            id: reservationId,
                            status: status,
                            notes: notes
                        })
                    });

                    if (response.success) {
                        this.showNotification('Reservation status updated successfully', 'success');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('reservationStatusModal'));
                        modal.hide();
                        
                        // Reload current page data
                        if (this.currentPage === 'reservations') {
                            this.loadReservations();
                        } else {
                            this.loadRecentReservations();
                        }
                    }
                } catch (error) {
                    this.showNotification('Failed to update reservation status', 'error');
                }
            }

            async loadEquipment() {
                try {
                    const response = await this.apiCall('equipment.php');
                    if (response.success) {
                        this.renderEquipmentTable(response.data);
                    }
                } catch (error) {
                    console.error('Failed to load equipment:', error);
                }
            }

            renderEquipmentTable(equipment) {
                const tbody = document.getElementById('equipmentTable');
                if (equipment.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No equipment found</td></tr>';
                    return;
                }

                tbody.innerHTML = equipment.map(item => `
                    <tr>
                        <td>${item.code}</td>
                        <td>${item.name}</td>
                        <td>${item.category}</td>
                        <td>Rp ${this.formatNumber(item.price_per_hour)}</td>
                        <td><span class="status-badge ${item.status}">${item.status}</span></td>
                        <td>
                            <button class="btn btn-primary btn-action" onclick="admin.editEquipment(${item.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-action" onclick="admin.deleteEquipment(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `).join('');
            }

            loadQuickStats() {
                const tbody = document.getElementById('quickStats');
                tbody.innerHTML = `
                    <tr><td>Pending Reservations</td><td><span id="pendingCount">0</span></td></tr>
                    <tr><td>Available Equipment</td><td><span id="availableCount">0</span></td></tr>
                    <tr><td>New Messages</td><td><span id="newMessagesCount">0</span></td></tr>
                    <tr><td>Active Users</td><td><span id="activeUsersCount">0</span></td></tr>
                `;
            }

            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show notification`;
                notification.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            formatNumber(num) {
                return new Intl.NumberFormat('id-ID').format(num);
            }

            toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('active');
            }
        }

        function logout() {
            localStorage.removeItem('ilab_user');
            window.location.href = '../index.php';
        }

        function toggleSidebar() {
            admin.toggleSidebar();
        }

        // Initialize admin dashboard
        const admin = new AdminDashboard();
    </script>
</body>
</html>