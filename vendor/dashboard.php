<?php
// vendor/dashboard.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$vendor_id = $_SESSION['vendor_id'];

// Vendor's customer count
$customer_query = "SELECT COUNT(*) as total FROM customers WHERE vendor_id = '$vendor_id'";
$customer_result = mysqli_query($conn, $customer_query);
$total_customers = mysqli_fetch_assoc($customer_result)['total'] ?? 0;

// Total due amount
$due_query = "SELECT SUM(due_amount) as total_due FROM customers WHERE vendor_id = '$vendor_id'";
$due_result = mysqli_query($conn, $due_query);
$total_due = mysqli_fetch_assoc($due_result)['total_due'] ?? 0;

// Total collected amount
$collected_query = "SELECT SUM(paid_amount) as total_collected FROM payments WHERE customer_id IN (SELECT id FROM customers WHERE vendor_id = '$vendor_id')";
$collected_result = mysqli_query($conn, $collected_query);
$total_collected = mysqli_fetch_assoc($collected_result)['total_collected'] ?? 0;

// Telecalling pending
$tele_query = "SELECT COUNT(*) as total FROM customers WHERE vendor_id = '$vendor_id' AND telecalling_assign = 1 AND due_amount > 0";
$tele_result = mysqli_query($conn, $tele_query);
$tele_pending = mysqli_fetch_assoc($tele_result)['total'] ?? 0;
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-tachometer-alt"></i> Vendor Dashboard</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <!-- welcome card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card" style="background: linear-gradient(135deg, #00695c 0%, #004d40 100%); border-radius: 15px;">
                    <div class="card-body text-white">
                        <h4>Welcome, <?php echo $_SESSION['user_name']; ?>! 👋</h4>
                        <p class="mb-0 opacity-75">Monitor your customers and payments</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0"><?php echo $total_customers; ?></h3>
                        <p class="text-muted mb-0">Total Customers</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #fff3e0;">
                    <div class="card-body text-center">
                        <i class="fas fa-rupee-sign fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">₹ <?php echo number_format($total_due, 2); ?></h3>
                        <p class="text-muted mb-0">Total Due</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #e8f5e9;">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-usd fa-2x text-success mb-2"></i>
                        <h3 class="mb-0">₹ <?php echo number_format($total_collected, 2); ?></h3>
                        <p class="text-muted mb-0">Total Collected</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body">
                        <h5><i class="fas fa-bolt text-warning"></i> Quick Actions</h5>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <a href="add_customer.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add New Customer
                            </a>
                            <a href="import_customers.php" class="btn btn-success">
                                <i class="fas fa-file-import"></i> CSV Import
                            </a>
                            <a href="customers.php?telecall=1" class="btn btn-info">
                                <i class="fas fa-phone"></i> Telecalling List (<?php echo $tele_pending; ?>)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Customers -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Recent Customers</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Party Name</th>
                                        <th>WhatsApp Number</th>
                                        <th>Total Bill</th>
                                        <th>Due Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_query = "SELECT * FROM customers WHERE vendor_id = '$vendor_id' ORDER BY id DESC LIMIT 5";
                                    $recent_result = mysqli_query($conn, $recent_query);
                                    if(mysqli_num_rows($recent_result) > 0):
                                        while($row = mysqli_fetch_assoc($recent_result)):
                                    ?>
                                        <tr>
                                            <td><?php echo $row['party_name']; ?></td>
                                            <td><?php echo $row['whatsapp_number']; ?></td>
                                            <td>₹ <?php echo number_format($row['amount'], 2); ?></td>
                                            <td class="<?php echo $row['due_amount'] > 0 ? 'text-danger' : 'text-success'; ?>">
                                                ₹ <?php echo number_format($row['due_amount'], 2); ?>
                                            </td>
                                            <td>
                                                <?php if($row['payment_status'] == 'paid'): ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php elseif($row['payment_status'] == 'partial'): ?>
                                                    <span class="badge bg-warning">Partial</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Due</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr><td colspan="5" class="text-center">No customers found</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
mysqli_close($conn);
?>