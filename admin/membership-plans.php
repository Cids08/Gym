<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <title>Membership Plans - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/admin-dashboard.css">
    <link rel="stylesheet" href="../public/css/membership-plans.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Modern Yellow & Black Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { 
                transform: translateY(-50px);
                opacity: 0;
            }
            to { 
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            margin: 8% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 450px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.7),
                0 0 0 1px rgba(255, 223, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            animation: slideIn 0.4s ease-out;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(255, 223, 0, 0.1);
        }

        /* Modal Header */
        .modal-header {
            background: linear-gradient(135deg, #ff8a00 0%, #ffb347 100%);
            padding: 20px 25px;
            position: relative;
            overflow: hidden;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.15) 50%, transparent 70%);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .modal-header h3 {
            color: #000;
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 24px;
            color: #000;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
            z-index: 2;
            font-weight: bold;
        }

        .close:hover {
            background-color: rgba(0, 0, 0, 0.15);
            transform: translateY(-50%) rotate(90deg);
        }

        /* Modal Body */
        .modal-body {
            padding: 30px 25px;
            background: #1a1a1a;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #ffdf00;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input[type="text"], 
        .form-group input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            box-sizing: border-box;
            background-color: #2d2d2d;
            border: 2px solid #404040;
            border-radius: 8px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input:focus {
            border-color: #ffdf00;
            background-color: #333;
            box-shadow: 0 0 0 3px rgba(255, 223, 0, 0.15);
        }

        .form-group input::placeholder {
            color: #888;
        }

        /* Input animations */
        .input-highlight {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #ffdf00, #f0c800);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .form-group input:focus + .input-highlight {
            transform: scaleX(1);
        }

        /* Modal Footer */
        .modal-footer {
            padding: 0 25px 25px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: #1a1a1a;
        }

        .btn-save, .btn-update, .btn-cancel {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-save, .btn-update {
            background: linear-gradient(135deg, #ffdf00 0%, #f0c800 100%);
            color: #000;
            box-shadow: 0 4px 15px rgba(255, 223, 0, 0.3);
        }

        .btn-save:hover, .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 223, 0, 0.4);
            background: linear-gradient(135deg, #fff200 0%, #ffdf00 100%);
        }

        .btn-save:disabled, .btn-update:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            background: transparent;
            color: #ccc;
            border: 2px solid #404040;
        }

        .btn-cancel:hover {
            background-color: #404040;
            color: #fff;
            border-color: #555;
        }

        /* Button ripple effect */
        .btn-save::before, .btn-update::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }

        .btn-save:active::before, .btn-update:active::before {
            width: 100px;
            height: 100px;
        }

        /* Dark scrollbar for modal content */
        .modal-content::-webkit-scrollbar {
            width: 6px;
        }

        .modal-content::-webkit-scrollbar-track {
            background: #2d2d2d;
        }

        .modal-content::-webkit-scrollbar-thumb {
            background: #ffdf00;
            border-radius: 3px;
        }

        .modal-content::-webkit-scrollbar-thumb:hover {
            background: #f0c800;
        }

        /* Error state */
        .form-group.error input {
            border-color: #ff4444;
            background-color: rgba(255, 68, 68, 0.1);
        }

        .form-group .error-message {
            color: #ff4444;
            font-size: 0.8rem;
            margin-top: 4px;
            display: none;
        }

        .form-group.error .error-message {
            display: block;
        }

        /* Success notification */
        .success-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #ffdf00, #f0c800);
            color: #000;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 223, 0, 0.4);
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .modal-content {
                margin: 5% auto;
                width: 95%;
                max-width: none;
            }
            
            .modal-header, .modal-body, .modal-footer {
                padding-left: 20px;
                padding-right: 20px;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .btn-save, .btn-update, .btn-cancel {
                width: 100%;
            }
        }

        /* Additional yellow accent elements */
        .form-group input:valid {
            border-color: #ff8a00;
        }

        .modal-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #ff8a00, #ffb347, #ff8a00);
            z-index: 1;
        }

        /* Enhanced focus states */
        .btn-save:focus, .btn-update:focus, .btn-cancel:focus {
            outline: 2px solid #ff8a00;
            outline-offset: 2px;
        }

        .form-group input:focus {
            box-shadow: 0 0 0 3px rgba(255, 138, 0, 0.15);
        }
    </style>
