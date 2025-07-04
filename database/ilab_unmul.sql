-- Database Schema untuk ILab UNMUL

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    institution VARCHAR(100),
    faculty VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'user', 'staff') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Equipment/Peralatan
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    category VARCHAR(50),
    description TEXT,
    specifications TEXT,
    image_url VARCHAR(255),
    status ENUM('available', 'maintenance', 'out_of_order') DEFAULT 'available',
    location VARCHAR(100),
    price_per_hour DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Reservations
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    equipment_id INT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    total_hours INT,
    total_cost DECIMAL(10,2),
    status ENUM('pending', 'approved', 'rejected', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (equipment_id) REFERENCES equipment(id)
);

-- Tabel News/Berita
CREATE TABLE news (
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Tabel SOP Documents
CREATE TABLE sop_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    version VARCHAR(20),
    description TEXT,
    upload_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Contact Messages
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Activities/Kegiatan
CREATE TABLE activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    activity_date DATE,
    location VARCHAR(100),
    participants INT,
    category VARCHAR(50),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample data
INSERT INTO users (username, email, password, full_name, institution, role) VALUES
('admin', 'admin@ilab.unmul.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'UNMUL', 'admin');

INSERT INTO equipment (name, code, category, description, specifications, price_per_hour) VALUES
('GC-MS (Gas Chromatography-Mass Spectrometry)', 'GCMS-001', 'Analytical', 'Instrumen untuk analisis senyawa organik', 'Shimadzu GCMS-QP2010 Ultra', 150000),
('LC-MS/MS (Liquid Chromatography-Mass Spectrometry)', 'LCMS-001', 'Analytical', 'Instrumen untuk analisis senyawa farmasi', 'Waters Acquity UPLC', 200000),
('FTIR Spectrophotometer', 'FTIR-001', 'Analytical', 'Spektrometer untuk analisis gugus fungsi', 'Shimadzu IRTracer-100', 100000),
('Freeze Dryer', 'FD-001', 'Preparation', 'Pengering beku untuk preparasi sampel', 'Labconco FreeZone 2.5L', 75000),
('Real-time PCR', 'PCR-001', 'Molecular', 'Mesin PCR untuk analisis molekuler', 'Applied Biosystems QuantStudio 3', 180000),
('Laminar Air Flow', 'LAF-001', 'Safety', 'Kabinet biosafety untuk kultur sel', 'Esco Airstream Class II BSC', 50000);