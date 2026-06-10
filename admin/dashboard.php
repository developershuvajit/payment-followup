 <?php
// admin/dashboard.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

// বিভিন্ন কাউন্ট বের করা
$total_vendors = 0;
$total_customers = 0;
$total_due = 0;
$total_collected = 0;

// মোট ভেন্ডর কাউন্ট
$vendor_query = "SELECT COUNT(*) as total FROM vendors WHERE user_type = 'vendor'";
$vendor_result = mysqli_query($conn, $vendor_query);
if($vendor_row = mysqli_fetch_assoc($vendor_result)) {
    $total_vendors = $vendor_row['total'];
}

// মোট কাস্টমার কাউন্ট
$customer_query = "SELECT COUNT(*) as total FROM customers";
$customer_result = mysqli_query($conn, $customer_query);
if($customer_row = mysqli_fetch_assoc($customer_result)) {
    $total_customers = $customer_row['total'];
}

// মোট বাকি টাকা
$due_query = "SELECT SUM(due_amount) as total_due FROM customers";
$due_result = mysqli_query($conn, $due_query);
if($due_row = mysqli_fetch_assoc($due_result)) {
    $total_due = $due_row['total_due'] ?? 0;
}

// মোট কালেক্ট করা টাকা
$collected_query = "SELECT SUM(paid_amount) as total_collected FROM payments";
$collected_result = mysqli_query($conn, $collected_query);
if($collected_row = mysqli_fetch_assoc($collected_result)) {
    $total_collected = $collected_row['total_collected'] ?? 0;
}
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fas fa-tachometer-alt"></i> ড্যাশবোর্ড</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user-shield"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> লগআউট</a>
        </div>
    </div>
    
    <div class="main-content">
        <!-- স্ট্যাটিস্টিক্স কার্ড -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-primary" style="border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">মোট ভেন্ডর</h6>
                                <h2 class="mb-0"><?php echo $total_vendors; ?></h2>
                            </div>
                            <i class="fas fa-store fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-success" style="border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">মোট কাস্টমার</h6>
                                <h2 class="mb-0"><?php echo $total_customers; ?></h2>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-danger" style="border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">মোট বাকি টাকা</h6>
                                <h2 class="mb-0">৳ <?php echo number_format($total_due, 2); ?></h2>
                            </div>
                            <i class="fas fa-rupee-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-info" style="border-radius: 10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title">মোট কালেক্ট করা</h6>
                                <h2 class="mb-0">৳ <?php echo number_format($total_collected, 2); ?></h2>
                            </div>
                            <i class="fas fa-hand-holding-usd fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ওয়েলকাম মেসেজ -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-body">
                        <h5><i class="fas fa-waveform"></i> সুপার অ্যাডমিন প্যানেলে স্বাগতম!</h5>
                        <p class="text-muted">আপনি এখানে সব ভেন্ডর, কাস্টমার এবং পেমেন্ট মনিটর করতে পারবেন।</p>
                        <hr>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> <strong><?php echo $total_vendors; ?></strong> জন ভেন্ডর সক্রিয়
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> <strong><?php 
                                        $due_customers = 0;
                                        $due_cust_query = "SELECT COUNT(*) as total FROM customers WHERE due_amount > 0";
                                        $due_cust_result = mysqli_query($conn, $due_cust_query);
                                        if($due_cust_row = mysqli_fetch_assoc($due_cust_result)) {
                                            $due_customers = $due_cust_row['total'];
                                        }
                                        echo $due_customers; 
                                    ?></strong> জন কাস্টমারের ৳ বাকি
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-whatsapp"></i> WhatsApp রিমাইন্ডার সক্রিয়
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- সাম্প্রতিক ভেন্ডর লিস্ট -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card" style="border-radius: 10px;">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-store"></i> সাম্প্রতিক যোগ হওয়া ভেন্ডর</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>নাম</th>
                                    <th>ইমেইল</th>
                                    <th>কোম্পানি</th>
                                    <th>স্ট্যাটাস</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_vendors = "SELECT * FROM vendors WHERE user_type = 'vendor' ORDER BY id DESC LIMIT 5";
                                $recent_result = mysqli_query($conn, $recent_vendors);
                                if(mysqli_num_rows($recent_result) > 0) {
                                    while($row = mysqli_fetch_assoc($recent_result)) {
                                        echo "<tr>";
                                        echo "<td>{$row['id']}</td>";
                                        echo "<td>{$row['name']}</td>";
                                        echo "<td>{$row['email']}</td>";
                                        echo "<td>{$row['company_name']}</td>";
                                        echo "<td><span class='badge bg-success'>সক্রিয়</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<td><td colspan='5' class='text-center'>কোন ভেন্ডর নেই</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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