</head>
<body class="admin-dashboard">

<?php include '../partials/adminsidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="content-header">
        <h2 class="page-title">Membership Plans</h2>
        <div class="header-actions">
            <div class="current-date"><?php echo date('F j, Y'); ?></div>
            <div class="settings-dropdown">
                <button class="settings-btn">
                    <i class="fas fa-cog"></i>
                </button>
                <div class="dropdown-content">
                    <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="admin-container">
        <p class="description">Manage pricing, duration, and availability of membership plans.</p>

        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn-add" id="openModalBtn"><i class="fas fa-plus-circle"></i> Add Plan</button>
        </div>

        <!-- Plans Table -->
        <table class="plans-table" id="plansTable">
            <thead>
                <tr>
                    <th>Plan ID</th>
                    <th>Name</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="planRows">
                <!-- Rows will be loaded here dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Form -->
<div id="planModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Plan</h3>
            <span class="close" id="closeModalBtn">&times;</span>
        </div>
        
        <div class="modal-body">
            <form id="planForm">
                <input type="hidden" id="planId">
                
                <div class="form-group">
                    <label for="planName">Plan Name</label>
                    <input type="text" id="planName" placeholder="Enter plan name" required>
                    <div class="input-highlight"></div>
                    <div class="error-message">Please enter a valid plan name</div>
                </div>
                
                <div class="form-group">
                    <label for="planDuration">Duration</label>
                    <input type="text" id="planDuration" placeholder="e.g., 1 Month, 6 Months" required>
                    <div class="input-highlight"></div>
                    <div class="error-message">Please enter a valid duration</div>
                </div>
                
                <div class="form-group">
                    <label for="planPrice">Price ($)</label>
                    <input type="number" id="planPrice" step="0.01" placeholder="0.00" required>
                    <div class="input-highlight"></div>
                    <div class="error-message">Please enter a valid price</div>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
            <button type="submit" class="btn-save" id="saveBtn" form="planForm">Save Plan</button>
        </div>
    </div>
</div>

<script>
document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');

    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('sidebar-collapsed');

    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
});

