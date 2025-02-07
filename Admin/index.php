<?php
// index.php

include '../includes/con.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';

// Handle Add New User Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role  = trim($_POST['role']);

    // Check if a user with the given email already exists
    $check_query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultCheck = $stmt->get_result();

    if ($resultCheck->num_rows > 0) {
        $message = "<div class='alert alert-error'>A user with this email already exists.</div>";
    } else {
        // Assign a default password (hashed) for new users – adjust as needed.
        $defaultPassword = password_hash("default123", PASSWORD_DEFAULT);
        $insert_query  = "INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssss", $name, $email, $role, $defaultPassword);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>New user added successfully.</div>";
        } else {
            $message = "<div class='alert alert-error'>Error: Could not add new user.</div>";
        }
    }
}

// Handle Delete User Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];
    $check_query = "SELECT id FROM users WHERE id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $delete_user_id);
    $stmt->execute();
    $resultCheck = $stmt->get_result();

    if ($resultCheck->num_rows > 0) {
        $delete_query = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $delete_user_id);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>User deleted successfully.</div>";
        } else {
            $message = "<div class='alert alert-error'>Error: Failed to delete user.</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>Error: User not found.</div>";
    }
}

// Handle Edit User Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $edit_user_id = $_POST['edit_user_id'];
    $name         = trim($_POST['name']);
    $email        = trim($_POST['email']);
    $role         = trim($_POST['role']);

    $update_query = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssi", $name, $email, $role, $edit_user_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>User updated successfully.</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: Could not update user details.</div>";
    }
}

// Fetch all non-admin users for display
$query  = "SELECT id, name, email, role FROM users WHERE role != 'admin'";
$result = $conn->query($query);

// Include the unified admin header (adjust path if needed)
include '../includes/admin_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <!-- Optional: Google Font -->
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

  <style>
    /* Base Reset & Font */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Nunito', sans-serif;
      background: #f5f5f5;
      color: #444;
    }

    /* Container */
    .main-container {
      width: 90%;
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }
    .main-container h1 {
      text-align: center;
      margin-bottom: 20px;
      font-weight: 700;
      font-size: 2rem;
      color: #333;
    }

    /* Alerts */
    .alert {
      padding: 12px 20px;
      margin-bottom: 20px;
      border-radius: 4px;
      font-weight: 600;
      animation: fadeIn 0.3s ease;
    }
    .alert-success {
      background: #d4edda;
      color: #155724;
    }
    .alert-error {
      background: #f8d7da;
      color: #721c24;
    }

    /* Button Styles */
    .btn {
      display: inline-block;
      padding: 8px 16px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s ease;
      font-size: 0.9rem;
    }
    .btn:hover {
      opacity: 0.9;
    }
    .btn-success {
      background: #28a745;
      color: #fff;
    }
    .btn-primary {
      background: #007bff;
      color: #fff;
    }
    .btn-danger {
      background: #dc3545;
      color: #fff;
    }
    .btn-secondary {
      background: #6c757d;
      color: #fff;
    }

    /* Add User Button Wrapper */
    .top-bar {
      text-align: right;
      margin-bottom: 20px;
    }

    /* Search Input */
    .search-input {
      width: 100%;
      padding: 10px;
      margin-bottom: 20px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }
    .search-input:focus {
      outline: none;
      border-color: #007bff;
    }

    /* Table Styles */
    table {
      width: 100%;
      border-collapse: collapse;
      overflow: hidden;
      border-radius: 5px;
    }
    thead {
      background: #007bff;
      color: #fff;
    }
    th, td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #ddd;
      font-size: 0.95rem;
    }
    tbody tr:hover {
      background: #f1f1f1;
      transition: background 0.3s ease;
    }

    /* Modal Overlay */
    .modal {
      display: none; /* Hidden by default */
      position: fixed; 
      top: 0; 
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      align-items: center;
      justify-content: center;
      opacity: 0;
      pointer-events: none;
      transition: opacity 0.3s ease;
    }
    .modal.active {
      display: flex;
      opacity: 1;
      pointer-events: auto;
    }

    /* Modal Content */
    .modal-content {
      background: #fff;
      width: 90%;
      max-width: 500px;
      border-radius: 5px;
      position: relative;
      padding: 20px;
      transform: translateY(-50px);
      transition: transform 0.4s ease, opacity 0.4s ease;
      opacity: 0;
    }
    .modal.active .modal-content {
      transform: translateY(0);
      opacity: 1;
    }

    /* Modal Header & Close */
    .modal-header h2 {
      margin: 0;
      font-size: 1.4rem;
      font-weight: 600;
      color: #333;
      margin-bottom: 10px;
    }
    .close {
      color: #aaa;
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover {
      color: #000;
    }

    /* Modal Body & Footer */
    .modal-body {
      margin-bottom: 20px;
    }
    .modal-body label {
      display: block;
      margin: 10px 0 5px;
      font-weight: 600;
    }
    .modal-body input, 
    .modal-body select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1rem;
    }
    .modal-footer {
      text-align: right;
    }

    /* Animation Keyframes */
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
  </style>
