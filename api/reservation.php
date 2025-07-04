<?php
// api/reservation.php
// API untuk Sistem Reservasi ILab UNMUL

require_once 'config.php';

class ReservationAPI {
    private $db;
    private $table_name = "reservations";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Create new reservation
    public function createReservation($data) {
        // Validate required fields
        $required_fields = ['user_id', 'equipment_id', 'title', 'start_date', 'end_date', 'start_time', 'end_time'];
        foreach($required_fields as $field) {
            if(!isset($data[$field]) || empty($data[$field])) {
                return ['error' => "Field $field is required"];
            }
        }

        // Validate date and time formats
        if(!validateDate($data['start_date']) || !validateDate($data['end_date'])) {
            return ['error' => 'Invalid date format'];
        }
        
        if(!validateTime($data['start_time']) || !validateTime($data['end_time'])) {
            return ['error' => 'Invalid time format'];
        }

        // Check equipment availability
        if(!$this->checkAvailability($data['equipment_id'], $data['start_date'], $data['end_date'], $data['start_time'], $data['end_time'])) {
            return ['error' => 'Equipment not available for the selected time slot'];
        }

        // Calculate total hours and cost
        $start_datetime = new DateTime($data['start_date'] . ' ' . $data['start_time']);
        $end_datetime = new DateTime($data['end_date'] . ' ' . $data['end_time']);
        $interval = $start_datetime->diff($end_datetime);
        $total_hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);

