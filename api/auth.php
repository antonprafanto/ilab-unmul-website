<?php
// api/auth.php - Clean Version
// API untuk Authentication System ILab UNMUL

// Turn off all error output to prevent HTML in JSON response
error_reporting(0);
ini_set('display_errors', 0);

require_once 'config.php';

class AuthAPI {
    private $db;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // User registration
    public function register($data) {
        $required_fields = ['username', 'email', 'password', 'full_name', 'institution'];
        foreach($required_fields as $field) {
            if(!isset($data[$field]) || empty($data[$field])) {
                return ['error' => "Field $field is required"];
            }
        }

        // Validate email format
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format'];
        }

        // Check if username or email already exists
        if($this->userExists($data['username'], $data['email'])) {
            return ['error' => 'Username or email already exists'];
        }

        // Validate password strength
        if(strlen($data['password']) < 6) {
            return ['error' => 'Password must be at least 6 characters long'];
        }

        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (username, email, password, full_name, institution, faculty, phone, role, status, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 'active', NOW())";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['username'],
                $data['email'],
                $hashed_password,
                $data['full_name'],
                $data['institution'],
                $data['faculty'] ?? '',
                $data['phone'] ?? ''
            ]);

            if($result) {
                $user_id = $this->db->lastInsertId();
                
                return [
                    'success' => true,
                    'message' => 'Registration successful',
                    'user_id' => $user_id
                ];
            } else {
                return ['error' => 'Registration failed'];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error occurred'];
        }
    }

    // User login
    public function login($username, $password) {
        try {
            $query = "SELECT id, username, email, password, full_name, role, status FROM " . $this->table_name . " WHERE username = ? AND status = 'active'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username]);
            
            if($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $user['password'])) {
                    // Start session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['full_name'] = $user['full_name'];
                    
                    // Remove password from response
                    unset($user['password']);
                    
                    return [
                        'success' => true,
                        'message' => 'Login successful',
                        'user' => $user
                    ];
                }
            }
            
            return ['error' => 'Invalid username or password'];
        } catch(PDOException $e) {
            return ['error' => 'Login failed'];
        }
    }

    // Check if user exists
    private function userExists($username, $email) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE username = ? OR email = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$username, $email]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}

// Clean output buffer to prevent any HTML output
if (ob_get_length()) {
    ob_clean();
}

// Set JSON header
header('Content-Type: application/json');

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$auth_api = new AuthAPI();

try {
    switch($method) {
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(isset($input['action'])) {
                switch($input['action']) {
                    case 'register':
                        $result = $auth_api->register($input);
                        break;
                        
                    case 'login':
                        $result = $auth_api->login($input['username'], $input['password']);
                        break;
                        
                    default:
                        $result = ['error' => 'Invalid action'];
                }
            } else {
                $result = ['error' => 'Action is required'];
            }
            break;
            
        default:
            $result = ['error' => 'Method not allowed'];
    }
} catch(Exception $e) {
    $result = ['error' => 'Server error occurred'];
}

// Return clean JSON response
echo json_encode($result);
exit;
?>