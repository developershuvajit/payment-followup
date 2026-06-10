<?php
// vendor/payment_collect.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$vendor_id = $_SESSION['vendor_id'];
$customer_id = isset($_GET['id']) ? $_GET['id'] : 0;

// কাস্টমারের তথ্য বের করা
$query = "SELECT * FROM customers WHERE id = '$customer_id' AND vendor_id = '$vendor_id'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) == 0) {
    header("Location: customers.php");
    exit();
}

$customer = mysqli_fetch_assoc($result);

// পেমেন্ট হিস্টোরি
$history_query = "SELECT * FROM payments WHERE customer_id = '$customer_id' ORDER BY payment_date DESC";
$history_result = mysqli_query($conn, $history_query);

// মেসেজ দেখানো
$message = "";
$error = "";
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-hand-holding-usd"></i> পেমেন্ট কালেক্ট করুন</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> লগআউট</a>
        </div>
    </div>
    
    <div class="main-content">
        
        <?php if($message != ""): ?>
            <div class="alert alert-success alert-dismissible fade show"><?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error != ""): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- পেমেন্ট ফর্ম -->
            <div class="col-md-5">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-rupee-sign"></i> পেমেন্ট নিন</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong><?php echo $customer['party_name']; ?></strong><br>
                            <span class="text-muted">হোয়াটসঅ্যাপ: <?php echo $customer['whatsapp_number']; ?></span><br>
                            <span class="text-muted">মোট বিল: ₹ <?php echo number_format($customer['amount'], 2); ?></span><br>
                            <span class="text-danger fw-bold">বর্তমান বাকি: ₹ <?php echo number_format($customer['due_amount'], 2); ?></span>
                        </div>
                        
                        <form method="POST" action="action/payment_process.php">
                            <input type="hidden" name="customer_id" value="<?php echo $customer['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">পেমেন্টের পরিমাণ (₹) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control" max="<?php echo $customer['due_amount']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">পেমেন্টের তারিখ <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">নোটস</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="যেমন: ক্যাশ/ব্যাংক ট্রান্সফার"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> পেমেন্ট সংরক্ষণ করুন</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- পেমেন্ট হিস্টোরি -->
            <div class="col-md-7">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> পেমেন্ট হিস্টোরি</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>তারিখ</th>
                                        <th>পেমেন্ট</th>
                                        <th>বাকি থাকে</th>
                                        <th>নোটস</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($history_result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                                            <tr>
                                                <td><?php echo date('d-m-Y', strtotime($row['payment_date'])); ?></td>
                                                <td class="text-success fw-bold">₹ <?php echo number_format($row['paid_amount'], 2); ?></td>
                                                <td>₹ <?php echo number_format($row['remaining_due'], 2); ?></td>
                                                <td><?php echo $row['notes'] ?? '-'; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center">কোনো পেমেন্ট নেই</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="customers.php" class="btn btn-secondary btn-sm">← কাস্টমার লিস্টে ফিরে যান</a>
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