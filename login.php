<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ILab UNMUL</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c5530;
            --secondary-color: #f8f9fa;
            --accent-color: #ffc107;
            --text-dark: #333;
            --text-light: #666;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-color), #4a7c59);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 600px;
        }

        .auth-left {
            background: linear-gradient(135deg, var(--primary-color), #4a7c59);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .auth-left h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .auth-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .auth-logo {
            font-size: 5rem;
            margin-bottom: 2rem;
            opacity: 0.3;
        }

        .auth-right {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .auth-subtitle {
            color: var(--text-light);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 85, 48, 0.25);
        }

        .input-group {
            position: relative;
        }

        .input-group-text {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            z-index: 10;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background: #1e3a22;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: var(--text-light);
        }

        .auth-links {
            text-align: center;
            margin-top: 2rem;
        }

        .auth-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-links a:hover {
            color: #1e3a22;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1rem;
        }

        .loading {
            display: none;
            text-align: center;
            margin: 1rem 0;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .tabs {
            display: flex;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 1rem;
            background: none;
            border: none;
            font-weight: 600;
            color: var(--text-light);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                margin: 1rem;
            }
            
            .auth-left {
                padding: 2rem;
            }
            
            .auth-right {
                padding: 2rem;
            }
            
            .auth-left h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <div class="row g-0 h-100">
                <div class="col-lg-6">
                    <div class="auth-left h-100">
                        <div class="auth-logo">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h2>ILab UNMUL</h2>
                        <p>Integrated Laboratory Universitas Mulawarman</p>
                        <p>Pusat unggulan penelitian dan pengujian untuk mendukung inovasi berkelanjutan</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="auth-right h-100">
                        <div class="tabs">
                            <button class="tab active" onclick="switchTab('login')">Login</button>
                            <button class="tab" onclick="switchTab('register')">Register</button>
                        </div>

                        <!-- Login Form -->
                        <div id="login-tab" class="tab-content active">
                            <h3 class="auth-title">Welcome Back!</h3>
                            <p class="auth-subtitle">Please login to your account</p>
                            
                            <div id="loginAlert"></div>
                            
                            <form id="loginForm">
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="loginPassword" required>
                                        <span class="input-group-text" onclick="togglePassword('loginPassword')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Remember me
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="loading" id="loginLoading">
                                    <div class="loading-spinner"></div>
                                    <span>Logging in...</span>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </form>
                            
                            <div class="auth-links">
                                <p><a href="#" onclick="showForgotPassword()">Forgot Password?</a></p>
                                <p>Don't have an account? <a href="#" onclick="switchTab('register')">Register here</a></p>
                            </div>
                        </div>

                        <!-- Register Form -->
                        <div id="register-tab" class="tab-content">
                            <h3 class="auth-title">Create Account</h3>
                            <p class="auth-subtitle">Join ILab UNMUL today</p>
                            
                            <div id="registerAlert"></div>
                            
                            <form id="registerForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Full Name *</label>
                                            <input type="text" class="form-control" name="full_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Username *</label>
                                            <input type="text" class="form-control" name="username" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Institution *</label>
                                            <input type="text" class="form-control" name="institution" value="Universitas Mulawarman" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Faculty</label>
                                            <select class="form-control" name="faculty">
                                                <option value="">Select Faculty</option>
                                                <option value="FMIPA">FMIPA</option>
                                                <option value="Teknik">Teknik</option>
                                                <option value="Kedokteran">Kedokteran</option>
                                                <option value="Farmasi">Farmasi</option>
                                                <option value="Pertanian">Pertanian</option>
                                                <option value="Perikanan">Perikanan</option>
                                                <option value="Kehutanan">Kehutanan</option>
                                                <option value="FISIP">FISIP</option>
                                                <option value="Ekonomi">Ekonomi</option>
                                                <option value="Hukum">Hukum</option>
                                                <option value="FKIP">FKIP</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" name="phone" placeholder="+62">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="registerPassword" required>
                                        <span class="input-group-text" onclick="togglePassword('registerPassword')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Confirm Password *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="confirm_password" id="confirmPassword" required>
                                        <span class="input-group-text" onclick="togglePassword('confirmPassword')">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                                        <label class="form-check-label" for="agreeTerms">
                                            I agree to the <a href="#" onclick="showTerms()">Terms and Conditions</a>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="loading" id="registerLoading">
                                    <div class="loading-spinner"></div>
                                    <span>Creating account...</span>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </form>
                            
                            <div class="auth-links">
                                <p>Already have an account? <a href="#" onclick="switchTab('login')">Login here</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="forgotPasswordAlert"></div>
                    <form id="forgotPasswordForm">
                        <div class="form-group">
                            <label class="form-label">Enter your email address</label>
                            <input type="email" class="form-control" name="email" required>
                            <small class="form-text text-muted">We'll send you a link to reset your password</small>
                        </div>
                        <div class="loading" id="forgotPasswordLoading">
                            <div class="loading-spinner"></div>
                            <span>Sending reset link...</span>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        class AuthManager {
            constructor() {
                this.apiUrl = 'api/';
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.checkAuthStatus();
            }

            setupEventListeners() {
                // Login form
                document.getElementById('loginForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleLogin(e);
                });

                // Register form
                document.getElementById('registerForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleRegister(e);
                });

                // Forgot password form
                document.getElementById('forgotPasswordForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleForgotPassword(e);
                });

                // Password confirmation validation
                document.getElementById('confirmPassword').addEventListener('input', (e) => {
                    this.validatePasswordConfirmation();
                });
            }

            checkAuthStatus() {
                const user = localStorage.getItem('ilab_user');
                if (user) {
                    const userData = JSON.parse(user);
                    if (userData.role === 'admin') {
                        window.location.href = 'admin/dashboard.php';
                    } else {
                        window.location.href = 'dashboard.php';
                    }
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
                    throw error;
                }
            }

            async handleLogin(e) {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());
                
                this.showLoading('loginLoading', true);
                this.clearAlert('loginAlert');

                try {
                    const response = await this.apiCall('auth.php', {
                        method: 'POST',
                        body: JSON.stringify({ action: 'login', ...data })
                    });

                    if (response.success) {
                        localStorage.setItem('ilab_user', JSON.stringify(response.user));
                        this.showAlert('loginAlert', 'Login successful! Redirecting...', 'success');
                        
                        setTimeout(() => {
                            if (response.user.role === 'admin') {
                                window.location.href = 'admin/dashboard.php';
                            } else {
                                window.location.href = 'dashboard.php';
                            }
                        }, 1000);
                    }
                } catch (error) {
                    this.showAlert('loginAlert', error.message, 'error');
                } finally {
                    this.showLoading('loginLoading', false);
                }
            }

            async handleRegister(e) {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                // Validate password confirmation
                if (data.password !== data.confirm_password) {
                    this.showAlert('registerAlert', 'Passwords do not match', 'error');
                    return;
                }

                this.showLoading('registerLoading', true);
                this.clearAlert('registerAlert');

                try {
                    const response = await this.apiCall('auth.php', {
                        method: 'POST',
                        body: JSON.stringify({ action: 'register', ...data })
                    });

                    if (response.success) {
                        this.showAlert('registerAlert', 'Registration successful! Please login with your credentials.', 'success');
                        setTimeout(() => {
                            switchTab('login');
                        }, 2000);
                    }
                } catch (error) {
                    this.showAlert('registerAlert', error.message, 'error');
                } finally {
                    this.showLoading('registerLoading', false);
                }
            }

            async handleForgotPassword(e) {
                const formData = new FormData(e.target);
                const data = Object.fromEntries(formData.entries());

                this.showLoading('forgotPasswordLoading', true);
                this.clearAlert('forgotPasswordAlert');

                try {
                    const response = await this.apiCall('auth.php', {
                        method: 'POST',
                        body: JSON.stringify({ action: 'forgot_password', ...data })
                    });

                    if (response.success) {
                        this.showAlert('forgotPasswordAlert', 'Password reset link sent to your email', 'success');
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
                            modal.hide();
                        }, 2000);
                    }
                } catch (error) {
                    this.showAlert('forgotPasswordAlert', error.message, 'error');
                } finally {
                    this.showLoading('forgotPasswordLoading', false);
                }
            }

            validatePasswordConfirmation() {
                const password = document.getElementById('registerPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                
                if (confirmPassword && password !== confirmPassword) {
                    document.getElementById('confirmPassword').setCustomValidity('Passwords do not match');
                } else {
                    document.getElementById('confirmPassword').setCustomValidity('');
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
            }

            clearAlert(containerId) {
                document.getElementById(containerId).innerHTML = '';
            }

            showLoading(loadingId, show) {
                const loading = document.getElementById(loadingId);
                loading.style.display = show ? 'block' : 'none';
            }
        }

        // Tab switching
        function switchTab(tabName) {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            document.querySelector(`[onclick="switchTab('${tabName}')"]`).classList.add('active');
            document.getElementById(`${tabName}-tab`).classList.add('active');
            
            // Clear any alerts
            document.querySelectorAll('[id$="Alert"]').forEach(alert => alert.innerHTML = '');
        }

        // Password visibility toggle
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Show forgot password modal
        function showForgotPassword() {
            const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
            modal.show();
        }

        // Show terms modal (placeholder)
        function showTerms() {
            alert('Terms and Conditions will be displayed here');
        }

        // Initialize authentication manager
        const authManager = new AuthManager();

        // Check URL parameters for actions
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action');

        if (action === 'register') {
            switchTab('register');
        } else if (action === 'reset') {
            const token = urlParams.get('token');
            if (token) {
                // Handle password reset
                console.log('Password reset token:', token);
            }
        }
    </script>
</body>
</html>