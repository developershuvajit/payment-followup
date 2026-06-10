<?php
// admin/include/sidebar.php
// সক্রিয় মেনু চিহ্নিত করার জন্য
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="brand">
        <h3>💰 Payment System</h3>
        <p>Super Admin Panel</p>
    </div>
    
    <div class="nav-item">
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>ড্যাশবোর্ড</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="vendors.php" class="<?php echo ($current_page == 'vendors.php') ? 'active' : ''; ?>">
            <i class="fas fa-store"></i>
            <span>ভেন্ডর ম্যানেজমেন্ট</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="all_customers.php" class="<?php echo ($current_page == 'all_customers.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>সব কাস্টমার</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="payment_logs.php" class="<?php echo ($current_page == 'payment_logs.php') ? 'active' : ''; ?>">
            <i class="fas fa-history"></i>
            <span>পেমেন্ট লগস</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="whatsapp_settings.php" class="<?php echo ($current_page == 'whatsapp_settings.php') ? 'active' : ''; ?>">
            <i class="fab fa-whatsapp"></i>
            <span>WhatsApp সেটিংস</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="reports.php" class="<?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i>
            <span>রিপোর্ট</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>লগআউট</span>
        </a>
    </div>
</div>