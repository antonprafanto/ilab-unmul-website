<?php
class ContactAPI
{
    private $db;
    private $table_name = "contact_messages";

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Submit contact message
    public function submitMessage($data)
    {
        $required_fields = ['name', 'email', 'subject', 'message'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return ['error' => "Field $field is required"];
            }
        }

        // Validate email format
        if (!validateEmail($data['email'])) {
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

            if ($result) {
                $message_id = $this->db->lastInsertId();

                // Send notification to admin
                $this->sendAdminNotification($data);

                // Send auto-reply to user
                $this->sendAutoReply($data['email'], $data['name']);

                return [
                    'success' => true,
                    'message' => 'Message sent successfully',
                    'message_id' => $message_id
                ];
            }
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Get all contact messages (Admin only)
    public function getMessages($filters = [])
    {
        requireAdmin();

        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query .= " AND status = ?";
            $params[] = $filters['status'];
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $query .= " ORDER BY created_at DESC";

        if (isset($filters['limit'])) {
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
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Update message status (Admin only)
    public function updateMessageStatus($id, $status)
    {
        requireAdmin();

        $allowed_statuses = ['new', 'read', 'replied'];
        if (!in_array($status, $allowed_statuses)) {
            return ['error' => 'Invalid status'];
        }

        try {
            $query = "UPDATE " . $this->table_name . " SET status = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$status, $id]);

            if ($result) {
                logActivity($_SESSION['user_id'], 'UPDATE_MESSAGE_STATUS', "Updated message #$id status to $status");

                return [
                    'success' => true,
                    'message' => 'Message status updated successfully'
                ];
            }
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Delete message (Admin only)
    public function deleteMessage($id)
    {
        requireAdmin();

        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$id]);

            if ($result) {
                logActivity($_SESSION['user_id'], 'DELETE_MESSAGE', "Deleted contact message ID: $id");

                return [
                    'success' => true,
                    'message' => 'Message deleted successfully'
                ];
            }
        } catch (PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Send notification to admin
    private function sendAdminNotification($data)
    {
        $subject = "New Contact Message - " . $data['subject'];
        $message = "
            <h3>New Contact Message Received</h3>
            <p><strong>Name:</strong> {$data['name']}</p>
            <p><strong>Email:</strong> {$data['email']}</p>
            <p><strong>Subject:</strong> {$data['subject']}</p>
            <p><strong>Message:</strong></p>
            <div style='background: #f8f9fa; padding: 15px; border-left: 4px solid #2c5530;'>
                {$data['message']}
            </div>
            <p><strong>Received:</strong> " . date('Y-m-d H:i:s') . "</p>
        ";

        sendEmail(ADMIN_EMAIL, $subject, $message);
    }

    // Send auto-reply to user
    private function sendAutoReply($email, $name)
    {
        $subject = "Thank you for contacting ILab UNMUL";
        $message = "
            <h3>Thank you for your message</h3>
            <p>Dear $name,</p>
            <p>Thank you for contacting Integrated Laboratory UNMUL. We have received your message and will respond as soon as possible.</p>
            <p>Our team typically responds within 24-48 hours during business days.</p>
            <p>If you have urgent matters, please contact us directly at:</p>
            <ul>
                <li>Phone: +62 541 735055</li>
                <li>Email: ilab@unmul.ac.id</li>
            </ul>
            <p>Best regards,<br>ILab UNMUL Team</p>
        ";

        sendEmail($email, $subject, $message);
    }
}

// Handle API requests for contact
if (basename($_SERVER['PHP_SELF']) == 'contact.php') {
    $method = $_SERVER['REQUEST_METHOD'];
    $contact_api = new ContactAPI();

    switch ($method) {
        case 'GET':
            $filters = array_intersect_key($_GET, array_flip(['status', 'search', 'limit']));
            $result = $contact_api->getMessages($filters);
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);

            if (isset($input['action'])) {
                switch ($input['action']) {
                    case 'submit':
                        $result = $contact_api->submitMessage($input);
                        break;

                    case 'update_status':
                        $result = $contact_api->updateMessageStatus($input['id'], $input['status']);
                        break;

                    default:
                        $result = ['error' => 'Invalid action'];
                }
            } else {
                $result = $contact_api->submitMessage($input);
            }
            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                $result = $contact_api->deleteMessage($_GET['id']);
            } else {
                $result = ['error' => 'Message ID is required'];
            }
            break;

        default:
            $result = ['error' => 'Method not allowed'];
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

?>