<?php
// api/news.php - FIXED FINAL VERSION
// API untuk News Management ILab UNMUL

require_once 'config.php';

class NewsAPI {
    private $db;
    private $table_name = "news";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Get all news
    public function getNews($filters = []) {
        try {
            $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                      LEFT JOIN users u ON n.author_id = u.id 
                      WHERE n.status = 'published'";
            $params = [];
            
            if(isset($filters['category']) && !empty($filters['category'])) {
                $query .= " AND n.category = ?";
                $params[] = $filters['category'];
            }
            
            if(isset($filters['search']) && !empty($filters['search'])) {
                $query .= " AND (n.title LIKE ? OR n.content LIKE ?)";
                $searchTerm = "%" . $filters['search'] . "%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            $query .= " ORDER BY n.publish_date DESC, n.created_at DESC";
            
            // FIX: Proper limit handling
            if(isset($filters['limit']) && is_numeric($filters['limit']) && $filters['limit'] > 0) {
                $query .= " LIMIT " . intval($filters['limit']);
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

    // Get single news
    public function getSingleNews($id) {
        try {
            $query = "SELECT n.*, u.full_name as author_name FROM " . $this->table_name . " n 
                      LEFT JOIN users u ON n.author_id = u.id 
                      WHERE n.id = ? AND n.status = 'published'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$id]);
            
            if($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'data' => $stmt->fetch(PDO::FETCH_ASSOC)
                ];
            } else {
                return ['error' => 'News not found'];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Create news (Admin only)
    public function createNews($data) {
        $required_fields = ['title', 'content'];
        foreach($required_fields as $field) {
            if(!isset($data[$field]) || empty($data[$field])) {
                return ['error' => "Field $field is required"];
            }
        }

        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (title, content, excerpt, image_url, author_id, category, status, publish_date, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $excerpt = $data['excerpt'] ?? substr(strip_tags($data['content']), 0, 200) . '...';
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                $data['title'],
                $data['content'],
                $excerpt,
                $data['image_url'] ?? '',
                $data['author_id'] ?? 1,
                $data['category'] ?? 'general',
                $data['status'] ?? 'published',
                $data['publish_date'] ?? date('Y-m-d')
            ]);

            if($result) {
                $news_id = $this->db->lastInsertId();
                
                return [
                    'success' => true,
                    'message' => 'News created successfully',
                    'news_id' => $news_id
                ];
            }
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
    $news_api = new NewsAPI();

    switch($method) {
        case 'GET':
            if(isset($_GET['id'])) {
                $result = $news_api->getSingleNews($_GET['id']);
            } else {
                // Safe parameter extraction
                $filters = [];
                if(isset($_GET['category'])) $filters['category'] = $_GET['category'];
                if(isset($_GET['search'])) $filters['search'] = $_GET['search'];
                if(isset($_GET['limit']) && is_numeric($_GET['limit'])) $filters['limit'] = $_GET['limit'];
                
                $result = $news_api->getNews($filters);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $result = $news_api->createNews($input);
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