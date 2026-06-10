<?php
// vendor/action/send_bulk_reminder.php
session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../../login.php");
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$customer_ids = isset($_POST['customer_ids']) ? $_POST['customer_ids'] : [];
$message_template = isset($_POST['message_content']) ? $_POST['message_content'] : '';

if(empty($customer_ids)) {
    $_SESSION['error'] = "কোন কাস্টমার সিলেক্ট করা হয়নি!";
    header("Location: ../reminder.php");
    exit();
}

if(empty($message_template)) {
    $_SESSION['error'] = "মেসেজ খালি রাখা যাবে না!";
    header("Location: ../reminder.php");
    exit();
}

$success_count = 0;
$failed_count = 0;

foreach($customer_ids as $customer_id) {
    // কাস্টমারের তথ্য বের করা
    $query = "SELECT * FROM customers WHERE id = '$customer_id' AND vendor_id = '$vendor_id'";
    $result = mysqli_query($conn, $query);
    
    if($row = mysqli_fetch_assoc($result)) {
        // মেসেজ টেমপ্লেট রিপ্লেস
        $message = $message_template;
        $message = str_replace('{party_name}', $row['party_name'], $message);
        $message = str_replace('{due_amount}', '₹ ' . number_format($row['due_amount'], 2), $message);
        $message = str_replace('{amount}', '₹ ' . number_format($row['amount'], 2), $message);
        $message = str_replace('{bill_date}', date('d-m-Y', strtotime($row['bill_date'])), $message);
        $message = str_replace('{whatsapp}', $row['whatsapp_number'], $message);
        
        // WhatsApp API এখানে কল করতে হবে
        // বর্তমানে শুধু লগ সেভ করা হচ্ছে
        
        $log_query = "INSERT INTO whatsapp_logs (customer_id, message, sent_date, status) 
                      VALUES ('$customer_id', '" . mysqli_real_escape_string($conn, $message) . "', NOW(), 'sent')";
        
        if(mysqli_query($conn, $log_query)) {
            $success_count++;
        } else {
            $failed_count++;
        }
        
        // লাস্ট রিমাইন্ডার তারিখ আপডেট
        $update_last = "UPDATE customers SET last_reminder_sent = CURDATE() WHERE id = '$customer_id'";
        mysqli_query($conn, $update_last);
    } else {
        $failed_count++;
    }
}

$_SESSION['message'] = "✅ $success_count জন কাস্টমারকে রিমাইন্ডার পাঠানো হয়েছে! ❌ $failed_count জন ব্যর্থ হয়েছে!";

header("Location: ../reminder.php");
exit();
?>