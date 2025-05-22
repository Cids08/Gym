<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../frontend/login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "muscle_city_gym");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$sql = "SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC";
$result = $conn->query($sql);

function debug_log($message)
{
    file_put_contents('debug.log', date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Members - Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/css/admin-dashboard.css">
    <link rel="stylesheet" href="../public/css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css ">
    <style>
        /* Keep your full CSS styles -->
           <!-- You already have them in your uploaded file -->
        */
        .badge {
            padding: 5px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }

        .basic {
            background-color: #6c757d;
        }

        .premium {
            background-color: #28a745;
        }

        .vip {
            background-color: #ffc107;
            color: black;
        }

        .active {
            background-color: #28a745;
        }

        .expired {
            background-color: #dc3545;
        }

        .pending {
            background-color: #ffc107;
            color: black;
        }

        /* Enhanced Modal Styles -->
           <!-- This is preserved from your original file -->
        */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(2px);
            animation: modalFadeIn 0.3s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes modalFadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .modal-content {
            background-color: #2a2a2a;
            margin: 5% auto;
            padding: 0;
            border: none;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            animation: modalSlideIn 0.3s ease-out;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #ff8a00 0%, #ffb347 100%);
            color: #1a1a1a;
            padding: 20px 30px;
            position: relative;
            font-weight: 600;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 300;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .close:hover {
            background-color: rgba(26, 26, 26, 0.1);
            transform: translateY(-50%) rotate(90deg);
        }

        .modal-body {
            padding: 30px;
            background-color: #2a2a2a;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #fff;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #444;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #1a1a1a;
            color: #fff;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff8a00;
            background-color: #333;
            box-shadow: 0 0 0 3px rgba(255, 138, 0, 0.1);
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .modal-footer {
            padding: 20px 30px;
            background-color: #1a1a1a;
            border-top: 1px solid #444;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff8a00 0%, #ffb347 100%);
            color: #1a1a1a;
            font-weight: 600;
        }

        .btn-success {
            background: linear-gradient(135deg, #ff8a00 0%, #ffb347 100%);
            color: #1a1a1a;
            font-weight: 600;
        }

        .btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn.loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="admin-dashboard">

<!-- Show session messages -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php include '../partials/adminsidebar.php'; ?>
<!-- Main Content -->
<div class="main-content" id="mainContent">
    <div class="content-header">
        <h2 class="page-title">Manage Members</h2>
        <div class="header-actions">
            <button class="settings-btn"><i class="fas fa-cog"></i></button>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-welcome">
            <h3>Member Management</h3>
            <p>View, add, filter, and manage all gym members below.</p>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search members..." id="searchInput" onkeyup="filterTable()">
            </div>
            <div class="filter-controls">
                <select id="filterMembership" onchange="filterTable()">
                    <option value="">All Membership Types</option>
                    <option value="basic">Basic</option>
                    <option value="premium">Premium</option>
                    <option value="vip">VIP</option>
                </select>
                <select id="filterStatus" onchange="filterTable()">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="expired">Expired</option>
                    <option value="pending">Pending</option>
                </select>
                <button class="btn btn-primary" onclick="openModal('addModal')" style="background: linear-gradient(135deg, #ff8a00 0%, #ffb347 100%); color: #1a1a1a; font-weight: 600;">
                    <i class="fas fa-user-plus"></i> Add Member
                </button>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><span class="badge <?= $row['package'] ?>"><?= ucfirst($row['package']) ?></span></td>
                                <td><span class="badge <?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
                                <td>
                                    <button class="btn btn-edit" onclick="editUser(<?= json_encode($row) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="#" onclick="return confirm('Are you sure?') && deleteUser(<?= $row['id'] ?>)" class="btn btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">No members found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Add New Member</h3>
            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <form action="user_crud.php" method="POST" id="addForm">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" name="first_name" id="first_name" required autocomplete="given-name">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" name="last_name" id="last_name" required autocomplete="family-name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required minlength="6" autocomplete="new-password">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="package">Membership Type</label>
                        <select name="package" id="package" required autocomplete="off">
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" required autocomplete="off">
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" name="create" class="btn btn-success">
                    <i class="fas fa-user-plus"></i> Add Member
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Member Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-user-edit"></i> Edit Member</h3>
            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <form id="editForm" action="user_crud.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_first_name">First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" required autocomplete="given-name">
                    </div>
                    <div class="form-group">
                        <label for="edit_last_name">Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" required autocomplete="family-name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" name="username" id="edit_username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" name="email" id="edit_email" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="edit_password">Password (optional)</label>
                    <input type="password" name="password" id="edit_password" autocomplete="new-password">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_package">Membership Type</label>
                        <select name="package" id="edit_package" required autocomplete="off">
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select name="status" id="edit_status" required autocomplete="off">
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" name="update" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Member
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Run only after DOM loads
    document.addEventListener("DOMContentLoaded", function () {
        const addForm = document.getElementById('addForm');
        const editForm = document.getElementById('editForm');

        if (addForm) {
            addForm.addEventListener('submit', function (e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });
        }

        if (editForm) {
            editForm.addEventListener('submit', function (e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });
        }

        // Sidebar toggle
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        if (toggleBtn && sidebar && mainContent) {
            toggleBtn.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            });

            // Restore sidebar state
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            }
        }

        // Settings dropdown
        const settingsBtn = document.querySelector('.settings-btn');
        const dropdownContent = document.querySelector('.dropdown-content');

        if (settingsBtn && dropdownContent) {
            settingsBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdownContent.classList.toggle('show');
            });

            document.addEventListener('click', function () {
                if (dropdownContent.classList.contains('show')) {
                    dropdownContent.classList.remove('show');
                }
            });
        }
    });

    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'block';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.animation = 'modalFadeOut 0.3s ease-out forwards';
            setTimeout(() => {
                modal.style.display = 'none';
                modal.style.animation = '';
            }, 300);
        }
    }

    function editUser(userData) {
        console.log("Edit user called", userData);
        document.getElementById('edit_id').value = userData.id;
        document.getElementById('edit_first_name').value = userData.first_name;
        document.getElementById('edit_last_name').value = userData.last_name;
        document.getElementById('edit_username').value = userData.username;
        document.getElementById('edit_email').value = userData.email;
        document.getElementById('edit_package').value = userData.package;
        document.getElementById('edit_status').value = userData.status;
        document.getElementById('edit_password').value = '';
        openModal('editModal');
    }

    function deleteUser(id) {
        window.location.href = "user_crud.php?delete=" + id;
    }

    function suspendUser(id) {
        window.location.href = "user_crud.php?suspend=" + id;
    }

    function renewUser(id) {
        window.location.href = "user_crud.php?renew=" + id;
    }

    function filterTable() {
        const table = document.querySelector(".user-table tbody");
        const rows = table ? table.getElementsByTagName("tr") : [];
        const search = document.getElementById("searchInput")?.value.toLowerCase() || '';
        const packageFilter = document.getElementById("filterMembership")?.value.toLowerCase() || '';
        const statusFilter = document.getElementById("filterStatus")?.value.toLowerCase() || '';

        Array.from(rows).forEach(row => {
            const cells = row.getElementsByTagName("td");
            if (cells.length < 6) return;

            const name = (cells[1]?.innerText || '').toLowerCase();
            const email = (cells[2]?.innerText || '').toLowerCase();
            const membership = (cells[3]?.innerText.trim() || '').toLowerCase();
            const status = (cells[4]?.innerText.trim() || '').toLowerCase();

            const matchesSearch = name.includes(search) || email.includes(search);
            const matchesPackage = !packageFilter || membership === packageFilter;
            const matchesStatus = !statusFilter || status === statusFilter;

            row.style.display = (matchesSearch && matchesPackage && matchesStatus) ? "" : "none";
        });
    }
</script>
</body>
</html>