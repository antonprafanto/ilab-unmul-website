<?php
// api/contact.php
// API untuk Contact Messages ILab UNMUL

require_once 'config.php';

class ContactAPI {
    private $db;
    private $table_name = "contact_messages";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Submit contact message
    public function submitMessage($data) {
        $required_fields = ['name', 'email', 'subject', 'message'];
        foreach($required_fields as $field) {
            if(!isset($data[$field]) || empty($data[$field])) {
                return ['error' => "Field $field is required"];
            }
        }

        // Validate email format
        if(!validateEmail($data['email'])) {
            return ['error' => 'Invalid email format'];
        }

        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (name, email, subject, message, status, created_at) 
                     VALUES (?, ?, ?, ?, 'new', NOW())";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                sanitize_input($data['name']),
                sanitize_input($data['email']),
                sanitize_input($data['subject']),
                sanitize_input($data['message'])
            ]);

            if($result) {
                $message_id = $this->db->lastInsertId();
                
                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'message_id' => $message_id
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Get all contact messages (Admin only)
    public function getMessages($filters = []) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
            $params = [];
            
            if(isset($filters['status']) && !empty($filters['status'])) {
                $query .= " AND status = ?";
                $params[] = $filters['status'];
            }
            
            if(isset($filters['search']) && !empty($filters['search'])) {
                $query .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
                $searchTerm = "%" . $filters['search'] . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $query .= " ORDER BY created_at DESC";
            
            if(isset($filters['limit'])) {
                $query .= " LIMIT ?";
                $params[] = $filters['limit'];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
}

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Handle API requests
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $contact_api = new ContactAPI();
    
    switch($method) {
        case 'GET':
            $filters = array_intersect_key($_GET, array_flip(['status', 'search', 'limit']));
            $result = $contact_api->getMessages($filters);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(isset($input['action'])) {
                switch($input['action']) {
                    case 'submit':
                        $result = $contact_api->submitMessage($input);
                        break;
                        
                    default:
                        $result = ['error' => 'Invalid action'];
                }
            } else {
                $result = $contact_api->submitMessage($input);
            }
            break;
            
        default:
            $result = ['error' => 'Method not allowed'];
    }
} catch(Exception $e) {
    $result = ['error' => 'Server error: ' . $e->getMessage()];
}

// Return JSON response
echo json_encode($result);
?>