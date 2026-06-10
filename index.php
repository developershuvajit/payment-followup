<?php
// index.php - Landing Page
session_start();

require_once 'config/database.php';

// যদি লগইন করে থাকে তাহলে রিডাইরেক্ট
if(isset($_SESSION['user_id'])) {
    if($_SESSION['user_type'] == 'super_admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: vendor/dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Follow-up System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e0f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 450px;
            margin: 0 auto;
            padding: 40px 30px;
        }
        .logo {
            font-size: 60px;
            margin-bottom: 20px;
        }
        h1 {
            color: #00695c;
            font-size: 26px;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: #00695c;
            color: white;
            padding: 12px 35px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #004d40;
        }
        .features {
            margin-top: 30px;
            text-align: left;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .features h3 {
            color: #00695c;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .features ul {
            list-style: none;
        }
        .features li {
            color: #555;
            padding: 6px 0;
            padding-left: 25px;
            position: relative;
        }
        .features li:before {
            content: "✓";
            color: #00695c;
            position: absolute;
            left: 0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                💰📱
            </div>
            <h1>Payment Follow-up System</h1>
            <div class="subtitle">Smart Payment Reminder Solution</div>
            
            <a href="login.php" class="btn">🔐 লগইন করুন</a>
            
            <div class="features">
                <h3>সিস্টেম ফিচারসমূহ:</h3>
                <ul>
                    <li>ভেন্ডর ম্যানেজমেন্ট</li>
                    <li>কাস্টমার ডাটা এন্ট্রি</li>
                    <li>CSV/Excel ইম্পোর্ট</li>
                    <li>অটোমেটিক WhatsApp রিমাইন্ডার</li>
                    <li>পেমেন্ট কালেকশন ট্র্যাকিং</li>
                    <li>ডিউ রিপোর্ট</li>
                </ul>
            </div>
            <div class="footer">
                © 2024 Payment Follow-up System
            </div>
        </div>
    </div>
</body>
</html>