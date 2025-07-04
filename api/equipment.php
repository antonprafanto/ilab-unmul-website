<?php
// api/equipment.php
// API untuk Equipment Management ILab UNMUL

require_once 'config.php';

class EquipmentAPI {
    private $db;
    private $table_name = "equipment";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all equipment
    public function getEquipment($filters = []) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];
        
        if(isset($filters['category']) && !empty($filters['category'])) {
            $query .= " AND category = ?";
            $params[] = $filters['category'];
        }
        
        if(isset($filters['status']) && !empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        if(isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (name LIKE ? OR description LIKE ? OR specifications LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $query .= " ORDER BY name ASC";
        
        if(isset($filters['limit'])) {
            $query .= " LIMIT ?";
            $params[] = $filters['limit'];
        }
        
        try {
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

    // Get single equipment
    public function getSingleEquipment($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            if($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'data' => $stmt->fetch(PDO::FETCH_ASSOC)
                ];
            } else {
                return ['error' => 'Equipment not found'];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Create equipment (Admin only)
    public function createEquipment($data) {
        requireAdmin();
        
        $required_fields = ['name', 'code', 'category', 'price_per_hour'];
        foreach($required_fields as $field) {
            if(!isset($data[$field]) || empty($data[$field])) {
                return ['error' => "Field $field is required"];
            }
        }

        // Check if code already exists
        if($this->checkCodeExists($data['code'])) {
            return ['error' => 'Equipment code already exists'];
        }

        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (name, code, category, description, specifications, image_url, status, location, price_per_hour, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['name'],
                $data['code'],
                $data['category'],
                $data['description'] ?? '',
                $data['specifications'] ?? '',
                $data['image_url'] ?? '',
                $data['status'] ?? 'available',
                $data['location'] ?? '',
                $data['price_per_hour']
            ]);

            if($result) {
                $equipment_id = $this->db->lastInsertId();
                logActivity($_SESSION['user_id'], 'CREATE_EQUIPMENT', "Created equipment: {$data['name']}");
                
                return [
                    'success' => true,
                    'message' => 'Equipment created successfully',
                    'equipment_id' => $equipment_id
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Update equipment (Admin only)
    public function updateEquipment($id, $data) {
        requireAdmin();

        try {
            $fields = [];
            $params = [];
            
            $allowed_fields = ['name', 'code', 'category', 'description', 'specifications', 'image_url', 'status', 'location', 'price_per_hour'];
            
            foreach($allowed_fields as $field) {
                if(isset($data[$field])) {
                    $fields[] = "$field = ?";
                    $params[] = $data[$field];
                }
            }
            
            if(empty($fields)) {
                return ['error' => 'No fields to update'];
            }
            
            $fields[] = "updated_at = NOW()";
            $params[] = $id;
            
            $query = "UPDATE " . $this->table_name . " SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($params);
            
            if($result) {
                logActivity($_SESSION['user_id'], 'UPDATE_EQUIPMENT', "Updated equipment ID: $id");
                
                return [
                    'success' => true,
                    'message' => 'Equipment updated successfully'
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Delete equipment (Admin only)
    public function deleteEquipment($id) {
        requireAdmin();

        // Check if equipment has active reservations
        if($this->hasActiveReservations($id)) {
            return ['error' => 'Cannot delete equipment with active reservations'];
        }

        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$id]);
            
            if($result) {
                logActivity($_SESSION['user_id'], 'DELETE_EQUIPMENT', "Deleted equipment ID: $id");
                
                return [
                    'success' => true,
                    'message' => 'Equipment deleted successfully'
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Get equipment categories
    public function getCategories() {
        try {
            $query = "SELECT DISTINCT category FROM " . $this->table_name . " WHERE category IS NOT NULL AND category != '' ORDER BY category";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            return [
                'success' => true,
                'data' => $categories
            ];
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Get equipment statistics
    public function getStatistics() {
        try {
            $stats = [];
            
            // Total equipment
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Available equipment
            $query = "SELECT COUNT(*) as available FROM " . $this->table_name . " WHERE status = 'available'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['available'] = $stmt->fetch(PDO::FETCH_ASSOC)['available'];
            
            // Maintenance equipment
            $query = "SELECT COUNT(*) as maintenance FROM " . $this->table_name . " WHERE status = 'maintenance'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['maintenance'] = $stmt->fetch(PDO::FETCH_ASSOC)['maintenance'];
            
            // Out of order equipment
            $query = "SELECT COUNT(*) as out_of_order FROM " . $this->table_name . " WHERE status = 'out_of_order'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['out_of_order'] = $stmt->fetch(PDO::FETCH_ASSOC)['out_of_order'];
            
            // Equipment by category
            $query = "SELECT category, COUNT(*) as count FROM " . $this->table_name . " GROUP BY category ORDER BY count DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'data' => $stats
            ];
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Upload equipment image
    public function uploadImage($file) {
        requireAdmin();
        
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $filename = uploadFile($file, $allowed_types);
        
        if($filename) {
            return [
                'success' => true,
                'filename' => $filename,
                'url' => 'uploads/images/' . $filename
            ];
        } else {
            return ['error' => 'Failed to upload image'];
        }
    }

    // Check if equipment code exists
    private function checkCodeExists($code, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE code = ?";
        $params = [$code];
        
        if($exclude_id) {
            $query .= " AND id != ?";
            $params[] = $exclude_id;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // Check if equipment has active reservations
    private function hasActiveReservations($equipment_id) {
        $query = "SELECT COUNT(*) as count FROM reservations WHERE equipment_id = ? AND status IN ('pending', 'approved')";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$equipment_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$equipment_api = new EquipmentAPI();

switch($method) {
    case 'GET':
        if(isset($_GET['action'])) {
            switch($_GET['action']) {
                case 'get':
                    if(isset($_GET['id'])) {
                        $result = $equipment_api->getSingleEquipment($_GET['id']);
                    } else {
                        $filters = array_intersect_key($_GET, array_flip(['category', 'status', 'search', 'limit']));
                        $result = $equipment_api->getEquipment($filters);
                    }
                    break;
                    
                case 'categories':
                    $result = $equipment_api->getCategories();
                    break;
                    
                case 'statistics':
                    $result = $equipment_api->getStatistics();
                    break;
                    
                default:
                    $result = ['error' => 'Invalid action'];
            }
        } else {
            $filters = array_intersect_key($_GET, array_flip(['category', 'status', 'search', 'limit']));
            $result = $equipment_api->getEquipment($filters);
        }
        break;
        
    case 'POST':
        if(isset($_FILES['image'])) {
            $result = $equipment_api->uploadImage($_FILES['image']);
        } else {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if(isset($input['action'])) {
                switch($input['action']) {
                    case 'create':
                        $result = $equipment_api->createEquipment($input);
                        break;
                        
                    default:
                        $result = ['error' => 'Invalid action'];
                }
            } else {
                $result = $equipment_api->createEquipment($input);
            }
        }
        break;
        
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if(isset($input['id'])) {
            $result = $equipment_api->updateEquipment($input['id'], $input);
        } else {
            $result = ['error' => 'Equipment ID is required'];
        }
        break;
        
    case 'DELETE':
        if(isset($_GET['id'])) {
            $result = $equipment_api->deleteEquipment($_GET['id']);
        } else {
            $result = ['error' => 'Equipment ID is required'];
        }
        break;
        
    default:
        $result = ['error' => 'Method not allowed'];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
?>