<?php
// vendor/include/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <div class="brand">
        <h3>💰 Payment System</h3>
        <p>Vendor Panel</p>
    </div>
    
    <div class="nav-item">
        <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>ড্যাশবোর্ড</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>কাস্টমার লিস্ট</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="add_customer.php" class="<?php echo ($current_page == 'add_customer.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-plus"></i>
            <span>নতুন কাস্টমার</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="import_customers.php" class="<?php echo ($current_page == 'import_customers.php') ? 'active' : ''; ?>">
            <i class="fas fa-file-import"></i>
            <span>CSV ইম্পোর্ট</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="payment_history.php" class="<?php echo ($current_page == 'payment_history.php') ? 'active' : ''; ?>">
            <i class="fas fa-history"></i>
            <span>পেমেন্ট হিস্টোরি</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>লগআউট</span>
        </a>
    </div>
</div>