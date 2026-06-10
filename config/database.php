<?php
// config/database.php
// MySQLi দিয়ে সিম্পল ডাটাবেজ কানেকশন

$host = "localhost:8889";
$db_name = "payment_system";
$username = "root";
$password = "root";

// কানেকশন তৈরি
$conn = mysqli_connect($host, $username, $password, $db_name);

// কানেকশন চেক
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ইউনিকোড সাপোর্টের জন্য
mysqli_set_charset($conn, "utf8");

// কানেকশন ভেরিয়েবলটা সব জায়গায় ব্যবহার করার জন্য
?>