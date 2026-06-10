<?php
// vendor/action/send_reminder.php
session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: 017login.php");
    exit();
}

if(isset($_GET['id'])) {
    $customer_id = $_GET['id'];
    $vendor_id = $_SESSION['vendor_id'];
    
    $customer_query = "SELECT * FROM customers WHERE id = '$customer_id' AND vendor_id = '$vendor_id'";
    $customer_result = mysqli_query($conn, $customer_query);
    
    if(mysqli_num_rows($customer_result) > 0) {
        $customer = mysqli_fetch_assoc($customer_result);
        
        // মেসেজ টেমপ্লেট
        $message = "প্রিয় {$customer['party_name']},\n\n";
        $message .= "আপনার ₹ " . number_format($customer['due_amount'], 2) . " টাকা বিল বাকি আছে।\n";
        $message .= "বিল তারিখ: " . date('d-m-Y', strtotime($customer['bill_date'])) . "\n";
        $message .= "মোট বিল: ₹ " . number_format($customer['amount'], 2) . "\n\n";
        $message .= "দয়া করে শীঘ্রই পেমেন্ট করুন।\n";
        $message .= "ধন্যবাদ!";
        
        // লগ সেভ করা
        $log_query = "INSERT INTO whatsapp_logs (customer_id, message, sent_date, status) 
                      VALUES ('$customer_id', '$message', NOW(), 'pending')";
        mysqli_query($conn, $log_query);
        
        $_SESSION['message'] = "📱 রিমাইন্ডার পাঠানোর জন্য প্রস্তুত! (WhatsApp API সংযোগ করা হয়নি)";
        
        // বাস্তব WhatsApp API এখানে সংযোগ করতে হবে
        // উদাহরণ: file_get_contents("https://graph.facebook.com/v17.0/...");
        
    } else {
        $_SESSION['error'] = "❌ কাস্টমার পাওয়া যায়নি!";
    }
}

header("Location: ../customers.php");
exit();
?>