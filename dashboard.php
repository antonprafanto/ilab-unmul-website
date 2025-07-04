<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ILab UNMUL</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), #4a7c59);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .user-welcome {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .user-info h2 {
            margin: 0;
            font-size: 1.8rem;
        }

        .user-info p {
            margin: 0;
            opacity: 0.9;
        }

        .dashboard-nav {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .nav-pills .nav-link {
            color: var(--text-dark);
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-pills .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        .dashboard-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.total { color: #007bff; }
        .stat-icon.pending { color: #ffc107; }
        .stat-icon.approved { color: #28a745; }
        .stat-icon.completed { color: #17a2b8; }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .stat-label {
            color: var(--text-light);
            font-size: 0.9rem;
        }

        .reservation-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .reservation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .reservation-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .reservation-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin: 0;
        }

        .reservation-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-icon {
            color: var(--primary-color);
            width: 20px;
        }

        .quick-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-card {
            flex: 1;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
            text-decoration: none;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            color: var(--text-dark);
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .action-icon.reserve { color: #007bff; }
        .action-icon.equipment { color: #28a745; }
        .action-icon.profile { color: #ffc107; }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .profile-form {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .profile-avatar-large {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 2rem;
        }

        @media (max-width: 768px) {
            .user-welcome {
                flex-direction: column;
                text-align: center;
            }
            
            .quick-actions {
                flex-direction: column;
            }
            
            .stat-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="user-welcome">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info">
                            <h2 id="userName">Loading...</h2>
                            <p id="userRole">User</p>
                            <p id="userInstitution">Institution</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i> Settings
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#profile" onclick="showTab('profile')">Profile</a></li>
                            <li><a class="dropdown-item" href="../index.php">Back to Website</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="logout()">Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Navigation -->
        <div class="dashboard-nav">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link active" href="#dashboard" onclick="showTab('dashboard')">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#reservations" onclick="showTab('reservations')">
                        <i class="fas fa-calendar-check me-2"></i>My Reservations
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#equipment" onclick="showTab('equipment')">
                        <i class="fas fa-flask me-2"></i>Equipment
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#profile" onclick="showTab('profile')">
                        <i class="fas fa-user me-2"></i>Profile
                    </a>
                </li>
            </ul>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard-tab" class="tab-content">
            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="#" class="action-card" onclick="showTab('equipment')">
                    <div class="action-icon reserve">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <h5>Make Reservation</h5>
                    <p>Reserve laboratory equipment</p>
                </a>
                <a href="#" class="action-card" onclick="showTab('equipment')">
                    <div class="action-icon equipment">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h5>Browse Equipment</h5>
                    <p>View available equipment</p>
                </a>
                <a href="#" class="action-card" onclick="showTab('profile')">
                    <div class="action-icon profile">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h5>Edit Profile</h5>
                    <p>Update your information</p>
                </a>
            </div>

            <!-- Statistics -->
            <div class="stat-grid">
                <div class="stat-item">
                    <div class="stat-icon total">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-number" id="totalReservations">0</div>
                    <div class="stat-label">Total Reservations</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number" id="pendingReservations">0</div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon approved">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number" id="approvedReservations">0</div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-item">
                    <div class="stat-icon completed">
                        <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="stat-number" id="completedReservations">0</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="dashboard-card">
                <h4><i class="fas fa-history me-2"></i>Recent Reservations</h4>
                <div id="recentReservations">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservations Tab -->
        <div id="reservations-tab" class="tab-content" style="display: none;">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-calendar-check me-2"></i>My Reservations</h4>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="completed">Completed</option>
                        </select>
                        <button class="btn btn-primary" onclick="loadMyReservations()">
                            <i class="fas fa-refresh"></i> Refresh
                        </button>
                    </div>
                </div>
                <div id="myReservations">
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Equipment Tab -->
        <div id="equipment-tab" class="tab-content" style="display: none;">
            <div class="dashboard-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-flask me-2"></i>Laboratory Equipment</h4>
                    <div class="d-flex gap-2">
                        <select class="form-select" id="categoryFilter">
                            <option value="">All Categories</option>
                        </select>
                        <input type="text" class="form-control" id="searchInput" placeholder="Search equipment...">
                    </div>
                </div>
                <div id="equipmentGrid" class="row">
                    <div class="col-12 text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Tab -->
        <div id="profile-tab" class="tab-content" style="display: none;">
            <div class="profile-form">
                <div class="text-center mb-4">
                    <div class="profile-avatar-large">
                        <i class="fas fa-user"></i>
                    </div>
                    <h4>My Profile</h4>
                </div>
                
                <div id="profileAlert"></div>
                
                <form id="profileForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" id="fullName">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" id="username" readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Institution</label>
                                <input type="text" class="form-control" name="institution" id="institution">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Faculty</label>
                                <input type="text" class="form-control" name="faculty" id="faculty">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" class="form-control" name="phone" id="phone">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <h5>Change Password</h5>
                <form id="passwordForm">
                    <div class="mb-3">
                        <label class="form-label">Current Password</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        class UserDashboard {
            constructor() {
                this.apiUrl = 'api/';
                this.currentUser = null;
                this.init();
            }

            init() {
                this.checkAuthStatus();
                this.setupEventListeners();
                this.loadDashboardData();
            }

            checkAuthStatus() {
                const user = localStorage.getItem('ilab_user');
                if (!user) {
                    window.location.href = 'login.php';
                    return;
                }

                this.currentUser = JSON.parse(user);
                this.updateUserInfo();
            }

            updateUserInfo() {
                document.getElementById('userName').textContent = this.currentUser.full_name;
                document.getElementById('userRole').textContent = this.currentUser.role;
                document.getElementById('userInstitution').textContent = this.currentUser.institution || 'Institution';
            }

            setupEventListeners() {
                // Profile form
                document.getElementById('profileForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.updateProfile(e);
                });

                // Password form
                document.getElementById('passwordForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.changePassword(e);
                });

                // Filters
                document.getElementById('statusFilter')?.addEventListener('change', () => {
                    this.loadMyReservations();
                });

                document.getElementById('categoryFilter')?.addEventListener('change', () => {
                    this.loadEquipment();
                });

                document.getElementById('searchInput')?.addEventListener('input', () => {
                    this.loadEquipment();
                });
            }

            async loadDashboardData() {
                try {
                    // Load user statistics
                    const statsResponse = await app.apiCall(`reservation.php?action=stats&user_id=${this.currentUser.id}`);
                    if (statsResponse.success) {
                        this.updateStatistics(statsResponse.data);
                    }

                    // Load recent reservations
                    await this.loadRecentReservations();

                    // Load profile data
                    await this.loadProfile();

                } catch (error) {
                    console.error('Failed to load dashboard data:', error);
                }
            }

            updateStatistics(stats) {
                document.getElementById('totalReservations').textContent = stats.total_reservations || 0;
                document.getElementById('pendingReservations').textContent = stats.pending_reservations || 0;
                document.getElementById('approvedReservations').textContent = stats.approved_reservations || 0;
                document.getElementById('completedReservations').textContent = stats.completed_reservations || 0;
            }

            async loadRecentReservations() {
                try {
                    const response = await app.apiCall(`reservation.php?user_id=${this.currentUser.id}&limit=5`);
                    if (response.success) {
                        this.renderRecentReservations(response.data);
                    }
                } catch (error) {
                    console.error('Failed to load recent reservations:', error);
                }
            }

            renderRecentReservations(reservations) {
                const container = document.getElementById('recentReservations');
                
                if (reservations.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h5>No Reservations Yet</h5>
                            <p>You haven't made any reservations yet. Start by browsing our equipment!</p>
                            <button class="btn btn-primary" onclick="showTab('equipment')">Browse Equipment</button>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = reservations.map(reservation => `
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <h6 class="reservation-title">${reservation.title}</h6>
                            <span class="status-badge ${reservation.status}">${reservation.status}</span>
                        </div>
                        <div class="reservation-info">
                            <div class="info-item">
                                <i class="fas fa-flask info-icon"></i>
                                <span>${reservation.equipment_name}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar info-icon"></i>
                                <span>${reservation.start_date}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock info-icon"></i>
                                <span>${reservation.start_time} - ${reservation.end_time}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-money-bill info-icon"></i>
                                <span>Rp ${app.formatNumber(reservation.total_cost)}</span>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            async loadMyReservations() {
                try {
                    const status = document.getElementById('statusFilter')?.value || '';
                    const url = `reservation.php?user_id=${this.currentUser.id}${status ? `&status=${status}` : ''}`;
                    const response = await app.apiCall(url);
                    
                    if (response.success) {
                        this.renderMyReservations(response.data);
                    }
                } catch (error) {
                    console.error('Failed to load reservations:', error);
                }
            }

            renderMyReservations(reservations) {
                const container = document.getElementById('myReservations');
                
                if (reservations.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h5>No Reservations Found</h5>
                            <p>No reservations match your current filter.</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = reservations.map(reservation => `
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <h6 class="reservation-title">${reservation.title}</h6>
                            <span class="status-badge ${reservation.status}">${reservation.status}</span>
                        </div>
                        <div class="reservation-info">
                            <div class="info-item">
                                <i class="fas fa-flask info-icon"></i>
                                <span>${reservation.equipment_name}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-calendar info-icon"></i>
                                <span>${reservation.start_date} - ${reservation.end_date}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock info-icon"></i>
                                <span>${reservation.start_time} - ${reservation.end_time}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-money-bill info-icon"></i>
                                <span>Rp ${app.formatNumber(reservation.total_cost)}</span>
                            </div>
                        </div>
                        ${reservation.notes ? `<div class="mt-2"><small class="text-muted"><i class="fas fa-sticky-note"></i> ${reservation.notes}</small></div>` : ''}
                    </div>
                `).join('');
            }

            async loadProfile() {
                try {
                    const response = await app.apiCall(`auth.php?action=profile&user_id=${this.currentUser.id}`);
                    if (response.success) {
                        this.populateProfile(response.data);
                    }
                } catch (error) {
                    console.error('Failed to load profile:', error);
                }
            }

            populateProfile(profile) {
                document.getElementById('fullName').value = profile.full_name || '';
                document.getElementById('username').value = profile.username || '';
                document.getElementById('email').value = profile.email || '';
                document.getElementById('institution').value = profile.institution || '';
                document.getElementById('faculty').value = profile.faculty || '';
                document.getElementById('phone').value = profile.phone || '';
            }

            async updateProfile(e) {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                try {
                    const response = await app.apiCall('auth.php', {
                        method: 'PUT',
                        body: JSON.stringify({ user_id: this.currentUser.id, ...data })
                    });

                    if (response.success) {
                        this.showAlert('profileAlert', 'Profile updated successfully!', 'success');
                        
                        // Update current user data
                        this.currentUser.full_name = data.full_name;
                        this.currentUser.institution = data.institution;
                        localStorage.setItem('ilab_user', JSON.stringify(this.currentUser));
                        this.updateUserInfo();
                    }
                } catch (error) {
                    this.showAlert('profileAlert', 'Failed to update profile: ' + error.message, 'error');
                }
            }

            async changePassword(e) {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                if (data.new_password !== data.confirm_password) {
                    this.showAlert('profileAlert', 'New passwords do not match', 'error');
                    return;
                }

                try {
                    const response = await app.apiCall('auth.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            action: 'change_password',
                            user_id: this.currentUser.id,
                            current_password: data.current_password,
                            new_password: data.new_password
                        })
                    });

                    if (response.success) {
                        this.showAlert('profileAlert', 'Password changed successfully!', 'success');
                        e.target.reset();
                    }
                } catch (error) {
                    this.showAlert('profileAlert', 'Failed to change password: ' + error.message, 'error');
                }
            }

            showAlert(containerId, message, type) {
                const container = document.getElementById(containerId);
                const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
                const icon = type === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';
                
                container.innerHTML = `
                    <div class="alert ${alertClass}">
                        <i class="${icon} me-2"></i>${message}
                    </div>
                `;

                setTimeout(() => {
                    container.innerHTML = '';
                }, 5000);
            }
        }

        // Tab switching
        function showTab(tabName) {
            // Remove active class from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Add active class to clicked nav link
            document.querySelector(`[onclick="showTab('${tabName}')"]`).classList.add('active');
            
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Show selected tab content
            document.getElementById(`${tabName}-tab`).style.display = 'block';
            
            // Load tab-specific data
            switch(tabName) {
                case 'reservations':
                    userDashboard.loadMyReservations();
                    break;
                case 'equipment':
                    app.loadEquipmentData();
                    break;
                case 'profile':
                    userDashboard.loadProfile();
                    break;
            }
        }

        function logout() {
            localStorage.removeItem('ilab_user');
            window.location.href = 'login.php';
        }

        // Initialize dashboard
        const userDashboard = new UserDashboard();
    </script>
</body>
</html>