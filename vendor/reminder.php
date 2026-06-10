<?php
// vendor/reminder.php
require_once 'include/header.php';
require_once 'include/sidebar.php';
require_once '../config/database.php';

$vendor_id = $_SESSION['vendor_id'];
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

// সেভ করা টেমপ্লেট বের করা
$template_query = "SELECT * FROM whatsapp_templates WHERE vendor_id = '$vendor_id' OR vendor_id = 0 ORDER BY id DESC";
$template_result = mysqli_query($conn, $template_query);

// ডিফল্ট টেমপ্লেট
$default_template = "প্রিয় {party_name},

আপনার ₹ {due_amount} টাকা বিল বাকি আছে।
বিল তারিখ: {bill_date}
মোট বিল: ₹ {amount}

দয়া করে দ্রুত পেমেন্ট করুন।
ধন্যবাদ।";

// ফিল্টার
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_telecall = isset($_GET['telecall']) ? $_GET['telecall'] : '';

$where = "vendor_id = '$vendor_id' AND due_amount > 0";
if($filter_status != '') {
    $where .= " AND payment_status = '$filter_status'";
}
if($filter_telecall == '1') {
    $where .= " AND telecalling_assign = 1";
}

// কাস্টমার লিস্ট
$customers_query = "SELECT * FROM customers WHERE $where ORDER BY due_amount DESC";
$customers_result = mysqli_query($conn, $customers_query);
$total_customers = mysqli_num_rows($customers_result);
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fab fa-whatsapp"></i> WhatsApp রিমাইন্ডার</h4>
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
            <!-- বাম পাশ: কাস্টমার লিস্ট -->
            <div class="col-md-5">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> কাস্টমার লিস্ট (যাদের বাকি আছে)</h5>
                    </div>
                    <div class="card-body">
                        <!-- ফিল্টার -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <select id="statusFilter" class="form-select form-select-sm">
                                    <option value="">সব স্ট্যাটাস</option>
                                    <option value="unpaid" <?php echo $filter_status == 'unpaid' ? 'selected' : ''; ?>>বাকি</option>
                                    <option value="partial" <?php echo $filter_status == 'partial' ? 'selected' : ''; ?>>আংশিক</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <select id="telecallFilter" class="form-select form-select-sm">
                                    <option value="">সব</option>
                                    <option value="1" <?php echo $filter_telecall == '1' ? 'selected' : ''; ?>>টেলিকলিং অ্যাসাইন</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label for="selectAll" class="form-check-label ms-2">সব সিলেক্ট করুন (<?php echo $total_customers; ?> জন)</label>
                                </div>
                                <span class="badge bg-info">সিলেক্টেড: <span id="selectedCount">0</span></span>
                            </div>
                        </div>
                        
                        <form id="reminderForm" method="POST" action="action/send_bulk_reminder.php">
                            <div class="customer-list" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th width="30">#</th>
                                            <th>পার্টি নাম</th>
                                            <th>বাকি (₹)</th>
                                            <th>হোয়াটসঅ্যাপ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if($total_customers > 0): ?>
                                            <?php while($row = mysqli_fetch_assoc($customers_result)): ?>
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="customer_ids[]" value="<?php echo $row['id']; ?>" 
                                                               class="customer-checkbox form-check-input"
                                                               data-name="<?php echo $row['party_name']; ?>"
                                                               data-due="<?php echo $row['due_amount']; ?>"
                                                               data-amount="<?php echo $row['amount']; ?>"
                                                               data-bill="<?php echo $row['bill_date']; ?>"
                                                               data-whatsapp="<?php echo $row['whatsapp_number']; ?>">
                                                    </td>
                                                    <td><?php echo $row['party_name']; ?></td>
                                                    <td class="text-danger">₹ <?php echo number_format($row['due_amount'], 2); ?></td>
                                                    <td><?php echo $row['whatsapp_number']; ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center">কোন কাস্টমার নেই যাদের টাকা বাকি</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ডান পাশ: মেসেজ টেমপ্লেট -->
            <div class="col-md-7">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> মেসেজ টেমপ্লেট</h5>
                    </div>
                    <div class="card-body">
                        <!-- টেমপ্লেট সিলেক্ট -->
                        <div class="mb-3">
                            <label class="form-label">সেভ করা টেমপ্লেট</label>
                            <select id="templateSelect" class="form-select">
                                <option value="">-- ডিফল্ট টেমপ্লেট --</option>
                                <?php while($template = mysqli_fetch_assoc($template_result)): ?>
                                    <option value="<?php echo htmlspecialchars($template['template_content']); ?>">
                                        <?php echo htmlspecialchars($template['template_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- মেসেজ এডিটর -->
                        <div class="mb-3">
                            <label class="form-label">মেসেজ লিখুন <span class="text-danger">*</span></label>
                            <textarea id="messageContent" name="message_content" class="form-control" rows="10" required><?php echo htmlspecialchars($default_template); ?></textarea>
                            <small class="text-muted">
                                <strong>ভেরিয়েবল ব্যবহার করুন:</strong><br>
                                {party_name} - কাস্টমারের নাম<br>
                                {due_amount} - বাকি টাকা<br>
                                {amount} - মোট বিল<br>
                                {bill_date} - বিলের তারিখ<br>
                                {whatsapp} - হোয়াটসঅ্যাপ নম্বর
                            </small>
                        </div>
                        
                        <!-- প্রিভিউ -->
                        <div class="mb-3">
                            <label class="form-label">প্রিভিউ</label>
                            <div id="previewBox" class="alert alert-info" style="min-height: 150px; white-space: pre-line;">
                                টেমপ্লেট সিলেক্ট করুন অথবা মেসেজ লিখুন
                            </div>
                        </div>
                        
                        <!-- টেমপ্লেট সেভ -->
                        <div class="row mb-3">
                            <div class="col-8">
                                <input type="text" id="templateName" class="form-control" placeholder="টেমপ্লেটের নাম (যেমন: দৈনিক রিমাইন্ডার)">
                            </div>
                            <div class="col-4">
                                <button type="button" id="saveTemplateBtn" class="btn btn-secondary w-100">
                                    <i class="fas fa-save"></i> টেমপ্লেট সেভ
                                </button>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- সাবমিট বাটন -->
                        <div class="d-flex gap-2">
                            <button type="button" id="previewBtn" class="btn btn-info">
                                <i class="fas fa-eye"></i> প্রিভিউ
                            </button>
                            <button type="button" id="sendSelectedBtn" class="btn btn-primary flex-grow-1">
                                <i class="fab fa-whatsapp"></i> সিলেক্টেড কাস্টমারদের পাঠান
                            </button>
                        </div>
                        
                        <input type="hidden" id="selectedCustomersData" name="selected_customers">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// সিলেক্ট অল ফাংশন
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.customer-checkbox');
const selectedCountSpan = document.getElementById('selectedCount');
const messageContent = document.getElementById('messageContent');
const previewBox = document.getElementById('previewBox');

// আপডেট সিলেক্টেড কাউন্ট
function updateSelectedCount() {
    const checked = document.querySelectorAll('.customer-checkbox:checked');
    selectedCountSpan.textContent = checked.length;
    return checked;
}

// সিলেক্ট অল
selectAll?.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// ইন্ডিভিজুয়াল চেকবক্স
checkboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        updateSelectedCount();
        // সব চেকবক্স চেক করা থাকলে সিলেক্ট অল চেক করানো
        const allChecked = document.querySelectorAll('.customer-checkbox:checked').length === checkboxes.length;
        if(selectAll) selectAll.checked = allChecked;
    });
});

