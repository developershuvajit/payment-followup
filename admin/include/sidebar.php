<?php
// admin/include/sidebar.php
// Identify active menu item
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
            <span>Dashboard</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="vendors.php" class="<?php echo ($current_page == 'vendors.php') ? 'active' : ''; ?>">
            <i class="fas fa-store"></i>
            <span>Vendor Management</span>
        </a>
    </div>

    <div class="nav-item">
        <a href="../logout.php">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </div>
</div>