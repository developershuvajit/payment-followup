<?php
// vendor/action/update_customer.php
session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $vendor_id = $_SESSION['vendor_id'];
    
    $party_name = mysqli_real_escape_string($conn, $_POST['party_name']);
    $whatsapp_number = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);
    $bill_date = $_POST['bill_date'];
    $amount = $_POST['amount'];
    $due_amount = $_POST['due_amount'];
    $message_frequency = $_POST['message_frequency'];
    $telecalling_assign = $_POST['telecalling_assign'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    // পেমেন্ট স্ট্যাটাস ক্যালকুলেশন
    if($due_amount <= 0) {
        $payment_status = 'paid';
    } elseif($due_amount < $amount) {
        $payment_status = 'partial';
    } else {
        $payment_status = 'unpaid';
    }
    
    $update_query = "UPDATE customers SET 
                     party_name='$party_name', 
                     whatsapp_number='$whatsapp_number', 
                     bill_date='$bill_date', 
                     amount='$amount', 
                     due_amount='$due_amount', 
                     message_frequency='$message_frequency', 
                     telecalling_assign='$telecalling_assign',
                     payment_status='$payment_status',
                     notes='$notes'
                     WHERE id='$customer_id' AND vendor_id='$vendor_id'";
    
    if(mysqli_query($conn, $update_query)) {
        $_SESSION['message'] = "✅ কাস্টমার আপডেট করা হয়েছে!";
    } else {
        $_SESSION['error'] = "❌ আপডেট করতে ব্যর্থ!";
    }
}

header("Location: ../customers.php");
exit();
?>