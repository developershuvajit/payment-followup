<?php
// vendor/include/header.php
session_start();

// লগইন চেক - ভেন্ডর না হলে লগইন পেজে পাঠানো
if(!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'vendor') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Panel - Payment System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f9;
            overflow-x: hidden;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
        }
        
        /* সাইডবার স্টাইল */
        .sidebar {
            width: 260px;
            background: #1e2a3a;
            color: white;
            transition: all 0.3s;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
        }
        
        .sidebar .brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #2c3e50;
            margin-bottom: 20px;
        }
        
        .sidebar .brand h3 {
            font-size: 20px;
            margin: 0;
            color: #fff;
        }
        
        .sidebar .brand p {
            font-size: 12px;
            color: #8ba0b5;
            margin: 5px 0 0;
        }
        
        .sidebar .nav-item {
            padding: 10px 20px;
            margin: 5px 0;
        }
        
        .sidebar .nav-item a {
            color: #b0c4de;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .sidebar .nav-item a:hover {
            background: #2c3e50;
            color: white;
        }
        
        .sidebar .nav-item a.active {
            background: #00695c;
            color: white;
        }
        
        .sidebar .nav-item a i {
            width: 22px;
            font-size: 18px;
        }
        
        /* কনটেন্ট এলাকা */
        .content {
            width: calc(100% - 260px);
            margin-left: 260px;
            transition: all 0.3s;
        }
        
        /* টপবার */
        .topbar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
        }
        
        .topbar .page-title h4 {
            margin: 0;
            color: #333;
        }
        
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .topbar .user-info span {
            color: #555;
        }
        
        .topbar .user-info .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        
        .topbar .user-info .logout-btn:hover {
            background: #c82333;
        }
        
        /* মেইন কনটেন্ট */
        .main-content {
            padding: 25px;
        }
        
        /* রেসপন্সিভ */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar .brand h3, .sidebar .brand p, .sidebar .nav-item a span {
                display: none;
            }
            .sidebar .nav-item a {
                justify-content: center;
            }
            .sidebar .nav-item a i {
                font-size: 22px;
            }
            .content {
                width: calc(100% - 70px);
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">