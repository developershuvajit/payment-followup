<?php
// vendor/action/import_customers.php
session_start();
require_once '../../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../../login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csv_file'])) {
    $vendor_id = $_SESSION['vendor_id'];
    $file = $_FILES['csv_file']['tmp_name'];
    
    if(($handle = fopen($file, "r")) !== FALSE) {
        $row = 0;
        $success = 0;
        $failed = 0;
        
        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if($row == 0) { $row++; continue; } // হেডার স্কিপ
            
            $party_name = mysqli_real_escape_string($conn, $data[0]);
            $whatsapp_number = mysqli_real_escape_string($conn, $data[1]);
            $bill_date = date('Y-m-d', strtotime($data[2]));
            $amount = floatval($data[3]);
            $due_amount = $amount;
            
            if(!empty($party_name) && !empty($whatsapp_number) && !empty($bill_date) && $amount > 0) {
                $insert = "INSERT INTO customers (vendor_id, party_name, whatsapp_number, bill_date, amount, due_amount, payment_status) 
                           VALUES ('$vendor_id', '$party_name', '$whatsapp_number', '$bill_date', '$amount', '$due_amount', 'unpaid')";
                
                if(mysqli_query($conn, $insert)) {
                    $success++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
            $row++;
        }
        fclose($handle);
        
        $_SESSION['message'] = "✅ $success টি কাস্টমার ইম্পোর্ট হয়েছে! ❌ $failed টি ব্যর্থ হয়েছে!";
    } else {
        $_SESSION['error'] = "❌ ফাইল খুলতে ব্যর্থ হয়েছে!";
    }
} else {
    $_SESSION['error'] = "❌ কোনো ফাইল সিলেক্ট করা হয়নি!";
}

header("Location: ../import_customers.php");
exit();
?>