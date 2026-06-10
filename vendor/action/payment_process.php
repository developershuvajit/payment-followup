<?php
// vendor/action/payment_process.php
session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = $_POST['customer_id'];
    $vendor_id = $_SESSION['vendor_id'];
    $paid_amount = $_POST['paid_amount'];
    $payment_date = $_POST['payment_date'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes'] ?? '');
    
    // কাস্টমারের বর্তমান তথ্য বের করা
    $customer_query = "SELECT * FROM customers WHERE id = '$customer_id' AND vendor_id = '$vendor_id'";
    $customer_result = mysqli_query($conn, $customer_query);
    
    if(mysqli_num_rows($customer_result) > 0) {
        $customer = mysqli_fetch_assoc($customer_result);
        $current_due = $customer['due_amount'];
        $new_due = $current_due - $paid_amount;
        
        if($new_due < 0) $new_due = 0;
        
        // পেমেন্ট স্ট্যাটাস নির্ধারণ
        if($new_due <= 0) {
            $payment_status = 'paid';
        } elseif($new_due < $customer['amount']) {
            $payment_status = 'partial';
        } else {
            $payment_status = 'unpaid';
        }
        
        // পেমেন্ট ইনসার্ট
        $insert_payment = "INSERT INTO payments (customer_id, paid_amount, remaining_due, payment_date, notes) 
                           VALUES ('$customer_id', '$paid_amount', '$new_due', '$payment_date', '$notes')";
        
        if(mysqli_query($conn, $insert_payment)) {
            // কাস্টমার আপডেট
            $update_customer = "UPDATE customers SET due_amount = '$new_due', payment_status = '$payment_status' WHERE id = '$customer_id'";
            mysqli_query($conn, $update_customer);
            
            $_SESSION['message'] = "✅ ₹ " . number_format($paid_amount, 2) . " পেমেন্ট সংগ্রহ করা হয়েছে! বাকি আছে: ₹ " . number_format($new_due, 2);
        } else {
            $_SESSION['error'] = "❌ পেমেন্ট সংরক্ষণ করতে ব্যর্থ!";
        }
    } else {
        $_SESSION['error'] = "❌ কাস্টমার পাওয়া যায়নি!";
    }
}

header("Location: ../payment_collect.php?id=$customer_id");
exit();
?>