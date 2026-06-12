<?php
// vendor/add_customer.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$vendor_id = $_SESSION['vendor_id'];
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-user-plus"></i> Add New Customer</h4>
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
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus"></i> Enter Customer Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="action/add_customer.php">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Party/Company Name <span class="text-danger">*</span></label>
                                    <input type="text" name="party_name" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">WhatsApp Number <span class="text-danger">*</span></label>
                                    <input type="text" name="whatsapp_number" class="form-control" placeholder="8801xxxxxxxxx" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Bill Date <span class="text-danger">*</span></label>
                                    <input type="date" name="bill_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Amount (₹) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Message Frequency</label>
                                    <select name="message_frequency" class="form-select">
                                        <option value="daily">Daily</option>
                                        <option value="weekly" selected>Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telecalling Assign</label>
                                    <select name="telecalling_assign" class="form-select">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Notes (Optional)</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="Write any special notes..."></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="text-end">
                                <a href="customers.php" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
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
?>