// প্রিভিউ ফাংশন
function generatePreview() {
    let message = messageContent.value;
    const firstChecked = document.querySelector('.customer-checkbox:checked');
    
    if(firstChecked) {
        let preview = message;
        preview = preview.replace(/{party_name}/g, firstChecked.dataset.name);
        preview = preview.replace(/{due_amount}/g, '₹ ' + parseFloat(firstChecked.dataset.due).toFixed(2));
        preview = preview.replace(/{amount}/g, '₹ ' + parseFloat(firstChecked.dataset.amount).toFixed(2));
        preview = preview.replace(/{bill_date}/g, firstChecked.dataset.bill);
        preview = preview.replace(/{whatsapp}/g, firstChecked.dataset.whatsapp);
        previewBox.innerHTML = preview.replace(/\n/g, '<br>');
    } else {
        let preview = message;
        preview = preview.replace(/{party_name}/g, '[কাস্টমারের নাম]');
        preview = preview.replace(/{due_amount}/g, '[বাকি টাকা]');
        preview = preview.replace(/{amount}/g, '[মোট বিল]');
        preview = preview.replace(/{bill_date}/g, '[বিলের তারিখ]');
        preview = preview.replace(/{whatsapp}/g, '[নম্বর]');
        previewBox.innerHTML = preview.replace(/\n/g, '<br>');
    }
}

// টেমপ্লেট সিলেক্ট
document.getElementById('templateSelect')?.addEventListener('change', function() {
    if(this.value) {
        messageContent.value = this.value;
        generatePreview();
    }
});

// প্রিভিউ বাটন
document.getElementById('previewBtn')?.addEventListener('click', generatePreview);

// মেসেজ টাইপ করার সময় প্রিভিউ আপডেট
messageContent.addEventListener('input', generatePreview);

// টেমপ্লেট সেভ
document.getElementById('saveTemplateBtn')?.addEventListener('click', function() {
    const templateName = document.getElementById('templateName').value;
    const templateContent = messageContent.value;
    
    if(!templateName) {
        alert('দয়া করে টেমপ্লেটের নাম দিন!');
        return;
    }
    
    fetch('action/save_template.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'template_name=' + encodeURIComponent(templateName) + '&template_content=' + encodeURIComponent(templateContent)
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('টেমপ্লেট সেভ করা হয়েছে!');
            location.reload();
        } else {
            alert('টেমপ্লেট সেভ করতে ব্যর্থ: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
});

// সিলেক্টেড কাস্টমারদের পাঠানো
document.getElementById('sendSelectedBtn')?.addEventListener('click', function() {
    const selected = document.querySelectorAll('.customer-checkbox:checked');
    
    if(selected.length === 0) {
        alert('দয়া করে কমপক্ষে একজন কাস্টমার সিলেক্ট করুন!');
        return;
    }
    
    if(!messageContent.value.trim()) {
        alert('দয়া করে একটি মেসেজ লিখুন!');
        return;
    }
    
    if(confirm(`আপনি কি ${selected.length} জন কাস্টমারকে WhatsApp reminder পাঠাতে চান?`)) {
        const form = document.getElementById('reminderForm');
        form.submit();
    }
});

// পৃষ্ঠা লোড হলে প্রিভিউ জেনারেট
generatePreview();
updateSelectedCount();

// ফিল্টার functionality
document.getElementById('statusFilter')?.addEventListener('change', function() {
    window.location.href = 'reminder.php?status=' + this.value + '&telecall=' + document.getElementById('telecallFilter').value;
});

document.getElementById('telecallFilter')?.addEventListener('change', function() {
    window.location.href = 'reminder.php?status=' + document.getElementById('statusFilter').value + '&telecall=' + this.value;
});
</script>

<style>
.customer-list::-webkit-scrollbar {
    width: 5px;
}
.customer-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}
.customer-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 5px;
}
</style>

<?php
require_once 'include/footer.php';
mysqli_close($conn);
?>