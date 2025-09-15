# 🚗 NU Parking Management System

A **web-based system** for managing campus parking spaces.  
Built with **PHP (XAMPP)**, **MySQL**, **HTML**, **CSS**, and **JavaScript**.

---

## ⚙️ Requirements

- **XAMPP** (Apache + MySQL)  
- **Git** (to clone the repository)  
- **Browser** (Chrome, Firefox, etc.)

---

## 📥 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/NeilVB/parking-management-system.git
   cd parking-management-system
   ```

2. **Move the project folder** into your XAMPP `htdocs` directory:
   ```
   C:/xampp/htdocs/parking-management-system
   ```

---

## 🗄 Database Setup

1. Open **phpMyAdmin** at [http://localhost/phpmyadmin](http://localhost/phpmyadmin).  
2. Create a new database:
   ```sql
   CREATE DATABASE parking_spaces;
   ```
3. Inside that database, create the main table:
   ```sql
   CREATE TABLE parking_spaces (
       id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(255) NOT NULL,
       slug VARCHAR(255) NOT NULL UNIQUE,
       max_space INT(11) UNSIGNED NOT NULL,
       used_space INT(11) UNSIGNED NOT NULL DEFAULT 0
   );
   ```

---

## 🔧 Configuration

Edit `controller/connection.php` and ensure it matches your XAMPP credentials:

```php
<?php
$connection = mysqli_connect("localhost", "root", "", "parking_spaces");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
```

---

## ▶️ Run the Project

1. Start **Apache** and **MySQL** from the XAMPP Control Panel.  
2. Open your browser and go to:  
   👉 [http://localhost/parking-management-system/](http://localhost/parking-management-system/)
