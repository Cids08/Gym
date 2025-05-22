<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payments - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- OLD PATH FORMAT -->
    <link rel="stylesheet" href="../public/css/admin-dashboard.css">
    <link rel="stylesheet" href="../public/css/payments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css ">

    <style>
        /* Main content container */
        .admin-container {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(30, 30, 30, 0.8) 100%);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6);
            border: 1px solid rgba(255, 235, 59, 0.2);
            margin-bottom: 40px;
        }

        .description {
            text-align: center;
            font-size: 1.1em;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }

        .btn-add {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
        }

        /* Filters */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 30px 0;
            justify-content: center;
        }

        .filters input,
        .filters select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid rgba(255, 235, 59, 0.3);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 1em;
            min-width: 180px;
            transition: all 0.3s ease;
        }

        .filters input:focus,
        .filters select:focus {
            outline: none;
            border-color: #ffeb3b;
            box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.1);
        }

        .filters input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .btn-filter {
            background: linear-gradient(135deg, #03a9f4, #0288d1);
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(3, 169, 244, 0.3);
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(3, 169, 244, 0.4);
        }

        /* Table */
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
        }

        .payments-table th,
        .payments-table td {
            padding: 15px;
            border: 1px solid rgba(255, 235, 59, 0.1);
            text-align: left;
            color: #f5f5f5;
        }

        .payments-table th {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            color: white;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .payments-table td {
            background-color: rgba(30, 30, 30, 0.7);
            transition: all 0.3s ease;
        }

        .payments-table tr:nth-child(even) td {
            background-color: rgba(40, 40, 40, 0.7);
        }

        .payments-table tr:hover td {
            background-color: rgba(255, 235, 59, 0.1);
            transform: scale(1.01);
        }

        /* Action buttons in table */
        .btn-edit, .btn-delete {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            margin-right: 5px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-edit {
            background-color: #2196f3;
            color: white;
        }

        .btn-edit:hover {
            background-color: #1976d2;
            transform: translateY(-1px);
        }

        .btn-delete {
            background-color: #f44336;
            color: white;
        }

        .btn-delete:hover {
            background-color: #d32f2f;
            transform: translateY(-1px);
        }

        /* Status Classes */
        .paid {
            color: #4caf50;
            font-weight: bold;
            text-shadow: 0 0 8px rgba(76, 175, 80, 0.3);
        }

        .pending {
            color: #ffc107;
            font-weight: bold;
            text-shadow: 0 0 8px rgba(255, 193, 7, 0.3);
        }

        .overdue {
            color: #f44336;
            font-weight: bold;
            text-shadow: 0 0 8px rgba(244, 67, 54, 0.3);
        }

        /* Enhanced Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background: linear-gradient(135deg, rgba(30, 30, 30, 0.95) 0%, rgba(50, 50, 50, 0.95) 100%);
            margin: 8% auto;
            padding: 0;
            border-radius: 15px;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 235, 59, 0.2);
            animation: slideIn 0.3s ease;
            position: relative;
            overflow: hidden;
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

        .modal-header {
            background: linear-gradient(135deg, #ff9800, #f57c00);
            padding: 20px 25px;
            border-bottom: 1px solid rgba(255, 235, 59, 0.2);
            position: relative;
        }

        .modal-header h3 {
            margin: 0;
            color: white;
            font-size: 1.3em;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .close:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-50%) rotate(90deg);
        }

        .modal-body {
            padding: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            font-size: 0.95em;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="date"],
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            box-sizing: border-box;
            border: 1px solid rgba(255, 235, 59, 0.3);
            border-radius: 8px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ffeb3b;
            box-shadow: 0 0 0 3px rgba(255, 235, 59, 0.1);
            background-color: rgba(0, 0, 0, 0.7);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .modal-footer {
            padding: 20px 25px;
            border-top: 1px solid rgba(255, 235, 59, 0.1);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background-color: rgba(0, 0, 0, 0.2);
        }

        .btn-save, .btn-update {
            background: linear-gradient(135deg, #4caf50, #45a049);
            color: white;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .btn-update {
            background: linear-gradient(135deg, #2196f3, #1976d2);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        }

        .btn-save:hover, .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(76, 175, 80, 0.4);
        }

        .btn-update:hover {
            box-shadow: 0 6px 16px rgba(33, 150, 243, 0.4);
        }

        .btn-cancel {
            background: linear-gradient(135deg, #757575, #616161);
            color: white;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(117, 117, 117, 0.3);
        }

        .btn-cancel:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(117, 117, 117, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-container {
                padding: 20px;
            }

            .filters {
                flex-direction: column;
                align-items: stretch;
            }

            .filters input,
            .filters select {
                min-width: auto;
            }

            .payments-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }

            .payments-table th,
            .payments-table td {
                padding: 10px;
                font-size: 0.9em;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn-save, .btn-update, .btn-cancel {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="admin-dashboard">

<?php include '../partials/adminsidebar.php'; ?>

<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="content-header">
        <h2 class="page-title">Payments</h2>
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
        <p class="description">View and manage all payment transactions. Filter by status or user, and monitor overdue payments.</p>

        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn-add" id="openModalBtn"><i class="fas fa-plus-circle"></i> Add Payment</button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <input type="text" placeholder="Search by Member" id="searchMember">
            <input type="date" id="filterDate">
            <select id="filterStatus">
                <option value="">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="overdue">Overdue</option>
            </select>
            <button class="btn-filter" id="applyFilter"><i class="fas fa-filter"></i> Filter</button>
        </div>

        <!-- Payments Table -->
        <table class="payments-table" id="paymentsTable">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Member</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="paymentRows">
                <!-- Rows will be loaded dynamically -->
            </tbody>
        </table>
    </div>
</div>

<!-- Enhanced Modal Form -->
<div id="paymentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add New Payment</h3>
            <span class="close" id="closeModalBtn">&times;</span>
        </div>
        <form id="paymentForm">
            <div class="modal-body">
                <input type="hidden" id="transactionId">
                <div class="form-group">
                    <label for="memberName">Member Name</label>
                    <input type="text" id="memberName" placeholder="Enter member name" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" step="0.01" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label for="paymentDate">Payment Date</label>
                    <input type="date" id="paymentDate" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" required>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="overdue">Overdue</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn-save" id="saveBtn">Save</button>
            </div>
        </form>
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
    const modal = document.getElementById('paymentModal');
    const openModalBtn = document.getElementById('openModalBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const paymentForm = document.getElementById('paymentForm');
    const saveBtn = document.getElementById('saveBtn');
    const modalTitle = document.getElementById('modalTitle');

    let editing = false;

    function openModal() {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    openModalBtn.addEventListener('click', () => {
        editing = false;
        modalTitle.textContent = "Add New Payment";
        saveBtn.classList.replace('btn-update', 'btn-save');
        saveBtn.textContent = "Save";
        paymentForm.reset();
        document.getElementById('transactionId').value = '';
        // Set default date to today
        document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
        openModal();
    });

    closeModalBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });

    function loadPayments() {
        fetch('../admin/payment-handler.php?action=read')
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('paymentRows');
                tbody.innerHTML = '';

                data.forEach(payment => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${payment.id}</td>
                        <td>${payment.member}</td>
                        <td>$${parseFloat(payment.amount).toFixed(2)}</td>
                        <td>${payment.date}</td>
                        <td class="${payment.status}">${capitalize(payment.status)}</td>
                        <td>
                            <a href="#" class="btn-edit" data-id="${payment.id}">Edit</a>
                            <a href="#" class="btn-delete" data-id="${payment.id}">Delete</a>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                attachEventListeners();
            })
            .catch(error => {
                console.error('Error loading payments:', error);
            });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function attachEventListeners() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                if (confirm("Are you sure you want to delete this transaction?")) {
                    fetch('../admin/payment-handler.php?action=delete&id=' + id)
                        .then(() => loadPayments())
                        .catch(error => {
                            console.error('Error deleting payment:', error);
                            alert('Error deleting payment. Please try again.');
                        });
                }
            });
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                editing = true;
                modalTitle.textContent = "Edit Payment";
                saveBtn.classList.replace('btn-save', 'btn-update');
                saveBtn.textContent = "Update";

                fetch(`../admin/payment-handler.php?action=read_one&id=${id}`)
                    .then(res => res.json())
                    .then(payment => {
                        document.getElementById('transactionId').value = payment.id;
                        document.getElementById('memberName').value = payment.member;
                        document.getElementById('amount').value = payment.amount;
                        document.getElementById('paymentDate').value = payment.date;
                        document.getElementById('status').value = payment.status;
                        openModal();
                    })
                    .catch(error => {
                        console.error('Error loading payment data:', error);
                        alert('Error loading payment data. Please try again.');
                    });
            });
        });
    }

    paymentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        // Disable submit button to prevent double submission
        saveBtn.disabled = true;
        const originalText = saveBtn.textContent;
        saveBtn.innerHTML = '<span class="loading"></span> Processing...';

        const id = document.getElementById('transactionId').value;
        const member = document.getElementById('memberName').value;
        const amount = document.getElementById('amount').value;
        const date = document.getElementById('paymentDate').value;
        const status = document.getElementById('status').value;

        const method = editing ? 'PUT' : 'POST';
        const url = editing
            ? `../admin/payment-handler.php?action=update&id=${id}`
            : '../admin/payment-handler.php?action=create';

        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ member, amount, date, status })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(() => {
            closeModal();
            loadPayments();
        })
        .catch(error => {
            console.error('Error saving payment:', error);
            alert('Error saving payment. Please try again.');
        })
        .finally(() => {
            // Re-enable submit button
            saveBtn.disabled = false;
            saveBtn.textContent = originalText;
        });
    });

    // Apply filters
    document.getElementById('applyFilter').addEventListener('click', function () {
        const search = document.getElementById('searchMember').value.toLowerCase();
        const date = document.getElementById('filterDate').value;
        const status = document.getElementById('filterStatus').value;

        const rows = document.querySelectorAll('#paymentRows tr');

        rows.forEach(row => {
            const cols = row.children;
            const member = cols[1].innerText.toLowerCase();
            const paymentDate = cols[3].innerText;
            const paymentStatus = cols[4].innerText.toLowerCase();

            let match = true;

            if (search && !member.includes(search)) match = false;
            if (date && paymentDate !== date) match = false;
            if (status && paymentStatus !== status.toLowerCase()) match = false;

            row.style.display = match ? '' : 'none';
        });
    });

    // Real-time search
    document.getElementById('searchMember').addEventListener('input', function() {
        document.getElementById('applyFilter').click();
    });

    // Load payments on page load
    loadPayments();
});
</script>
</body>
</html>