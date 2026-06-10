<?php
// vendor/import_customers.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

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
            <h4><i class="fas fa-file-import"></i> CSV ইম্পোর্ট</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> লগআউট</a>
        </div>
    </div>
    
    <div class="main-content">
        
        <?php if($message != ""): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if($error != ""): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-upload"></i> CSV ফাইল আপলোড করুন</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>CSV ফাইল ফরম্যাট:</strong><br>
                            party_name, whatsapp_number, bill_date, amount<br>
                            <hr>
                            <strong>উদাহরণ:</strong><br>
                            রহিম স্টোর, 8801712345678, 2024-01-15, 5000<br>
                            করিম ট্রেডার্স, 8801712345679, 2024-01-20, 10000
                        </div>
                        
                        <form method="POST" action="action/import_customers.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">CSV ফাইল সিলেক্ট করুন <span class="text-danger">*</span></label>
                                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-upload"></i> ইম্পোর্ট করুন
                            </button>
                        </form>
                        
                        <hr>
                        <div class="text-center">
                            <a href="download_sample.php" class="btn btn-sm btn-secondary">
                                <i class="fas fa-download"></i> স্যাম্পল CSV ডাউনলোড করুন
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'include/footer.php';
?>