<?php
// admin/customers.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$message = "";
$error = "";

// Session মেসেজ
if(isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if(isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// কাস্টমার ডিলিট
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $del_query = "DELETE FROM customers WHERE id = $delete_id";
    if(mysqli_query($conn, $del_query)) {
        $message = "✅ কাস্টমার ডিলিট করা হয়েছে!";
    } else {
        $error = "❌ ডিলিট করতে ব্যর্থ হয়েছে!";
    }
}

// ফিল্টার ভেরিয়েবল
$vendor_filter = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// কুয়েরি বিল্ড
$where_conditions = [];
if($vendor_filter != '') {
    $where_conditions[] = "c.vendor_id = '$vendor_filter'";
}
if($status_filter != '') {
    $where_conditions[] = "c.payment_status = '$status_filter'";
}

$where_sql = "";
if(count($where_conditions) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_conditions);
}

// সব কাস্টমার বের করা (ভেন্ডরের নাম সহ)
$customers_query = "SELECT c.*, v.name as vendor_name, v.company_name 
                    FROM customers c 
                    LEFT JOIN vendors v ON c.vendor_id = v.id 
                    $where_sql 
                    ORDER BY c.id DESC";
$customers_result = mysqli_query($conn, $customers_query);

// ভেন্ডর লিস্ট (ফিল্টারের জন্য)
$vendors_query = "SELECT id, name FROM vendors WHERE user_type = 'vendor' AND status = 1";
$vendors_result = mysqli_query($conn, $vendors_query);
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-users"></i> কাস্টমার ম্যানেজমেন্ট</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> লগআউট</a>
        </div>
    </div>
    
    <div class="main-content">
        
        <!-- মেসেজ -->
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
        
        <!-- ফিল্টার ও অ্যাড বাটন -->
        <div class="row mb-4">
            <div class="col-md-8">
                <form method="GET" action="" class="row g-2">
                    <div class="col-md-4">
                        <select name="vendor_id" class="form-select">
                            <option value="">সব ভেন্ডর</option>
                            <?php while($v = mysqli_fetch_assoc($vendors_result)): ?>
                                <option value="<?php echo $v['id']; ?>" <?php echo $vendor_filter == $v['id'] ? 'selected' : ''; ?>>
                                    <?php echo $v['name']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">সব স্ট্যাটাস</option>
                            <option value="unpaid" <?php echo $status_filter == 'unpaid' ? 'selected' : ''; ?>>অপরিশোধিত</option>
                            <option value="partial" <?php echo $status_filter == 'partial' ? 'selected' : ''; ?>>আংশিক</option>
                            <option value="paid" <?php echo $status_filter == 'paid' ? 'selected' : ''; ?>>পরিশোধিত</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-secondary"><i class="fas fa-filter"></i> ফিল্টার</button>
                        <a href="customers.php" class="btn btn-light">রিসেট</a>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                    <i class="fas fa-plus"></i> নতুন কাস্টমার
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-import"></i> CSV ইম্পোর্ট
                </button>
            </div>
        </div>
        
        <!-- কাস্টমার লিস্ট টেবিল -->
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> কাস্টমার লিস্ট (<?php echo mysqli_num_rows($customers_result); ?> জন)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>ভেন্ডর</th>
                                        <th>পার্টি নাম</th>
                                        <th>হোয়াটসঅ্যাপ</th>
                                        <th>বিল তারিখ</th>
                                        <th>মোট বিল</th>
                                        <th>বাকি</th>
                                        <th>স্ট্যাটাস</th>
                                        <th>টেলিকলিং</th>
                                        <th>অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($customers_result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($customers_result)): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['vendor_name']; ?></td>
                                                <td><?php echo $row['party_name']; ?></td>
                                                <td><?php echo $row['whatsapp_number']; ?></td>
                                                <td><?php echo date('d-m-Y', strtotime($row['bill_date'])); ?></td>
                                                <td>₹ <?php echo number_format($row['amount'], 2); ?></td>
                                                <td class="<?php echo $row['due_amount'] > 0 ? 'text-danger fw-bold' : 'text-success'; ?>">
                                                    ₹ <?php echo number_format($row['due_amount'], 2); ?>
                                                </td>
                                                <td>
                                                    <?php if($row['payment_status'] == 'paid'): ?>
                                                        <span class="badge bg-success">পরিশোধিত</span>
                                                    <?php elseif($row['payment_status'] == 'partial'): ?>
                                                        <span class="badge bg-warning">আংশিক</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">বাকি</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if($row['telecalling_assign'] == 1): ?>
                                                        <span class="badge bg-info">অ্যাসাইন করা</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">না</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning edit-btn" 
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-vendor_id="<?php echo $row['vendor_id']; ?>"
                                                            data-party_name="<?php echo $row['party_name']; ?>"
                                                            data-whatsapp="<?php echo $row['whatsapp_number']; ?>"
                                                            data-bill_date="<?php echo $row['bill_date']; ?>"
                                                            data-amount="<?php echo $row['amount']; ?>"
                                                            data-due="<?php echo $row['due_amount']; ?>"
                                                            data-frequency="<?php echo $row['message_frequency']; ?>"
                                                            data-telecalling="<?php echo $row['telecalling_assign']; ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editCustomerModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?delete=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('ডিলিট করবেন?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <a href="payment_collect.php?id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="10" class="text-center">কোন কাস্টমার নেই</td></tr>
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

