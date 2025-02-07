<?php
// admin_header.php
// This file should be included at the beginning of every admin page.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hospital Admin Panel</title>
  <style>
    /* Unified Navigation Bar Styles */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #333;
      padding: 10px 20px;
      color: #fff;
    }
    .nav-left, .nav-right {
      display: flex;
      align-items: center;
    }
    .nav-left a,
    .nav-right a {
      color: #fff;
      text-decoration: none;
      margin-right: 15px;
      font-size: 16px;
      transition: color 0.2s ease;
    }
    .nav-left a:last-child {
      margin-right: 0;
    }
    .nav-right a {
      margin-left: 15px;
      margin-right: 0;
    }
    .navbar a:hover {
      color: #ddd;
    }
    /* Active Link Style */
    .active {
      font-weight: bold;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <div class="nav-left">
      <a href="index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Dashboard</a>
      <a href="manage_notifications.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage_notifications.php') ? 'active' : '' ?>">Notifications</a>
      <a href="manage_patients.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage_patients.php') ? 'active' : '' ?>">Manage Patients</a>
      <a href="manage_users.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage_users.php') ? 'active' : '' ?>">Manage Users</a>
    </div>
    <div class="nav-right">
      <a href="../methods/logout.php">Logout</a>
    </div>
  </div>
