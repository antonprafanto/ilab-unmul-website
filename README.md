# ?? ILab UNMUL - Integrated Laboratory Website

Sistem website resmi Integrated Laboratory Universitas Mulawarman untuk manajemen laboratorium dan reservasi equipment.

## ?? Fitur Utama

- ? **Homepage Responsif** dengan informasi lengkap ILab
- ? **Sistem Authentication** (Login/Register/Password Reset)
- ? **Equipment Reservation System** dengan approval workflow
- ? **Admin Dashboard** untuk manajemen lengkap
- ? **User Dashboard** untuk mahasiswa dan peneliti
- ? **News Management** untuk publikasi kegiatan
- ? **Contact System** terintegrasi email
- ? **SOP Document Management**
- ? **Responsive Design** untuk semua device

## ??? Teknologi

**Frontend:**
- HTML5, CSS3, JavaScript ES6+
- Bootstrap 5.3.0
- Font Awesome 6.0
- AOS (Animate On Scroll)

**Backend:**
- PHP 8.0+
- MySQL 8.0
- PDO untuk database operations
- RESTful API architecture

## ?? Instalasi Lokal

### Prerequisites
- XAMPP (Apache + MySQL + PHP)
- Git
- Browser modern

### Setup Instructions

1. **Clone repository:**
   ```bash
   git clone https://github.com/yourusername/ilab-unmul-website.git
   cd ilab-unmul-website
   ```

2. **Setup database:**
   - Buka phpMyAdmin: `http://localhost/phpmyadmin`
   - Buat database: `ilab`
   - Import: `database/ilab_unmul.sql`

3. **Konfigurasi database:**
   - Copy `api/config.example.php` ke `api/config.php`
   - Update database credentials untuk lokal

4. **Akses website:**
   - Homepage: `http://localhost/ilab-unmul-website/`
   - Login: `http://localhost/ilab-unmul-website/login.php`
   - Admin: `http://localhost/ilab-unmul-website/admin/dashboard.php`

## ?? Deployment

### Server Requirements
- PHP 8.0+
- MySQL 8.0+
- Apache/Nginx
- SSL Certificate
- SFTP Access

## ?? Database Schema

### Main Tables:
- `users` - User management dan authentication
- `equipment` - Data peralatan laboratorium
- `reservations` - Sistem reservasi dengan status tracking
- `news` - Sistem berita dan pengumuman
- `contact_messages` - Pesan dari contact form
- `sop_documents` - Dokumen SOP dan prosedur

## ?? Security Features

- Password hashing dengan bcrypt
- SQL injection protection dengan PDO
- XSS protection dengan input sanitization
- File upload validation
- Session security

## ?? User Roles

**Admin:**
- Dashboard dengan statistik lengkap
- Equipment management (CRUD)
- Reservation approval/rejection
- User management
- News dan content management

**User (Mahasiswa/Peneliti):**
- Equipment browsing dan reservation
- Reservation history tracking
- Profile management
- Dashboard personal

## ?? Design System

**Color Palette:**
- Primary: `#2c5530` (UNMUL Green)
- Secondary: `#f8f9fa` (Light Gray)
- Accent: `#ffc107` (Golden Yellow)

## ?? Support

**Technical Issues:**
- Email: admin@ilab.unmul.ac.id
- Phone: +62 541 735055

## ?? License

This project is licensed under the MIT License.

## ?? Acknowledgments

- Universitas Mulawarman
- UPT Teknologi Informasi dan Komunikasi UNMUL
- Integrated Laboratory UNMUL Team

---

**© 2024 Integrated Laboratory - Universitas Mulawarman**
