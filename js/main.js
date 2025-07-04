// js/main.js
// Main JavaScript file for ILab UNMUL Website

class ILabApp {
    constructor() {
        this.apiUrl = 'api/';
        this.currentUser = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadEquipmentData();
        this.loadNews();
        this.checkAuthStatus();
    }

    // Setup event listeners
    setupEventListeners() {
        // Contact form
        const contactForm = document.querySelector('.contact-form');
        if (contactForm) {
            contactForm.addEventListener('submit', (e) => this.handleContactForm(e));
        }

        // Reservation modal trigger
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-reserve')) {
                this.openReservationModal(e.target.dataset.equipmentId);
            }
        });

        // Login/Register forms
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }

        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => this.handleRegister(e));
        }

        // Reservation form
        const reservationForm = document.getElementById('reservationForm');
        if (reservationForm) {
            reservationForm.addEventListener('submit', (e) => this.handleReservation(e));
        }

        // Equipment filter
        const equipmentFilter = document.getElementById('equipmentFilter');
        if (equipmentFilter) {
            equipmentFilter.addEventListener('change', (e) => this.filterEquipment(e.target.value));
        }
    }

    // API Call helper
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

    // Load equipment data
    async loadEquipmentData() {
        try {
            const response = await this.apiCall('equipment.php');
            if (response.success) {
                this.renderEquipment(response.data);
            }
        } catch (error) {
            console.error('Failed to load equipment:', error);
        }
    }

    // Render equipment
    renderEquipment(equipment) {
        const container = document.getElementById('equipmentContainer');
        if (!container) return;

        container.innerHTML = equipment.map(item => `
            <div class="equipment-card" data-category="${item.category}">
                <div class="equipment-image">
                    ${item.image_url ? 
                        `<img src="uploads/images/${item.image_url}" alt="${item.name}">` : 
                        '<i class="fas fa-flask"></i>'
                    }
                </div>
                <div class="equipment-info">
                    <h5>${item.name}</h5>
                    <p>${item.description}</p>
                    <div class="equipment-specs">
                        <small class="text-muted">${item.specifications}</small>
                    </div>
                    <div class="equipment-details mt-2">
                        <span class="badge bg-primary">${item.category}</span>
                        <span class="badge bg-success">Available</span>
                    </div>
                    <div class="equipment-actions mt-3">
                        <span class="price">Rp ${this.formatNumber(item.price_per_hour)}/jam</span>
                        <button class="btn btn-primary btn-sm btn-reserve" data-equipment-id="${item.id}">
                            <i class="fas fa-calendar-plus"></i> Reserve
                        </button>
                    </div>
                </div>
            </div>
        `).join('');

        // Add reservation button event listeners
        container.querySelectorAll('.btn-reserve').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.openReservationModal(btn.dataset.equipmentId);
            });
        });
    }

    // Filter equipment
    filterEquipment(category) {
        const equipmentCards = document.querySelectorAll('.equipment-card');
        equipmentCards.forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Load news
    async loadNews() {
        try {
            const response = await this.apiCall('news.php?limit=6');
            if (response.success) {
                this.renderNews(response.data);
            }
        } catch (error) {
            console.error('Failed to load news:', error);
        }
    }

    // Render news
    renderNews(news) {
        const container = document.getElementById('newsContainer');
        if (!container) return;

        container.innerHTML = news.map(item => `
            <div class="col-lg-4 mb-4">
                <div class="news-card">
                    <div class="news-image">
                        ${item.image_url ? 
                            `<img src="uploads/images/${item.image_url}" alt="${item.title}">` : 
                            '<i class="fas fa-newspaper"></i>'
                        }
                    </div>
                    <div class="news-content">
                        <div class="news-date">${this.formatDate(item.publish_date)}</div>
                        <h5>${item.title}</h5>
                        <p>${item.excerpt}</p>
                        <a href="news-detail.php?id=${item.id}" class="btn btn-outline-primary btn-sm">Read More</a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Open reservation modal
    openReservationModal(equipmentId) {
        if (!this.currentUser) {
            this.showLoginModal();
            return;
        }

        // Load equipment details
        this.loadEquipmentDetails(equipmentId).then(equipment => {
            this.showReservationModal(equipment);
        });
    }

    // Load equipment details
    async loadEquipmentDetails(equipmentId) {
        try {
            const response = await this.apiCall(`equipment.php?id=${equipmentId}`);
            if (response.success) {
                return response.data;
            }
        } catch (error) {
            console.error('Failed to load equipment details:', error);
        }
    }

    // Show reservation modal
    showReservationModal(equipment) {
        const modalHtml = `
            <div class="modal fade" id="reservationModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Make Reservation - ${equipment.name}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="reservationForm">
                                <input type="hidden" name="equipment_id" value="${equipment.id}">
                                <input type="hidden" name="user_id" value="${this.currentUser.id}">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Equipment</label>
                                            <input type="text" class="form-control" value="${equipment.name}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Price per Hour</label>
                                            <input type="text" class="form-control" value="Rp ${this.formatNumber(equipment.price_per_hour)}" readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Research Title *</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Start Date *</label>
                                            <input type="date" class="form-control" name="start_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">End Date *</label>
                                            <input type="date" class="form-control" name="end_date" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Start Time *</label>
                                            <input type="time" class="form-control" name="start_time" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">End Time *</label>
                                            <input type="time" class="form-control" name="end_time" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Note:</strong> All reservations are subject to approval. You will receive an email confirmation once your reservation is processed.
                                    </div>
                                </div>
                                
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit Reservation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal
        const existingModal = document.getElementById('reservationModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
        modal.show();

        // Setup form handler
        document.getElementById('reservationForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleReservation(e);
        });
    }

    // Handle reservation submission
    async handleReservation(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await this.apiCall('reservation.php', {
                method: 'POST',
                body: JSON.stringify(data)
            });
            
            if (response.success) {
                this.showNotification('Reservation submitted successfully!', 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('reservationModal'));
                modal.hide();
                
                // Reset form
                e.target.reset();
                
                // Show reservation details
                this.showReservationConfirmation(response);
            }
        } catch (error) {
            this.showNotification('Failed to submit reservation: ' + error.message, 'error');
        }
    }

    // Show reservation confirmation
    showReservationConfirmation(response) {
        const confirmationHtml = `
            <div class="modal fade" id="confirmationModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Reservation Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <strong>Reservation Submitted Successfully!</strong>
                            </div>
                            <p><strong>Reservation ID:</strong> #${response.reservation_id}</p>
                            <p><strong>Total Hours:</strong> ${response.total_hours} hours</p>
                            <p><strong>Estimated Cost:</strong> Rp ${this.formatNumber(response.total_cost)}</p>
                            <p class="text-muted">You will receive an email confirmation once your reservation is reviewed and approved.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', confirmationHtml);
        const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        modal.show();
    }

    // Handle contact form
    async handleContactForm(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await this.apiCall('contact.php', {
                method: 'POST',
                body: JSON.stringify(data)
            });
            
            if (response.success) {
                this.showNotification('Message sent successfully!', 'success');
                e.target.reset();
            }
        } catch (error) {
            this.showNotification('Failed to send message: ' + error.message, 'error');
        }
    }

    // Check authentication status
    checkAuthStatus() {
        const user = localStorage.getItem('ilab_user');
        if (user) {
            this.currentUser = JSON.parse(user);
            this.updateUIForLoggedInUser();
        }
    }

    // Update UI for logged in user
    updateUIForLoggedInUser() {
        const loginBtn = document.querySelector('.btn-login');
        const userMenu = document.querySelector('.user-menu');
        
        if (loginBtn) {
            loginBtn.style.display = 'none';
        }
        
        if (userMenu) {
            userMenu.style.display = 'block';
            userMenu.innerHTML = `
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> ${this.currentUser.full_name}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                        <li><a class="dropdown-item" href="my-reservations.php">My Reservations</a></li>
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="app.logout()">Logout</a></li>
                    </ul>
                </div>
            `;
        }
    }

    // Show login modal
    showLoginModal() {
        const loginHtml = `
            <div class="modal fade" id="loginModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Login Required</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>You need to login to make a reservation.</p>
                            <form id="loginForm">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>
                            <hr>
                            <p class="text-center">Don't have an account? <a href="register.php">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', loginHtml);
        const modal = new bootstrap.Modal(document.getElementById('loginModal'));
        modal.show();
    }

    // Handle login
    async handleLogin(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        try {
            const response = await this.apiCall('auth.php', {
                method: 'POST',
                body: JSON.stringify({ action: 'login', ...data })
            });
            
            if (response.success) {
                this.currentUser = response.user;
                localStorage.setItem('ilab_user', JSON.stringify(response.user));
                this.updateUIForLoggedInUser();
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                modal.hide();
                
                this.showNotification('Login successful!', 'success');
            }
        } catch (error) {
            this.showNotification('Login failed: ' + error.message, 'error');
        }
    }

    // Logout
    logout() {
        this.currentUser = null;
        localStorage.removeItem('ilab_user');
        window.location.reload();
    }

    // Utility functions
    formatNumber(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Show notification
    showNotification(message, type = 'info') {
        const notificationHtml = `
            <div class="alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show notification" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        let container = document.getElementById('notificationContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notificationContainer';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
            document.body.appendChild(container);
        }

        container.insertAdjacentHTML('beforeend', notificationHtml);

        // Auto remove after 5 seconds
        setTimeout(() => {
            const notification = container.querySelector('.notification');
            if (notification) {
                notification.remove();
            }
        }, 5000);
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new ILabApp();
});

// Calendar functionality
class CalendarWidget {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.options = {
            equipmentId: null,
            onDateSelect: null,
            ...options
        };
        this.currentDate = new Date();
        this.selectedDate = null;
        this.reservations = [];
        
        this.init();
    }

    init() {
        this.loadReservations();
        this.render();
    }

    async loadReservations() {
        try {
            const response = await app.apiCall(`reservation.php?action=calendar&equipment_id=${this.options.equipmentId}&month=${this.currentDate.getMonth() + 1}&year=${this.currentDate.getFullYear()}`);
            if (response.success) {
                this.reservations = response.data;
                this.render();
            }
        } catch (error) {
            console.error('Failed to load reservations:', error);
        }
    }

    render() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        let html = `
            <div class="calendar-widget">
                <div class="calendar-header">
                    <button class="btn btn-outline-primary btn-sm" onclick="calendar.prevMonth()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h5>${monthNames[month]} ${year}</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="calendar.nextMonth()">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="calendar-grid">
                    <div class="calendar-days">
                        <div class="calendar-day-header">Sun</div>
                        <div class="calendar-day-header">Mon</div>
                        <div class="calendar-day-header">Tue</div>
                        <div class="calendar-day-header">Wed</div>
                        <div class="calendar-day-header">Thu</div>
                        <div class="calendar-day-header">Fri</div>
                        <div class="calendar-day-header">Sat</div>
                    </div>
                    <div class="calendar-dates">
        `;

        const currentDate = new Date(startDate);
        for (let i = 0; i < 42; i++) {
            const dateStr = currentDate.toISOString().split('T')[0];
            const hasReservation = this.reservations.some(res => res.start_date === dateStr);
            const isCurrentMonth = currentDate.getMonth() === month;
            const isSelected = this.selectedDate === dateStr;

            html += `
                <div class="calendar-date ${isCurrentMonth ? 'current-month' : 'other-month'} ${hasReservation ? 'has-reservation' : ''} ${isSelected ? 'selected' : ''}"
                     onclick="calendar.selectDate('${dateStr}')">
                    <span class="date-number">${currentDate.getDate()}</span>
                    ${hasReservation ? '<span class="reservation-indicator"></span>' : ''}
                </div>
            `;

            currentDate.setDate(currentDate.getDate() + 1);
        }

        html += `
                    </div>
                </div>
            </div>
        `;

        this.container.innerHTML = html;
    }

    selectDate(dateStr) {
        this.selectedDate = dateStr;
        this.render();
        
        if (this.options.onDateSelect) {
            this.options.onDateSelect(dateStr);
        }
    }

    prevMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
        this.loadReservations();
    }

    nextMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
        this.loadReservations();
    }
}

// Initialize calendar if container exists
document.addEventListener('DOMContentLoaded', () => {
    const calendarContainer = document.getElementById('calendar');
    if (calendarContainer) {
        window.calendar = new CalendarWidget('calendar', {
            onDateSelect: (date) => {
                console.log('Selected date:', date);
                // Handle date selection
            }
        });
    }
});