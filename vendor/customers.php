<?php
// vendor/customers.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$vendor_id = $_SESSION['vendor_id'];
$message = "";
$error = "";

// Session message
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Delete customer
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $del_query = "DELETE FROM customers WHERE id = $delete_id AND vendor_id = '$vendor_id'";
    if(mysqli_query($conn, $del_query)) {
        $message = "✅ Customer deleted successfully!";
    } else {
        $error = "❌ Failed to delete customer!";
    }
}

// Filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$tele_filter = isset($_GET['telecall']) ? $_GET['telecall'] : '';

$where = "vendor_id = '$vendor_id'";
if($status_filter != '') {
    $where .= " AND payment_status = '$status_filter'";
}
if($tele_filter == '1') {
    $where .= " AND telecalling_assign = 1 AND due_amount > 0";
}

$customers_query = "SELECT * FROM customers WHERE $where ORDER BY id DESC";
$customers_result = mysqli_query($conn, $customers_query);
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-users"></i> My Customer List</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        
        <?php if($message != ""): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error != ""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <!-- Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="" class="row g-2">
                    <div class="col-auto">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="unpaid" <?php echo $status_filter == 'unpaid' ? 'selected' : ''; ?>>Due</option>
                            <option value="partial" <?php echo $status_filter == 'partial' ? 'selected' : ''; ?>>Partial</option>
                            <option value="paid" <?php echo $status_filter == 'paid' ? 'selected' : ''; ?>>Paid</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                        <a href="customers.php" class="btn btn-light">Reset</a>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-end">
                <a href="add_customer.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Customer</a>
                <a href="import_customers.php" class="btn btn-success"><i class="fas fa-file-import"></i> CSV Import</a>
            </div>
        </div>
        
        <!-- Customer Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Customer List (<?php echo mysqli_num_rows($customers_result); ?> records)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Party Name</th>
                                        <th>WhatsApp Number</th>
                                        <th>Bill Date</th>
                                        <th>Total Amount</th>
                                        <th>Due Amount</th>
                                        <th>Status</th>
                                        <th>Telecalling</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($customers_result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($customers_result)): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['party_name']; ?></td>
                                                <td><?php echo $row['whatsapp_number']; ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($row['bill_date'])); ?></td>
                                                <td>₹ <?php echo number_format($row['amount'], 2); ?></td>
                                                <td class="text-danger fw-bold">₹ <?php echo number_format($row['due_amount'], 2); ?></td>
                                                <td>
                                                    <?php if($row['payment_status'] == 'paid'): ?>
                                                        <span class="badge bg-success">Paid</span>
                                                    <?php elseif($row['payment_status'] == 'partial'): ?>
                                                        <span class="badge bg-warning">Partial</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Due</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($row['telecalling_assign'] == 1): ?>
                                                        <span class="badge bg-info">Yes</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">No</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <a href="payment_collect.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </a>
                                                    <a href="send_reminder.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="9" class="text-center">No customers found</td></tr>
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