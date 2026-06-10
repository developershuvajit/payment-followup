<?php
// vendor/action/delete_customer.php
session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../../login.php");
    exit();
}

if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $customer_id = $_GET['id'];
    $vendor_id = $_SESSION['vendor_id'];
    
    // প্রথমে এই কাস্টমারের সব পেমেন্ট ডিলিট
    $del_payments = "DELETE FROM payments WHERE customer_id = '$customer_id'";
    mysqli_query($conn, $del_payments);
    
    // তারপর কাস্টমার ডিলিট
    $del_query = "DELETE FROM customers WHERE id = '$customer_id' AND vendor_id = '$vendor_id'";
    
    if(mysqli_query($conn, $del_query)) {
        $_SESSION['message'] = "✅ কাস্টমার ডিলিট করা হয়েছে!";
    } else {
        $_SESSION['error'] = "❌ ডিলিট করতে ব্যর্থ হয়েছে!";
    }
}

header("Location: ../customers.php");
exit();
?>