<!-- ========== অ্যাড কাস্টমার মডাল ========== -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> নতুন কাস্টমার যোগ করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_customer.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ভেন্ডর <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">সিলেক্ট করুন</option>
                            <?php
                            $ven_query = "SELECT id, name FROM vendors WHERE user_type='vendor' AND status=1";
                            $ven_res = mysqli_query($conn, $ven_query);
                            while($ven = mysqli_fetch_assoc($ven_res)):
                            ?>
                                <option value="<?php echo $ven['id']; ?>"><?php echo $ven['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">পার্টি/কোম্পানি নাম <span class="text-danger">*</span></label>
                        <input type="text" name="party_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">হোয়াটসঅ্যাপ নম্বর <span class="text-danger">*</span></label>
                        <input type="text" name="whatsapp_number" class="form-control" placeholder="8801xxxxxxxxx" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">বিল তারিখ <span class="text-danger">*</span></label>
                        <input type="date" name="bill_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">মোট টাকা (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">মেসেজ ফ্রিকোয়েন্সি</label>
                        <select name="message_frequency" class="form-select">
                            <option value="daily">দৈনিক</option>
                            <option value="weekly">সাপ্তাহিক</option>
                            <option value="monthly">মাসিক</option>
                            <option value="custom">কাস্টম</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">টেলিকলিং অ্যাসাইন</label>
                        <select name="telecalling_assign" class="form-select">
                            <option value="0">না</option>
                            <option value="1">হ্যাঁ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-primary">যোগ করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== এডিট কাস্টমার মডাল ========== -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> কাস্টমার এডিট করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="update_customer.php">
                <div class="modal-body">
                    <input type="hidden" name="customer_id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">ভেন্ডর</label>
                        <select name="vendor_id" id="edit_vendor_id" class="form-select" required>
                            <?php
                            mysqli_data_seek($vendors_result, 0);
                            while($ven = mysqli_fetch_assoc($vendors_result)):
                            ?>
                                <option value="<?php echo $ven['id']; ?>"><?php echo $ven['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">পার্টি নাম</label>
                        <input type="text" name="party_name" id="edit_party_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">হোয়াটসঅ্যাপ নম্বর</label>
                        <input type="text" name="whatsapp_number" id="edit_whatsapp" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">বিল তারিখ</label>
                        <input type="date" name="bill_date" id="edit_bill_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">মোট টাকা (₹)</label>
                        <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">বাকি টাকা (₹)</label>
                        <input type="number" step="0.01" name="due_amount" id="edit_due" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">মেসেজ ফ্রিকোয়েন্সি</label>
                        <select name="message_frequency" id="edit_frequency" class="form-select">
                            <option value="daily">দৈনিক</option>
                            <option value="weekly">সাপ্তাহিক</option>
                            <option value="monthly">মাসিক</option>
                            <option value="custom">কাস্টম</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">টেলিকলিং অ্যাসাইন</label>
                        <select name="telecalling_assign" id="edit_telecalling" class="form-select">
                            <option value="0">না</option>
                            <option value="1">হ্যাঁ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-warning">আপডেট করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========== CSV ইম্পোর্ট মডাল ========== -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-import"></i> CSV ফাইল ইম্পোর্ট করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="import_customers.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ভেন্ডর সিলেক্ট করুন <span class="text-danger">*</span></label>
                        <select name="vendor_id" class="form-select" required>
                            <option value="">সিলেক্ট করুন</option>
                            <?php
                            mysqli_data_seek($vendors_result, 0);
                            while($ven = mysqli_fetch_assoc($vendors_result)):
                            ?>
                                <option value="<?php echo $ven['id']; ?>"><?php echo $ven['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CSV ফাইল <span class="text-danger">*</span></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small class="text-muted">CSV ফরম্যাট: party_name, whatsapp_number, bill_date, amount</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                    <button type="submit" class="btn btn-success">ইম্পোর্ট করুন</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// এডিট বাটনে ক্লিক করলে ডাটা বসানো
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_vendor_id').value = this.dataset.vendor_id;
        document.getElementById('edit_party_name').value = this.dataset.party_name;
        document.getElementById('edit_whatsapp').value = this.dataset.whatsapp;
        document.getElementById('edit_bill_date').value = this.dataset.bill_date;
        document.getElementById('edit_amount').value = this.dataset.amount;
        document.getElementById('edit_due').value = this.dataset.due;
        document.getElementById('edit_frequency').value = this.dataset.frequency;
        document.getElementById('edit_telecalling').value = this.dataset.telecalling;
    });
});
</script>

<?php
require_once 'include/footer.php';
mysqli_close($conn);
?>