        // Get equipment price
        $equipment_price = $this->getEquipmentPrice($data['equipment_id']);
        $total_cost = $total_hours * $equipment_price;

        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (user_id, equipment_id, title, description, start_date, end_date, start_time, end_time, total_hours, total_cost, status, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['user_id'],
                $data['equipment_id'],
                $data['title'],
                $data['description'] ?? '',
                $data['start_date'],
                $data['end_date'],
                $data['start_time'],
                $data['end_time'],
                $total_hours,
                $total_cost
            ]);

            if($result) {
                $reservation_id = $this->db->lastInsertId();
                
                // Send notification email
                $this->sendReservationNotification($reservation_id, 'created');
                
                // Log activity
                logActivity($data['user_id'], 'CREATE_RESERVATION', "Created reservation #$reservation_id");
                
                return [
                    'success' => true,
                    'message' => 'Reservation created successfully',
                    'reservation_id' => $reservation_id,
                    'total_hours' => $total_hours,
                    'total_cost' => $total_cost
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Get reservations
    public function getReservations($filters = []) {
        $query = "SELECT r.*, u.full_name as user_name, e.name as equipment_name, e.category as equipment_category
                  FROM " . $this->table_name . " r
                  LEFT JOIN users u ON r.user_id = u.id
                  LEFT JOIN equipment e ON r.equipment_id = e.id
                  WHERE 1=1";
        
        $params = [];
        
        if(isset($filters['user_id'])) {
            $query .= " AND r.user_id = ?";
            $params[] = $filters['user_id'];
        }
        
        if(isset($filters['equipment_id'])) {
            $query .= " AND r.equipment_id = ?";
            $params[] = $filters['equipment_id'];
        }
        
        if(isset($filters['status'])) {
            $query .= " AND r.status = ?";
            $params[] = $filters['status'];
        }
        
        if(isset($filters['date_from'])) {
            $query .= " AND r.start_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if(isset($filters['date_to'])) {
            $query .= " AND r.end_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        $query .= " ORDER BY r.created_at DESC";
        
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

    // Get single reservation
    public function getReservation($id) {
        try {
            $query = "SELECT r.*, u.full_name as user_name, u.email as user_email, 
                            e.name as equipment_name, e.category as equipment_category, e.location as equipment_location
                      FROM " . $this->table_name . " r
                      LEFT JOIN users u ON r.user_id = u.id
                      LEFT JOIN equipment e ON r.equipment_id = e.id
                      WHERE r.id = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            if($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'data' => $stmt->fetch(PDO::FETCH_ASSOC)
                ];
            } else {
                return ['error' => 'Reservation not found'];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Update reservation status
    public function updateReservationStatus($id, $status, $notes = '') {
        $allowed_statuses = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];
        if(!in_array($status, $allowed_statuses)) {
            return ['error' => 'Invalid status'];
        }

        try {
            $query = "UPDATE " . $this->table_name . " SET status = ?, notes = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$status, $notes, $id]);
            
            if($result) {
                // Send notification email
                $this->sendReservationNotification($id, $status);
                
                // Log activity
                logActivity($_SESSION['user_id'] ?? 0, 'UPDATE_RESERVATION', "Updated reservation #$id status to $status");
                
                return [
                    'success' => true,
                    'message' => 'Reservation status updated successfully'
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Check equipment availability
    private function checkAvailability($equipment_id, $start_date, $end_date, $start_time, $end_time) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE equipment_id = ? 
                  AND status IN ('pending', 'approved')
                  AND (
                    (start_date <= ? AND end_date >= ?) OR
                    (start_date <= ? AND end_date >= ?) OR
                    (start_date >= ? AND end_date <= ?)
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            $equipment_id,
            $start_date, $start_date,
            $end_date, $end_date,
            $start_date, $end_date
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }

    // Get equipment price
    private function getEquipmentPrice($equipment_id) {
        $query = "SELECT price_per_hour FROM equipment WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$equipment_id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['price_per_hour'] : 0;
    }

    // Send reservation notification
    private function sendReservationNotification($reservation_id, $action) {
        $reservation = $this->getReservation($reservation_id);
        if($reservation['success']) {
            $data = $reservation['data'];
            
            $subject = "Reservation " . ucfirst($action) . " - " . $data['title'];
            $message = "
                <h3>Reservation Details</h3>
                <p><strong>Reservation ID:</strong> #$reservation_id</p>
                <p><strong>Title:</strong> {$data['title']}</p>
                <p><strong>Equipment:</strong> {$data['equipment_name']}</p>
                <p><strong>User:</strong> {$data['user_name']}</p>
                <p><strong>Date:</strong> {$data['start_date']} to {$data['end_date']}</p>
                <p><strong>Time:</strong> {$data['start_time']} to {$data['end_time']}</p>
                <p><strong>Status:</strong> " . ucfirst($data['status']) . "</p>
                <p><strong>Total Cost:</strong> Rp " . number_format($data['total_cost'], 0, ',', '.') . "</p>
            ";
            
            if(!empty($data['notes'])) {
                $message .= "<p><strong>Notes:</strong> {$data['notes']}</p>";
            }
            
            sendEmail($data['user_email'], $subject, $message);
        }
    }

    // Get calendar data
    public function getCalendarData($equipment_id = null, $month = null, $year = null) {
        $month = $month ?: date('m');
        $year = $year ?: date('Y');
        
        $query = "SELECT r.*, e.name as equipment_name, e.category as equipment_category
                  FROM " . $this->table_name . " r
                  LEFT JOIN equipment e ON r.equipment_id = e.id
                  WHERE MONTH(r.start_date) = ? AND YEAR(r.start_date) = ?
                  AND r.status IN ('pending', 'approved')";
        
        $params = [$month, $year];
        
        if($equipment_id) {
            $query .= " AND r.equipment_id = ?";
            $params[] = $equipment_id;
        }
        
        $query .= " ORDER BY r.start_date, r.start_time";
        
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

    // Get dashboard statistics
    public function getDashboardStats($user_id = null) {
        try {
            $stats = [];
            
            // Total reservations
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $params = [];
            
            if($user_id) {
                $query .= " WHERE user_id = ?";
                $params[] = $user_id;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['total_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Pending reservations
            $query = "SELECT COUNT(*) as pending FROM " . $this->table_name . " WHERE status = 'pending'";
            if($user_id) {
                $query .= " AND user_id = ?";
                $params = [$user_id];
            } else {
                $params = [];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['pending_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
            
            // Approved reservations
            $query = "SELECT COUNT(*) as approved FROM " . $this->table_name . " WHERE status = 'approved'";
            if($user_id) {
                $query .= " AND user_id = ?";
                $params = [$user_id];
            } else {
                $params = [];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['approved_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['approved'];
            
            // This month's reservations
            $query = "SELECT COUNT(*) as this_month FROM " . $this->table_name . " WHERE MONTH(start_date) = MONTH(NOW()) AND YEAR(start_date) = YEAR(NOW())";
            if($user_id) {
                $query .= " AND user_id = ?";
                $params = [$user_id];
            } else {
                $params = [];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $stats['this_month_reservations'] = $stmt->fetch(PDO::FETCH_ASSOC)['this_month'];
            
            return [
                'success' => true,
                'data' => $stats
            ];
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
}

// Handle API requests
$method = $_SERVER['REQUEST_METHOD'];
$reservation_api = new ReservationAPI();

switch($method) {
    case 'GET':
        if(isset($_GET['action'])) {
            switch($_GET['action']) {
                case 'get':
                    if(isset($_GET['id'])) {
                        $result = $reservation_api->getReservation($_GET['id']);
                    } else {
                        $filters = array_intersect_key($_GET, array_flip(['user_id', 'equipment_id', 'status', 'date_from', 'date_to', 'limit']));
                        $result = $reservation_api->getReservations($filters);
                    }
                    break;
                    
                case 'calendar':
                    $result = $reservation_api->getCalendarData(
                        $_GET['equipment_id'] ?? null,
                        $_GET['month'] ?? null,
                        $_GET['year'] ?? null
                    );
                    break;
                    
                case 'stats':
                    $result = $reservation_api->getDashboardStats(
                        $_GET['user_id'] ?? null
                    );
                    break;
                    
                default:
                    $result = ['error' => 'Invalid action'];
            }
        } else {
            $result = $reservation_api->getReservations();
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        
        if(isset($input['action'])) {
            switch($input['action']) {
                case 'create':
                    $result = $reservation_api->createReservation($input);
                    break;
                    
                case 'update_status':
                    $result = $reservation_api->updateReservationStatus(
                        $input['id'],
                        $input['status'],
                        $input['notes'] ?? ''
                    );
                    break;
                    
                default:
                    $result = ['error' => 'Invalid action'];
            }
        } else {
            $result = $reservation_api->createReservation($input);
        }
        break;
        
    default:
        $result = ['error' => 'Method not allowed'];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($result);
?>