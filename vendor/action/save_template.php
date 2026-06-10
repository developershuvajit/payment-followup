<?php
// vendor/action/save_template.php
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$vendor_id = $_SESSION['vendor_id'];
$template_name = isset($_POST['template_name']) ? mysqli_real_escape_string($conn, $_POST['template_name']) : '';
$template_content = isset($_POST['template_content']) ? mysqli_real_escape_string($conn, $_POST['template_content']) : '';

if(empty($template_name) || empty($template_content)) {
    echo json_encode(['success' => false, 'error' => 'টেমপ্লেট নাম এবং কন্টেন্ট প্রয়োজন']);
    exit();
}

// চেক করা টেমপ্লেট আগে থেকে আছে কিনা
$check_query = "SELECT id FROM whatsapp_templates WHERE vendor_id = '$vendor_id' AND template_name = '$template_name'";
$check_result = mysqli_query($conn, $check_query);

if(mysqli_num_rows($check_result) > 0) {
    // আপডেট
    $update_query = "UPDATE whatsapp_templates SET template_content = '$template_content' WHERE vendor_id = '$vendor_id' AND template_name = '$template_name'";
    if(mysqli_query($conn, $update_query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    // ইনসার্ট
    $insert_query = "INSERT INTO whatsapp_templates (vendor_id, template_name, template_content) VALUES ('$vendor_id', '$template_name', '$template_content')";
    if(mysqli_query($conn, $insert_query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
}
?>