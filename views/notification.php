<?php
// Include the database connection
include_once '../includes/con.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user ID
$message = ''; // To store success/error messages

// Mark all notifications as read if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    $mark_query = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($mark_query);
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $message = "<span style='color: green;'>All notifications marked as read successfully.</span>";
    } else {
        $message = "<span style='color: red;'>Error: Failed to mark notifications as read.</span>";
    }
}

// Fetch notifications for the logged-in user
$query = "SELECT message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notifications = $stmt->get_result();
include '../includes/noti_header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        .message { text-align: center; font-weight: bold; margin: 15px 0; }
        form { text-align: center; margin: 20px 0; }
        button { 
            background-color: #4CAF50; 
            color: white; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px; 
        }
        button:hover { background-color: #45a049; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; }
        table th, table td { padding: 12px; border: 1px solid #ddd; }
        table th { background-color: #4CAF50; color: white; }
        table tr:hover { background-color: #f1f1f1; }
    </style>
</head>
<body>
    <header>
        <h1>My Notifications</h1>
    </header>
    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <button type="submit" name="mark_all_read">Mark All as Read</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($notifications->num_rows > 0): ?>
                <?php while ($row = $notifications->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align: center;">No notifications found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

