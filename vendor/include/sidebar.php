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
            <span>Dashboard</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="customers.php" class="<?php echo ($current_page == 'customers.php') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Customer List</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="add_customer.php" class="<?php echo ($current_page == 'add_customer.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-plus"></i>
            <span>Add New Customer</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="import_customers.php" class="<?php echo ($current_page == 'import_customers.php') ? 'active' : ''; ?>">
            <i class="fas fa-file-import"></i>
            <span>CSV Import</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="reminder.php" class="<?php echo ($current_page == 'reminder.php') ? 'active' : ''; ?>">
            <i class="fas fa-history"></i>
            <span>Message Reminder</span>
        </a>
    </div>
    
    <div class="nav-item">
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>