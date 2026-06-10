<?php
// admin/add_vendor.php
session_start();
require_once '../config/database.php';

// লগইন চেক
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'super_admin') {
    header("Location: ../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name'] ?? '');
    
    // ইমেইল চেক করা
    $check_query = "SELECT id FROM vendors WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) > 0) {
        $_SESSION['error'] = "এই ইমেইল уже ব্যবহার করা হয়েছে!";
    } else {
        $insert_query = "INSERT INTO vendors (name, email, password, phone, company_name, user_type, status) 
                         VALUES ('$name', '$email', '$password', '$phone', '$company_name', 'vendor', 1)";
        
        if(mysqli_query($conn, $insert_query)) {
            $_SESSION['message'] = "✅ নতুন ভেন্ডর যোগ করা হয়েছে!";
        } else {
            $_SESSION['error'] = "❌ ভেন্ডর যোগ করতে ব্যর্থ হয়েছে!";
        }
    }
}

header("Location: vendors.php");
exit();
?>