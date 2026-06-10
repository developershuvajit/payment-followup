<?php
// install.php - ডাটাবেজ টেবিল তৈরি করার ফাইল
// শুধু একবার রান করাবেন

require_once 'config/database.php';

// vendors টেবিল
$sql_vendors = "CREATE TABLE IF NOT EXISTS vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    company_name VARCHAR(150),
    status TINYINT DEFAULT 1,
    user_type ENUM('super_admin', 'vendor') DEFAULT 'vendor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// customers টেবিল
$sql_customers = "CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendor_id INT NOT NULL,
    party_name VARCHAR(150) NOT NULL,
    whatsapp_number VARCHAR(20) NOT NULL,
    bill_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    due_amount DECIMAL(10,2) NOT NULL,
    message_frequency ENUM('daily', 'weekly', 'monthly', 'custom') DEFAULT 'weekly',
    telecalling_assign TINYINT DEFAULT 0,
    payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    last_reminder_sent DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// payments টেবিল
$sql_payments = "CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    paid_amount DECIMAL(10,2) NOT NULL,
    remaining_due DECIMAL(10,2) NOT NULL,
    payment_date DATE NOT NULL,
    receipt VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// whatsapp_logs টেবিল
$sql_whatsapp_logs = "CREATE TABLE IF NOT EXISTS whatsapp_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_date DATETIME NOT NULL,
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    response TEXT
)";

// ডিফল্ট সুপার অ্যাডমিন যোগ করা
$sql_admin = "INSERT INTO vendors (name, email, password, user_type) 
     SELECT 'Super Admin', 'admin@example.com', MD5('admin123'), 'super_admin'
     WHERE NOT EXISTS (SELECT 1 FROM vendors WHERE email = 'admin@example.com')";

// সব কোয়েরি এক্সিকিউট করা
if(mysqli_query($conn, $sql_vendors)) {
    echo "✅ vendors টেবিল তৈরি হয়েছে<br>";
} else {
    echo "❌ vendors টেবিল error: " . mysqli_error($conn) . "<br>";
}

if(mysqli_query($conn, $sql_customers)) {
    echo "✅ customers টেবিল তৈরি হয়েছে<br>";
} else {
    echo "❌ customers টেবিল error: " . mysqli_error($conn) . "<br>";
}

if(mysqli_query($conn, $sql_payments)) {
    echo "✅ payments টেবিল তৈরি হয়েছে<br>";
} else {
    echo "❌ payments টেবিল error: " . mysqli_error($conn) . "<br>";
}

if(mysqli_query($conn, $sql_whatsapp_logs)) {
    echo "✅ whatsapp_logs টেবিল তৈরি হয়েছে<br>";
} else {
    echo "❌ whatsapp_logs টেবিল error: " . mysqli_error($conn) . "<br>";
}

if(mysqli_query($conn, $sql_admin)) {
    echo "✅ Super Admin যোগ করা হয়েছে<br>";
} else {
    echo "❌ Super Admin error: " . mysqli_error($conn) . "<br>";
}

echo "<hr>";
echo "🔐 সুপার অ্যাডমিন লগিন তথ্য:<br>";
echo "📧 Email: admin@example.com<br>";
echo "🔑 Password: admin123<br>";
echo "<hr>";
echo "<a href='index.php'>Go to Homepage →</a>";

mysqli_close($conn);
?>