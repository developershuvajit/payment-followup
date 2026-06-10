<?php
// admin/vendors.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$message = "";
$error = "";

// ভেন্ডর ডিলিট
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $del_query = "DELETE FROM vendors WHERE id = $delete_id AND user_type = 'vendor'";
    if(mysqli_query($conn, $del_query)) {
        $message = "✅ ভেন্ডর ডিলিট করা হয়েছে!";
    } else {
        $error = "❌ ডিলিট করতে ব্যর্থ হয়েছে!";
    }
}

// স্ট্যাটাস টগল (সক্রিয়/নিষ্ক্রিয়)
if(isset($_GET['status']) && isset($_GET['id'])) {
    $status_id = $_GET['id'];
    $new_status = $_GET['status'] == 1 ? 0 : 1;
    $status_query = "UPDATE vendors SET status = $new_status WHERE id = $status_id";
    if(mysqli_query($conn, $status_query)) {
        $message = "✅ স্ট্যাটাস আপডেট হয়েছে!";
    } else {
        $error = "❌ স্ট্যাটাস আপডেট করতে ব্যর্থ হয়েছে!";
    }
}

// সব ভেন্ডর বের করা
$vendors_query = "SELECT * FROM vendors WHERE user_type = 'vendor' ORDER BY id DESC";
$vendors_result = mysqli_query($conn, $vendors_query);
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-store"></i> ভেন্ডর ম্যানেজমেন্ট</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> লগআউট</a>
        </div>
    </div>
    
    <div class="main-content">
        
        <!-- মেসেজ দেখানোর জন্য -->
        <?php if($message != ""): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if($error != ""): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- অ্যাড ভেন্ডর বাটন -->
        <div class="row mb-4">
            <div class="col-12">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVendorModal">
                    <i class="fas fa-plus"></i> নতুন ভেন্ডর যোগ করুন
                </button>
            </div>
        </div>
        
        <!-- ভেন্ডর লিস্ট টেবিল -->
        <div class="row">
            <div class="col-12">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> ভেন্ডর লিস্ট</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>নাম</th>
                                        <th>ইমেইল</th>
                                        <th>ফোন</th>
                                        <th>কোম্পানি</th>
                                        <th>স্ট্যাটাস</th>
                                        <th>তারিখ</th>
                                        <th>অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($vendors_result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($vendors_result)): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['email']; ?></td>
                                                <td><?php echo $row['phone'] ?? '-'; ?></td>
                                                <td><?php echo $row['company_name'] ?? '-'; ?></td>
                                                <td>
                                                    <?php if($row['status'] == 1): ?>
                                                        <span class="badge bg-success">সক্রিয়</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">নিষ্ক্রিয়</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-warning edit-btn" 
                                                            data-id="<?php echo $row['id']; ?>"
                                                            data-name="<?php echo $row['name']; ?>"
                                                            data-email="<?php echo $row['email']; ?>"
                                                            data-phone="<?php echo $row['phone']; ?>"
                                                            data-company="<?php echo $row['company_name']; ?>"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#editVendorModal">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <a href="?delete=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       onclick="return confirm('আপনি কি নিশ্চিত? এই ভেন্ডর ডিলিট করলে তার সব কাস্টমারও ডিলিট হবে!')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                    <a href="?status=<?php echo $row['status']; ?>&id=<?php echo $row['id']; ?>" 
                                                       class="btn btn-sm btn-secondary">
                                                        <?php if($row['status'] == 1): ?>
                                                            <i class="fas fa-ban"></i> নিষ্ক্রিয়
                                                        <?php else: ?>
                                                            <i class="fas fa-check"></i> সক্রিয়
                                                        <?php endif; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">কোন ভেন্ডর নেই</td>
                                        </tr>
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

<!-- ========== অ্যাড ভেন্ডর মডাল ========== -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> নতুন ভেন্ডর যোগ করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="add_vendor.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">নাম <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ইমেইল <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">পাসওয়ার্ড <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ফোন নম্বর</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">কোম্পানির নাম</label>
                        <input type="text" name="company_name" class="form-control">
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

<!-- ========== এডিট ভেন্ডর মডাল ========== -->
<div class="modal fade" id="editVendorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-edit"></i> ভেন্ডর এডিট করুন</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="update_vendor.php">
                <div class="modal-body">
                    <input type="hidden" name="vendor_id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">নাম</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ইমেইল</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">পাসওয়ার্ড (খালি রাখলে অপরিবর্তিত)</label>
                        <input type="password" name="password" class="form-control" placeholder="নতুন পাসওয়ার্ড দিলে পরিবর্তন হবে">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ফোন নম্বর</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">কোম্পানির নাম</label>
                        <input type="text" name="company_name" id="edit_company" class="form-control">
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

<script>
// এডিট বাটনে ক্লিক করলে ডাটা ফিল্ডে বসানো
document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_name').value = this.dataset.name;
        document.getElementById('edit_email').value = this.dataset.email;
        document.getElementById('edit_phone').value = this.dataset.phone;
        document.getElementById('edit_company').value = this.dataset.company;
    });
});
</script>

<?php
require_once 'include/footer.php';
mysqli_close($conn);
?>