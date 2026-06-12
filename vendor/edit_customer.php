 <?php
// vendor/edit_customer.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$vendor_id = $_SESSION['vendor_id'];
$customer_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Get customer information
$query = "SELECT * FROM customers WHERE id = '$customer_id' AND vendor_id = '$vendor_id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header("Location: customers.php");
    exit();
}

$customer = mysqli_fetch_assoc($result);
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-edit"></i> Edit Customer</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Update Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="action/update_customer.php">
                            <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Party/Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="party_name" class="form-control" value="<?php echo $customer['party_name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">WhatsApp Number <span class="text-danger">*</span></label>
                                    <input type="text" name="whatsapp_number" class="form-control" value="<?php echo $customer['whatsapp_number']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bill Date <span class="text-danger">*</span></label>
                                    <input type="date" name="bill_date" class="form-control" value="<?php echo $customer['bill_date']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Amount (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo $customer['amount']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Due Amount (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="due_amount" class="form-control" value="<?php echo $customer['due_amount']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Message Frequency</label>
                                    <select name="message_frequency" class="form-select">
                                        <option value="daily" <?php echo $customer['message_frequency'] == 'daily' ? 'selected' : ''; ?>>Daily</option>
                                        <option value="weekly" <?php echo $customer['message_frequency'] == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                                        <option value="monthly" <?php echo $customer['message_frequency'] == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                                        <option value="custom" <?php echo $customer['message_frequency'] == 'custom' ? 'selected' : ''; ?>>Custom</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telecalling Assign</label>
                                    <select name="telecalling_assign" class="form-select">
                                        <option value="0" <?php echo $customer['telecalling_assign'] == 0 ? 'selected' : ''; ?>>No</option>
                                        <option value="1" <?php echo $customer['telecalling_assign'] == 1 ? 'selected' : ''; ?>>Yes</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3"><?php echo $customer['notes']; ?></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="customers.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update</button>
                            </div>
                        </form>
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