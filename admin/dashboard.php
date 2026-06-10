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
$total_pending_followup = 0;
$total_telecalling = 0;

// মোট ভেন্ডর কাউন্ট
$vendor_query = "SELECT COUNT(*) as total FROM vendors WHERE user_type = 'vendor' AND status = 1";
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

// যাদের টাকা বাকি (pending followup)
$pending_query = "SELECT COUNT(*) as total FROM customers WHERE due_amount > 0";
$pending_result = mysqli_query($conn, $pending_query);
if($pending_row = mysqli_fetch_assoc($pending_result)) {
    $total_pending_followup = $pending_row['total'];
}

// টেলিকলিং অ্যাসাইন যারা আছে
$tele_query = "SELECT COUNT(*) as total FROM customers WHERE telecalling_assign = 1";
$tele_result = mysqli_query($conn, $tele_query);
if($tele_row = mysqli_fetch_assoc($tele_result)) {
    $total_telecalling = $tele_row['total'];
}

// মাস অনুযায়ী কালেকশন (চার্টের জন্য)
$monthly_collection = [];
$month_query = "SELECT DATE_FORMAT(payment_date, '%M') as month, SUM(paid_amount) as total 
                FROM payments 
                WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY MONTH(payment_date) 
                ORDER BY payment_date DESC";
