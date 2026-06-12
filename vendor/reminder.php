<?php
// vendor/reminder.php
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

// Get saved templates
$template_query = "SELECT * FROM whatsapp_templates WHERE vendor_id = '$vendor_id' OR vendor_id = 0 ORDER BY id DESC";
$template_result = mysqli_query($conn, $template_query);

// Default template
$default_template = "Dear {party_name},

Your due amount of ₹ {due_amount} is pending.
Bill Date: {bill_date}
Total Bill: ₹ {amount}

Please make the payment soon.
Thank you.";

// Filter
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_telecall = isset($_GET['telecall']) ? $_GET['telecall'] : '';

$where = "vendor_id = '$vendor_id' AND due_amount > 0";
if($filter_status != '') {
    $where .= " AND payment_status = '$filter_status'";
}
if($filter_telecall == '1') {
    $where .= " AND telecalling_assign = 1";
}

// Customer list
$customers_query = "SELECT * FROM customers WHERE $where ORDER BY due_amount DESC";
$customers_result = mysqli_query($conn, $customers_query);
$total_customers = mysqli_num_rows($customers_result);
?>

<div class="content">
    <div class="topbar">
        <div class="page-title">
            <h4><i class="fab fa-whatsapp"></i> WhatsApp Reminder</h4>
        </div>
        <div class="user-info">
            <span><i class="fas fa-user"></i> <?php echo $_SESSION['user_name']; ?></span>
            <a href="../logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            <!-- Left Side: Customer List -->
            <div class="col-md-5">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Customer List (With Due)</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <select id="statusFilter" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="unpaid" <?php echo $filter_status == 'unpaid' ? 'selected' : ''; ?>>Due</option>
                                    <option value="partial" <?php echo $filter_status == 'partial' ? 'selected' : ''; ?>>Partial</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <select id="telecallFilter" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="1" <?php echo $filter_telecall == '1' ? 'selected' : ''; ?>>Telecalling Assigned</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label for="selectAll" class="form-check-label ms-2">Select All (<?php echo $total_customers; ?> customers)</label>
                                </div>
                                <span class="badge bg-info">Selected: <span id="selectedCount">0</span></span>
                            </div>
                        </div>
                        
                        <form id="reminderForm" method="POST" action="action/send_bulk_reminder.php">
                            <div class="customer-list" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th width="30">#</th>
                                            <th>Party Name</th>
                                            <th>Due (₹)</th>
                                            <th>WhatsApp</th>
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
                                            <tr><td colspan="4" class="text-center">No customers with due amount found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Message Template -->
            <div class="col-md-7">
                <div class="card" style="border-radius: 15px;">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-edit"></i> Message Template</h5>
                    </div>
                    <div class="card-body">
                        <!-- Template Select -->
                        <div class="mb-3">
                            <label class="form-label">Saved Templates</label>
                            <select id="templateSelect" class="form-select">
                                <option value="">-- Default Template --</option>
                                <?php while($template = mysqli_fetch_assoc($template_result)): ?>
                                    <option value="<?php echo htmlspecialchars($template['template_content']); ?>">
                                        <?php echo htmlspecialchars($template['template_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Message Editor -->
                        <div class="mb-3">
                            <label class="form-label">Write Message <span class="text-danger">*</span></label>
                            <textarea id="messageContent" name="message_content" class="form-control" rows="10" required><?php echo htmlspecialchars($default_template); ?></textarea>
                            <small class="text-muted">
                                <strong>Use Variables:</strong><br>
                                {party_name} - Customer Name<br>
                                {due_amount} - Due Amount<br>
                                {amount} - Total Bill<br>
                                {bill_date} - Bill Date<br>
                                {whatsapp} - WhatsApp Number
                            </small>
                        </div>
                        
                        <!-- Preview -->
                        <div class="mb-3">
                            <label class="form-label">Preview</label>
                            <div id="previewBox" class="alert alert-info" style="min-height: 150px; white-space: pre-line;">
                                Select a template or write a message
                            </div>
                        </div>
                        
                        <!-- Save Template -->
                        <div class="row mb-3">
                            <div class="col-8">
                                <input type="text" id="templateName" class="form-control" placeholder="Template Name (e.g., Daily Reminder)">
                            </div>
                            <div class="col-4">
                                <button type="button" id="saveTemplateBtn" class="btn btn-secondary w-100">
                                    <i class="fas fa-save"></i> Save Template
                                </button>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Submit Buttons -->
                        <div class="d-flex gap-2">
                            <button type="button" id="previewBtn" class="btn btn-info">
                                <i class="fas fa-eye"></i> Preview
                            </button>
                            <button type="button" id="sendSelectedBtn" class="btn btn-primary flex-grow-1">
                                <i class="fab fa-whatsapp"></i> Send to Selected Customers
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
// Select All Function
const selectAll = document.getElementById('selectAll');
const checkboxes = document.querySelectorAll('.customer-checkbox');
const selectedCountSpan = document.getElementById('selectedCount');
const messageContent = document.getElementById('messageContent');
const previewBox = document.getElementById('previewBox');

// Update selected count
function updateSelectedCount() {
    const checked = document.querySelectorAll('.customer-checkbox:checked');
    selectedCountSpan.textContent = checked.length;
    return checked;
}

// Select All
selectAll?.addEventListener('change', function() {
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectedCount();
});

// Individual checkbox
checkboxes.forEach(cb => {
    cb.addEventListener('change', function() {
        updateSelectedCount();
        // Check select all if all checkboxes are checked
        const allChecked = document.querySelectorAll('.customer-checkbox:checked').length === checkboxes.length;
        if(selectAll) selectAll.checked = allChecked;
    });
});

// Preview function
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
        preview = preview.replace(/{party_name}/g, '[Customer Name]');
        preview = preview.replace(/{due_amount}/g, '[Due Amount]');
        preview = preview.replace(/{amount}/g, '[Total Bill]');
        preview = preview.replace(/{bill_date}/g, '[Bill Date]');
        preview = preview.replace(/{whatsapp}/g, '[Number]');
        previewBox.innerHTML = preview.replace(/\n/g, '<br>');
    }
}

// Template select
document.getElementById('templateSelect')?.addEventListener('change', function() {
    if(this.value) {
        messageContent.value = this.value;
        generatePreview();
    }
});

// Preview button
document.getElementById('previewBtn')?.addEventListener('click', generatePreview);

// Update preview when typing
messageContent.addEventListener('input', generatePreview);

// Save template
document.getElementById('saveTemplateBtn')?.addEventListener('click', function() {
    const templateName = document.getElementById('templateName').value;
    const templateContent = messageContent.value;
    
    if(!templateName) {
        alert('Please enter a template name!');
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
            alert('Template saved successfully!');
            location.reload();
        } else {
            alert('Failed to save template: ' + data.error);
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
});

// Send to selected customers
document.getElementById('sendSelectedBtn')?.addEventListener('click', function() {
    const selected = document.querySelectorAll('.customer-checkbox:checked');
    
    if(selected.length === 0) {
        alert('Please select at least one customer!');
        return;
    }
    
    if(!messageContent.value.trim()) {
        alert('Please write a message!');
        return;
    }
    
    if(confirm(`Are you sure you want to send WhatsApp reminder to ${selected.length} customer(s)?`)) {
        const form = document.getElementById('reminderForm');
        form.submit();
    }
});

// Generate preview on page load
generatePreview();
updateSelectedCount();

// Filter functionality
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