document.addEventListener('DOMContentLoaded', function () {
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        document.getElementById('adminSidebar').classList.add('collapsed');
        document.getElementById('mainContent').classList.add('sidebar-collapsed');
    }

    // Settings dropdown
    const settingsBtn = document.querySelector('.settings-btn');
    const dropdownContent = document.querySelector('.dropdown-content');

    settingsBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdownContent.classList.toggle('show');
    });

    document.addEventListener('click', function() {
        dropdownContent.classList.remove('show');
    });

    // Modal logic
    const modal = document.getElementById('planModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const planForm = document.getElementById('planForm');
    const saveBtn = document.getElementById('saveBtn');
    const modalTitle = document.getElementById('modalTitle');

    let editing = false;

    // Open modal for adding new plan
    openModalBtn.addEventListener('click', () => {
        editing = false;
        modalTitle.textContent = "Add New Plan";
        saveBtn.classList.replace('btn-update', 'btn-save');
        saveBtn.textContent = "Save Plan";
        resetForm();
        showModal();
    });

    // Close modal events
    closeModalBtn.addEventListener('click', hideModal);
    cancelBtn.addEventListener('click', hideModal);

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            hideModal();
        }
    });

    function showModal() {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        
        // Focus on first input
        setTimeout(() => {
            document.getElementById('planName').focus();
        }, 300);
    }

    function hideModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
        clearErrors();
    }

    function resetForm() {
        planForm.reset();
        document.getElementById('planId').value = '';
        clearErrors();
    }

    function clearErrors() {
        document.querySelectorAll('.form-group').forEach(group => {
            group.classList.remove('error');
        });
    }

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const formGroup = field.closest('.form-group');
        const errorMessage = formGroup.querySelector('.error-message');
        
        formGroup.classList.add('error');
        errorMessage.textContent = message;
        
        // Remove error after user starts typing
        field.addEventListener('input', function removeError() {
            formGroup.classList.remove('error');
            field.removeEventListener('input', removeError);
        });
    }

    function validateForm() {
        let isValid = true;
        clearErrors();

        const name = document.getElementById('planName').value.trim();
        const duration = document.getElementById('planDuration').value.trim();
        const price = document.getElementById('planPrice').value;

        if (!name) {
            showError('planName', 'Plan name is required');
            isValid = false;
        }

        if (!duration) {
            showError('planDuration', 'Duration is required');
            isValid = false;
        }

        if (!price || parseFloat(price) <= 0) {
            showError('planPrice', 'Please enter a valid price');
            isValid = false;
        }

        return isValid;
    }

    function showSuccessMessage(message) {
        // Create a simple success notification
        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Enhanced form submission with loading state
    planForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        // Add loading state
        saveBtn.disabled = true;
        const originalText = saveBtn.textContent;
        saveBtn.textContent = editing ? 'Updating...' : 'Saving...';

        const id = document.getElementById('planId').value;
        const name = document.getElementById('planName').value.trim();
        const duration = document.getElementById('planDuration').value.trim();
        const price = document.getElementById('planPrice').value;

        const method = editing ? 'PUT' : 'POST';
        const url = editing
            ? `../admin/membership-handler.php?action=update&id=${id}`
            : '../admin/membership-handler.php?action=create';

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, duration, price })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            hideModal();
            loadPlans();
            
            // Show success message
            showSuccessMessage(editing ? 'Plan updated successfully!' : 'Plan created successfully!');
        })
        .catch(error => {
            console.error('Error:', error);
            showError('planName', 'An error occurred. Please try again.');
        })
        .finally(() => {
            // Restore button state
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        });
    });

    function loadPlans() {
        fetch('../admin/membership-handler.php?action=read')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('planRows');
                tbody.innerHTML = '';

                if (!data || data.length === 0) {
                    const demoData = [
                        { id: 1, name: "Monthly", duration: "1 Month", price: "30" },
                        { id: 2, name: "Yearly", duration: "12 Months", price: "300" },
                        { id: 3, name: "Student Discount", duration: "6 Months", price: "100" }
                    ];

                    demoData.forEach(plan => {
                        const row = createPlanRow(plan);
                        tbody.appendChild(row);
                    });
                } else {
                    data.forEach(plan => {
                        const row = createPlanRow(plan);
                        tbody.appendChild(row);
                    });
                }

                attachEventListeners();
            })
            .catch(err => {
                console.error("Failed to load plans:", err);
                const tbody = document.getElementById('planRows');
                tbody.innerHTML = `<tr><td colspan="5">Error loading plans</td></tr>`;
            });
    }

    function createPlanRow(plan) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${plan.id}</td>
            <td>${plan.name}</td>
            <td>${plan.duration}</td>
            <td>$${parseFloat(plan.price).toFixed(2)}</td>
            <td>
                <a href="#" class="btn-edit" data-id="${plan.id}">Edit</a>
                <a href="#" class="btn-delete" data-id="${plan.id}">Delete</a>
            </td>
        `;
        return row;
    }

    function attachEventListeners() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                
                // Enhanced confirmation dialog
                if (confirm("⚠️ Are you sure you want to delete this plan?\n\nThis action cannot be undone.")) {
                    fetch('../admin/membership-handler.php?action=delete&id=' + id)
                        .then(response => {
                            if (response.ok) {
                                loadPlans();
                                showSuccessMessage('Plan deleted successfully!');
                            }
                        })
                        .catch(error => {
                            console.error('Error deleting plan:', error);
                            alert('Error deleting plan. Please try again.');
                        });
                }
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                editing = true;
                modalTitle.textContent = "Edit Plan";
                saveBtn.classList.replace('btn-save', 'btn-update');
                saveBtn.textContent = "Update Plan";

                fetch(`../admin/membership-handler.php?action=read_one&id=${id}`)
                    .then(res => res.json())
                    .then(plan => {
                        document.getElementById('planId').value = plan.id;
                        document.getElementById('planName').value = plan.name;
                        document.getElementById('planDuration').value = plan.duration;
                        document.getElementById('planPrice').value = plan.price;
                        showModal();
                    })
                    .catch(error => {
                        console.error('Error loading plan:', error);
                        alert('Error loading plan data. Please try again.');
                    });
            });
        });
    }

    loadPlans();
});
</script>
</body>
</html>