<?php
// manage_users.php

include '../includes/con.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$edit_user = null;

// Handle Delete User Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];
    $check_query = "SELECT id FROM users WHERE id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $delete_user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $delete_query = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_query);
        $delete_stmt->bind_param("i", $delete_user_id);
        if ($delete_stmt->execute()) {
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
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    $update_query = "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssi", $name, $email, $role, $edit_user_id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>User updated successfully.</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: Could not update user details.</div>";
    }
}

// Fetch User Details for Editing
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_user_id'])) {
    $edit_user_id = $_GET['edit_user_id'];
    $query = "SELECT id, name, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $edit_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_user = $result->fetch_assoc();
    } else {
        $message = "<div class='alert alert-error'>Error: User not found.</div>";
    }
}

// Fetch all non-admin users
$query = "SELECT id, name, email, role FROM users WHERE role != 'admin'";
$result = $conn->query($query);

// (Optional) Include an admin header if you have a standard header file
// include '../includes/admin_header.php';
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
      color: #333;
    }

    /* Navigation Bar */
    .navbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #333;
      padding: 0 20px;
      height: 60px;
    }
    .navbar .nav-brand {
      color: #fff;
      font-size: 1.2rem;
      font-weight: 700;
      text-decoration: none;
    }
    .nav-links {
      list-style: none;
      display: flex;
      gap: 15px;
    }
    .nav-links li {
      position: relative;
    }
    .nav-links a {
      color: #fff;
      text-decoration: none;
      padding: 8px 0;
      transition: color 0.3s ease;
      font-size: 0.95rem;
    }
    .nav-links a:hover {
      color: #ddd;
    }

    /* Main Container */
    .main-container {
      width: 90%;
      max-width: 900px;
      margin: 20px auto;
      padding: 20px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .main-container h1 {
      text-align: center;
      margin-bottom: 20px;
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

    /* Edit Form Container */
    .edit-form {
      margin-bottom: 20px;
      padding: 20px;
      background: #fafafa;
      border: 1px solid #ddd;
      border-radius: 8px;
      animation: fadeIn 0.3s ease;
    }
    .edit-form h2 {
      margin-bottom: 15px;
      font-size: 1.5rem;
      color: #333;
    }
    .edit-form label {
      display: block;
      margin: 10px 0 5px;
      font-weight: 600;
    }
    .edit-form input,
    .edit-form select {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .edit-form button {
      padding: 10px 15px;
      border: none;
      background-color: #28a745;
      color: #fff;
      border-radius: 4px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    .edit-form button:hover {
      background-color: #218838;
    }

    /* Table Wrapper */
    .table-wrapper {
      overflow-x: auto;
      margin-top: 20px;
    }

    /* Table Styles */
    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 600px; /* Ensures table is wide enough for columns */
      background: #fff;
      border-radius: 5px;
      overflow: hidden;
    }
    thead {
      background: #4CAF50;
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

    /* Action Buttons */
    .btn {
      display: inline-block;
      padding: 6px 10px;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      color: #fff;
      margin-right: 5px;
      font-size: 0.85rem;
      transition: background 0.3s ease;
    }
    .btn-edit {
      background: #007bff;
    }
    .btn-delete {
      background: #dc3545;
    }
    .btn-edit:hover {
      background: #0056b3;
    }
    .btn-delete:hover {
      background: #bd2130;
    }

    /* Footer */
    footer {
      background-color: #333;
      color: #fff;
      text-align: center;
      padding: 10px 0;
      margin-top: 40px;
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(5px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar">
  <a href="#" class="nav-brand">Hospital Management</a>
  <ul class="nav-links">
    <li><a href="index.php">Dashboard</a></li>
    <li><a href="manage_patients.php">Manage Patients</a></li>
    <li><a href="manage_users.php">Manage users</a></li>
    <li><a href="../methods/logout.php">Logout</a></li>
  </ul>
</div>

<!-- Main Container -->
<div class="main-container">
  <h1>Manage Users</h1>

  <!-- Display message if any -->
  <?php if (!empty($message)) echo $message; ?>

  <!-- Edit User Form -->
  <?php if ($edit_user): ?>
    <div class="edit-form">
      <h2>Edit User</h2>
      <form action="" method="POST">
        <input type="hidden" name="edit_user_id" value="<?= htmlspecialchars($edit_user['id']) ?>">

        <label for="name">Name:</label>
        <input
          type="text"
          id="name"
          name="name"
          value="<?= htmlspecialchars($edit_user['name']) ?>"
          required
        >

        <label for="email">Email:</label>
        <input
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($edit_user['email']) ?>"
          required
        >

        <label for="role">Role:</label>
        <select id="role" name="role" required>
          <option value="patient" <?= $edit_user['role'] === 'patient' ? 'selected' : '' ?>>Patient</option>
          <option value="doctor" <?= $edit_user['role'] === 'doctor' ? 'selected' : '' ?>>Doctor</option>
        </select>

        <button type="submit">Update User</button>
      </form>
    </div>
  <?php endif; ?>

  <!-- Users Table -->
  <div class="table-wrapper">
    <table>
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
                <a
                  href="manage_users.php?edit_user_id=<?= htmlspecialchars($row['id']) ?>"
                  class="btn btn-edit"
                >
                  Edit
                </a>
                <form
                  action=""
                  method="POST"
                  style="display:inline-block;"
                  onsubmit="return confirm('Are you sure you want to delete this user?');"
                >
                  <input type="hidden" name="delete_user_id" value="<?= htmlspecialchars($row['id']) ?>">
                  <button
                    type="submit"
                    class="btn btn-delete"
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
</div>

<footer>
  &copy; <?= date("Y") ?> Hospital Management System. All rights reserved.
</footer>

</body>
</html>

