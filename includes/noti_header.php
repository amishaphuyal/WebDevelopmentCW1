<?php
@session_start();
include_once 'con.php';

// Check user role
$role = $_SESSION['role'] ?? 'guest';

// Fetch unread notifications count
$user_id = $_SESSION['user_id'] ?? null;
$notification_count = 0;
if ($user_id) {
    $query = "SELECT COUNT(*) AS unread_count FROM notifications WHERE user_id = ? AND is_read = FALSE";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notification = $result->fetch_assoc();
    $notification_count = $notification['unread_count'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Hospital Management System</title>
    <style>
        /* Navbar Styles */
        .navbar {
            background-color:  rgba(242,144,69,255);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar h1 {
            font-size: 1.8rem;
            margin: 0;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 1rem;
            margin: 0;
        }

        .navbar ul li {
            display: inline-block;
            border-radius: 3px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navbar ul li a:hover {
            background-color:rgb(22, 124, 179);
        }

        .notification-badge {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>HMS</h1>
        <ul>
            <!-- Links for Admin -->
            <?php if ($role === 'admin'): ?>
                
                <li><a href="/html/Admin/index.php">Dashboard</a></li>
                <li><a href="/html/Admin/manage_patients.php">Manage Patients</a></li>
                <li><a href="/html/Admin/manage_notifications.php">Notifications</a></li>
                <li><a href="/html/views/notification.php">View Notifications</a> <span class="notification-badge"><?= $notification_count ?></span></li>
            <?php endif; ?>

            <!-- Links for Doctor -->
            <?php if ($role === 'doctor'): ?>
                <li><a href="/html/doctor/index.php">Dashboard</a></li>
                <li><a href="/html/doctor/manage_appointments.php">Manage Appointments</a></li>
                <li><a href="/html/doctor/add_prescription.php">Prescriptions</a></li>
                <li><a href="/html/doctor/profile.php">Profile</a></li>
                <li><a href="/html/views/notification.php">Notifications</a> <span class="notification-badge"><?= $notification_count ?></span></li>
            <?php endif; ?>

            <!-- Links for Patient -->
            <?php if ($role === 'patient'): ?>
                <li><a href="/html/patient/index.php">Dashboard</a></li>
                <li><a href="/html/patient/book_appointment.php">Book Appointment</a></li>
                <li><a href="/html/views/notification.php">Notifications</a> <span class="notification-badge"><?= $notification_count ?></span></li>
            <?php endif; ?>

            <!-- Logout -->
            <?php if ($role !== 'guest'): ?>
                <li><a href="../../../../html/methods/logout.php">Logout</a></li>
            <?php else: ?>
                <!-- For Guests -->
                <li><a href="/login.php">Login</a></li>
                <li><a href="/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>