$month_result = mysqli_query($conn, $month_query);
$months = [];
$collections = [];
while($row = mysqli_fetch_assoc($month_result)) {
    $months[] = $row['month'];
    $collections[] = $row['total'];
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
        
        <!-- welcome card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white" style="background: linear-gradient(135deg, #00695c 0%, #004d40 100%); border-radius: 15px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-2">স্বাগতম, <?php echo $_SESSION['user_name']; ?>! 👋</h4>
                                <p class="mb-0 opacity-75">সুপার অ্যাডমিন প্যানেলে আপনি সব ভেন্ডর, কাস্টমার এবং পেমেন্ট মনিটর করতে পারবেন।</p>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-chart-line fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- স্ট্যাটিস্টিক্স কার্ড (6 টা কার্ড) -->
        <div class="row">
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <i class="fas fa-store fa-2x text-primary mb-2"></i>
                        <h3 class="mb-0"><?php echo $total_vendors; ?></h3>
                        <p class="text-muted mb-0">মোট ভেন্ডর</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x text-success mb-2"></i>
                        <h3 class="mb-0"><?php echo $total_customers; ?></h3>
                        <p class="text-muted mb-0">মোট কাস্টমার</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #fff3e0;">
                    <div class="card-body text-center">
                        <i class="fas fa-rupee-sign fa-2x text-warning mb-2"></i>
                        <h3 class="mb-0">৳ <?php echo number_format($total_due, 0); ?></h3>
                        <p class="text-muted mb-0">মোট বাকি</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #e8f5e9;">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-usd fa-2x text-info mb-2"></i>
                        <h3 class="mb-0">৳ <?php echo number_format($total_collected, 0); ?></h3>
                        <p class="text-muted mb-0">মোট কালেক্ট</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #ffebee;">
                    <div class="card-body text-center">
                        <i class="fas fa-bell fa-2x text-danger mb-2"></i>
                        <h3 class="mb-0"><?php echo $total_pending_followup; ?></h3>
                        <p class="text-muted mb-0">ফলোআপ বাকি</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 col-lg-2 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; background: #e3f2fd;">
                    <div class="card-body text-center">
                        <i class="fas fa-phone-alt fa-2x text-secondary mb-2"></i>
                        <h3 class="mb-0"><?php echo $total_telecalling; ?></h3>
                        <p class="text-muted mb-0">টেলিকলিং অ্যাসাইন</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- চার্ট এবং লিস্ট সেকশন -->
        <div class="row mt-3">
            <!-- কালেকশন চার্ট -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0"><i class="fas fa-chart-line text-success"></i> গত ৬ মাসের কালেকশন</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="collectionChart" height="200"></canvas>
                        <?php if(count($collections) == 0): ?>
                            <p class="text-center text-muted mt-3">কোনো ডাটা নেই</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- পেমেন্ট স্ট্যাটাস -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0"><i class="fas fa-chart-pie text-primary"></i> পেমেন্ট স্ট্যাটাস</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // পেমেন্ট স্ট্যাটাস কাউন্ট
                        $paid_query = "SELECT COUNT(*) as total FROM customers WHERE payment_status = 'paid'";
                        $paid_result = mysqli_query($conn, $paid_query);
                        $paid_count = mysqli_fetch_assoc($paid_result)['total'] ?? 0;
                        
                        $unpaid_query = "SELECT COUNT(*) as total FROM customers WHERE payment_status = 'unpaid'";
                        $unpaid_result = mysqli_query($conn, $unpaid_query);
                        $unpaid_count = mysqli_fetch_assoc($unpaid_result)['total'] ?? 0;
                        
                        $partial_query = "SELECT COUNT(*) as total FROM customers WHERE payment_status = 'partial'";
                        $partial_result = mysqli_query($conn, $partial_query);
                        $partial_count = mysqli_fetch_assoc($partial_result)['total'] ?? 0;
                        ?>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-check-circle text-success"></i> পরিশোধিত</span>
                                <span><?php echo $paid_count; ?> জন</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo $total_customers > 0 ? ($paid_count/$total_customers)*100 : 0; ?>%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-clock text-warning"></i> আংশিক</span>
                                <span><?php echo $partial_count; ?> জন</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-warning" style="width: <?php echo $total_customers > 0 ? ($partial_count/$total_customers)*100 : 0; ?>%"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <span><i class="fas fa-times-circle text-danger"></i> বাকি</span>
                                <span><?php echo $unpaid_count; ?> জন</span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: <?php echo $total_customers > 0 ? ($unpaid_count/$total_customers)*100 : 0; ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <small><i class="fas fa-info-circle"></i> মোট বাকি টাকা: <strong>৳ <?php echo number_format($total_due, 2); ?></strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- সাম্প্রতিক ভেন্ডর এবং কাস্টমার -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0"><i class="fas fa-store text-primary"></i> সাম্প্রতিক ভেন্ডর</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>নাম</th>
                                        <th>ইমেইল</th>
                                        <th>কোম্পানি</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recent_vendors = "SELECT * FROM vendors WHERE user_type = 'vendor' ORDER BY id DESC LIMIT 5";
                                    $recent_result = mysqli_query($conn, $recent_vendors);
                                    if(mysqli_num_rows($recent_result) > 0) {
                                        while($row = mysqli_fetch_assoc($recent_result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['name']}</td>";
                                            echo "<td>{$row['email']}</td>";
                                            echo "<td>{$row['company_name']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center'>কোন ভেন্ডর নেই</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="vendors.php" class="btn btn-sm btn-primary">সব ভেন্ডর দেখুন →</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0"><i class="fas fa-rupee-sign text-warning"></i> শীর্ষ বাকি কাস্টমার</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>পার্টি নাম</th>
                                        <th>মোট বিল</th>
                                        <th>বাকি</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $top_due = "SELECT party_name, amount, due_amount FROM customers WHERE due_amount > 0 ORDER BY due_amount DESC LIMIT 5";
                                    $top_result = mysqli_query($conn, $top_due);
                                    if(mysqli_num_rows($top_result) > 0) {
                                        while($row = mysqli_fetch_assoc($top_result)) {
                                            echo "<tr>";
                                            echo "<td>{$row['party_name']}</td>";
                                            echo "<td>৳ " . number_format($row['amount'], 2) . "</td>";
                                            echo "<td class='text-danger fw-bold'>৳ " . number_format($row['due_amount'], 2) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='3' class='text-center'>কোন বাকি কাস্টমার নেই</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="all_customers.php" class="btn btn-sm btn-warning">সব কাস্টমার দেখুন →</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- কুইক অ্যাকশন -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-bolt text-warning"></i> কুইক অ্যাকশন</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="vendors.php?action=add" class="btn btn-primary">
                                <i class="fas fa-plus"></i> নতুন ভেন্ডর
                            </a>
                            <a href="reports.php" class="btn btn-info">
                                <i class="fas fa-file-alt"></i> রিপোর্ট দেখুন
                            </a>
                            <a href="all_customers.php" class="btn btn-success">
                                <i class="fas fa-download"></i> কাস্টমার লিস্ট
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// চার্ট দেখানোর জন্য
<?php if(count($collections) > 0): ?>
const ctx = document.getElementById('collectionChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_reverse($months)); ?>,
        datasets: [{
            label: 'কালেকশন (৳)',
            data: <?php echo json_encode(array_reverse($collections)); ?>,
            borderColor: '#00695c',
            backgroundColor: 'rgba(0, 105, 92, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
<?php endif; ?>
</script>

<?php
require_once 'include/footer.php';
mysqli_close($conn);
?>