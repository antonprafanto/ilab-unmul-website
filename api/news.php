<?php
// api/news.php
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
        requireAdmin();
        
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
                $_SESSION['user_id'],
                $data['category'] ?? 'general',
                $data['status'] ?? 'published',
                $data['publish_date'] ?? date('Y-m-d')
            ]);

            if($result) {
                $news_id = $this->db->lastInsertId();
                logActivity($_SESSION['user_id'], 'CREATE_NEWS', "Created news: {$data['title']}");
                
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

    // Update news (Admin only)
    public function updateNews($id, $data) {
        requireAdmin();

        try {
            $fields = [];
            $params = [];
            
            $allowed_fields = ['title', 'content', 'excerpt', 'image_url', 'category', 'status', 'publish_date'];
            
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
                logActivity($_SESSION['user_id'], 'UPDATE_NEWS', "Updated news ID: $id");
                
                return [
                    'success' => true,
                    'message' => 'News updated successfully'
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }

    // Delete news (Admin only)
    public function deleteNews($id) {
        requireAdmin();

        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([$id]);
            
            if($result) {
                logActivity($_SESSION['user_id'], 'DELETE_NEWS', "Deleted news ID: $id");
                
                return [
                    'success' => true,
                    'message' => 'News deleted successfully'
                ];
            }
        } catch(PDOException $e) {
            return ['error' => 'Database error: ' . $e->getMessage()];
        }
    }
}

// Handle API requests for news
if(basename($_SERVER['PHP_SELF']) == 'news.php') {
    $method = $_SERVER['REQUEST_METHOD'];
    $news_api = new NewsAPI();

    switch($method) {
        case 'GET':
            if(isset($_GET['id'])) {
                $result = $news_api->getSingleNews($_GET['id']);
            } else {
                $filters = array_intersect_key($_GET, array_flip(['category', 'search', 'limit']));
                $result = $news_api->getNews($filters);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $result = $news_api->createNews($input);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            if(isset($input['id'])) {
                $result = $news_api->updateNews($input['id'], $input);
            } else {
                $result = ['error' => 'News ID is required'];
            }
            break;
            
        case 'DELETE':
            if(isset($_GET['id'])) {
                $result = $news_api->deleteNews($_GET['id']);
            } else {
                $result = ['error' => 'News ID is required'];
            }
            break;
            
        default:
            $result = ['error' => 'Method not allowed'];
    }

    header('Content-Type: application/json');
    echo json_encode($result);
}

?>