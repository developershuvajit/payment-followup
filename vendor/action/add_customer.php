<?php
// vendor/action/add_customer.php
session_start();
require_once '../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vendor_id = $_SESSION['vendor_id'];
    $party_name = mysqli_real_escape_string($conn, $_POST['party_name']);
    $whatsapp_number = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);
    $bill_date = $_POST['bill_date'];
    $amount = $_POST['amount'];
    $due_amount = $_POST['amount']; // প্রাথমিকভাবে মোট টাকাই বাকি থাকবে
    $message_frequency = $_POST['message_frequency'];
    $telecalling_assign = $_POST['telecalling_assign'] ?? 0;
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    $insert_query = "INSERT INTO customers (vendor_id, party_name, whatsapp_number, bill_date, amount, due_amount, message_frequency, telecalling_assign, payment_status, notes) 
                     VALUES ('$vendor_id', '$party_name', '$whatsapp_number', '$bill_date', '$amount', '$due_amount', '$message_frequency', '$telecalling_assign', 'unpaid', '$notes')";
    
    if(mysqli_query($conn, $insert_query)) {
        $_SESSION['message'] = "✅ নতুন কাস্টমার যোগ করা হয়েছে!";
    } else {
        $_SESSION['error'] = "❌ যোগ করতে ব্যর্থ: " . mysqli_error($conn);
    }
}

header("Location: ../customers.php");
exit();
?>