</head>
<body>

<div class="main-container">
    <h1>Manage Users</h1>
    <?php if (!empty($message)) echo $message; ?>

    <div class="top-bar">
        <button class="btn btn-success" id="openAddModal">Add New User</button>
    </div>

    <input 
      type="text" 
      id="userSearch" 
      class="search-input" 
      placeholder="Search users..."
    >

    <table id="usersTable">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['email']) ?></td>
              <td><?= htmlspecialchars($row['role']) ?></td>
              <td>
                <button 
                  class="btn btn-primary editBtn"
                  data-id="<?= htmlspecialchars($row['id']) ?>"
                  data-name="<?= htmlspecialchars($row['name']) ?>"
                  data-email="<?= htmlspecialchars($row['email']) ?>"
                  data-role="<?= htmlspecialchars($row['role']) ?>"
                >
                  Edit
                </button>
                <form 
                  action="" 
                  method="POST" 
                  style="display:inline-block;" 
                  onsubmit="return confirm('Are you sure you want to delete this user?');"
                >
                  <input type="hidden" name="delete_user_id" value="<?= htmlspecialchars($row['id']) ?>">
                  <button 
                    type="submit" 
                    class="btn btn-danger"
                  >
                    Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">No users found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div id="addModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeAddModal">&times;</span>
    <div class="modal-header">
      <h2>Add New User</h2>
    </div>
    <form action="" method="POST">
      <input type="hidden" name="action" value="add_user">
      <div class="modal-body">
        <label for="addName">Name</label>
        <input type="text" id="addName" name="name" required>

        <label for="addEmail">Email</label>
        <input type="email" id="addEmail" name="email" required>

        <label for="addRole">Role</label>
        <select id="addRole" name="role" required>
          <option value="patient">Patient</option>
          <option value="doctor">Doctor</option>
        </select>
        <p style="font-size: 0.85rem; color: #555;">
          Default password "default123" will be assigned.
        </p>
      </div>
      <div class="modal-footer">
        <button 
          type="button" 
          class="btn btn-secondary" 
          id="cancelAdd"
        >
          Cancel
        </button>
        <button 
          type="submit" 
          class="btn btn-success"
        >
          Add User
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeEditModal">&times;</span>
    <div class="modal-header">
      <h2>Edit User</h2>
    </div>
    <form action="" method="POST">
      <input type="hidden" name="edit_user_id" id="editUserId">
      <div class="modal-body">
        <label for="editName">Name</label>
        <input type="text" id="editName" name="name" required>

        <label for="editEmail">Email</label>
        <input type="email" id="editEmail" name="email" required>

        <label for="editRole">Role</label>
        <select id="editRole" name="role" required>
          <option value="patient">Patient</option>
          <option value="doctor">Doctor</option>
        </select>
      </div>
      <div class="modal-footer">
        <button 
          type="button" 
          class="btn btn-secondary" 
          id="cancelEdit"
        >
          Cancel
        </button>
        <button 
          type="submit" 
          class="btn btn-primary"
        >
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // ========= Add User Modal =========
  const addModal = document.getElementById("addModal");
  const openAddBtn = document.getElementById("openAddModal");
  const closeAddBtn = document.getElementById("closeAddModal");
  const cancelAddBtn = document.getElementById("cancelAdd");

  openAddBtn.onclick = function() {
    addModal.classList.add("active");
  };
  closeAddBtn.onclick = function() {
    addModal.classList.remove("active");
  };
  cancelAddBtn.onclick = function() {
    addModal.classList.remove("active");
  };

  // ========= Edit User Modal =========
  const editModal = document.getElementById("editModal");
  const closeEditBtn = document.getElementById("closeEditModal");
  const cancelEditBtn = document.getElementById("cancelEdit");
  const editButtons = document.querySelectorAll(".editBtn");

  editButtons.forEach(function(btn) {
    btn.addEventListener("click", function() {
      document.getElementById("editUserId").value = this.getAttribute("data-id");
      document.getElementById("editName").value  = this.getAttribute("data-name");
      document.getElementById("editEmail").value = this.getAttribute("data-email");
      document.getElementById("editRole").value  = this.getAttribute("data-role");
      editModal.classList.add("active");
    });
  });
  closeEditBtn.onclick = function() {
    editModal.classList.remove("active");
  };
  cancelEditBtn.onclick = function() {
    editModal.classList.remove("active");
  };

  // Close modals when clicking outside the modal content
  window.onclick = function(event) {
    if (event.target === addModal) {
      addModal.classList.remove("active");
    }
    if (event.target === editModal) {
      editModal.classList.remove("active");
    }
  };

  // ========= Client-side Table Search =========
  document.getElementById("userSearch").addEventListener("keyup", function() {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll("#usersTable tbody tr");

    rows.forEach(function(row) {
      row.style.display = row.textContent.toLowerCase().includes(value) ? "" : "none";
    });
  });
</script>

</body>
</html>

