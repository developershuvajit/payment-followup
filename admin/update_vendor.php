<?php
// admin/update_vendor.php
session_start();
require_once '../config/database.php';

// লগইন চেক
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'super_admin') {
    header("Location: ../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_id = $_POST['vendor_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name'] ?? '');
    
    // পাসওয়ার্ড আপডেট চেক
    if(!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $update_query = "UPDATE vendors SET name='$name', email='$email', password='$password', phone='$phone', company_name='$company_name' WHERE id=$vendor_id";
    } else {
        $update_query = "UPDATE vendors SET name='$name', email='$email', phone='$phone', company_name='$company_name' WHERE id=$vendor_id";
    }
    
    if(mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = "✅ ভেন্ডর আপডেট করা হয়েছে!";
    } else {
        $_SESSION['error'] = "❌ আপডেট করতে ব্যর্থ হয়েছে!";
    }
}

header("Location: vendors.php");
exit();
?>