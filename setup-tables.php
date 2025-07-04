<?php
// setup-tables.php
// Script untuk membuat tabel yang mungkin belum ada

require_once 'api/config.php';

echo "<h2>??? Setup Missing Tables</h2>";

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("? Database connection failed!");
}

echo "? Database connected successfully!<br><br>";

// Array of table creation queries
$tables = [
    'contact_messages' => "
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ",
    'news' => "
        CREATE TABLE IF NOT EXISTS news (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            content TEXT NOT NULL,
            excerpt TEXT,
            image_url VARCHAR(255),
            author_id INT,
            category VARCHAR(50),
            status ENUM('draft', 'published') DEFAULT 'draft',
            publish_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ",
    'sop_documents' => "
        CREATE TABLE IF NOT EXISTS sop_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            file_url VARCHAR(255) NOT NULL,
            category VARCHAR(50),
            version VARCHAR(20),
            description TEXT,
            upload_date DATE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ",
    'activities' => "
        CREATE TABLE IF NOT EXISTS activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            activity_date DATE,
            location VARCHAR(100),
            participants INT,
            category VARCHAR(50),
            image_url VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    "
];

// Create tables
foreach ($tables as $table_name => $sql) {
    try {
        $db->exec($sql);
        echo "? Table '$table_name' created/verified successfully<br>";
    } catch (PDOException $e) {
        echo "? Error creating table '$table_name': " . $e->getMessage() . "<br>";
    }
}

echo "<br><h3>?? Table Status Check:</h3>";

// Check existing tables
try {
    $stmt = $db->query("SHOW TABLES");
    $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Existing tables: " . implode(", ", $existing_tables) . "<br><br>";
    
    // Check each important table
    $required_tables = ['users', 'equipment', 'reservations', 'news', 'contact_messages'];
    
    foreach ($required_tables as $table) {
        if (in_array($table, $existing_tables)) {
            // Count records
            $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "? Table '$table' exists with $count records<br>";
        } else {
            echo "? Table '$table' is missing!<br>";
        }
    }
} catch (PDOException $e) {
    echo "? Error checking tables: " . $e->getMessage();
}

echo "<br><h3>?? Sample Data Insertion:</h3>";

// Insert sample news if table is empty
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM news");
    $news_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($news_count == 0) {
        $sample_news = [
            [
                'title' => 'Workshop GC-MS dan LC-MS/MS',
                'content' => 'Workshop "Discover the Selectivity and Provided Analysis with GC-MS, LC-MS/MS, and AAS" untuk dosen dan laboran dari berbagai fakultas.',
                'excerpt' => 'Workshop analisis menggunakan GC-MS dan LC-MS/MS untuk meningkatkan kemampuan penelitian.',
                'category' => 'workshop',
                'publish_date' => '2024-01-30'
            ],
            [
                'title' => 'Workshop Real-time PCR',
                'content' => 'Workshop "Real time PCR and Its Applications" untuk meningkatkan pemahaman teknik PCR dalam penelitian molekuler.',
                'excerpt' => 'Pelatihan teknik PCR real-time untuk aplikasi penelitian molekuler.',
                'category' => 'workshop', 
                'publish_date' => '2024-02-01'
            ],
            [
                'title' => 'Workshop FTIR Spectrophotometer',
                'content' => 'Workshop "Spektrometer FTIR, Pengertian, Fungsi dan Prinsip Kerja" untuk memahami aplikasi spektroskopi inframerah.',
                'excerpt' => 'Memahami prinsip kerja dan aplikasi spektrometer FTIR.',
                'category' => 'workshop',
                'publish_date' => '2024-02-16'
            ]
        ];
        
        $insert_sql = "INSERT INTO news (title, content, excerpt, category, status, publish_date, author_id, created_at) VALUES (?, ?, ?, ?, 'published', ?, 1, NOW())";
        $stmt = $db->prepare($insert_sql);
        
        foreach ($sample_news as $news) {
            $stmt->execute([
                $news['title'],
                $news['content'], 
                $news['excerpt'],
                $news['category'],
                $news['publish_date']
            ]);
        }
        
        echo "? Sample news data inserted successfully<br>";
    } else {
        echo "?? News table already has data ($news_count records)<br>";
    }
} catch (PDOException $e) {
    echo "? Error inserting sample data: " . $e->getMessage() . "<br>";
}

echo "<br><h3>?? API Endpoints Test:</h3>";

// Test API endpoints
$api_endpoints = [
    'contact.php',
    'news.php', 
    'auth.php',
    'equipment.php',
    'reservation.php'
];

foreach ($api_endpoints as $endpoint) {
    $url = 'https://ilab.unmul.ac.id/api/' . $endpoint;
    echo "Testing: <a href='$url' target='_blank'>$endpoint</a> - ";
    
    // Simple test with curl or file_get_contents
    $context = stream_context_create([
        'http' => [
            'timeout' => 5,
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response !== false) {
        $json = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            echo "? Returns valid JSON<br>";
        } else {
            echo "?? Response received but not valid JSON<br>";
        }
    } else {
        echo "? No response<br>";
    }
}

echo "<br><h3>? Setup Complete!</h3>";
echo "You can now test the dashboard again. If errors persist, check the browser console for specific API endpoints that are failing.";
?>