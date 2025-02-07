<?php
include_once '../includes/con.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';

// Handle notification creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notification'])) {
    $user_id = $_POST['user_id'];
    $message_text = $_POST['message'];

    if (!empty($user_id) && !empty($message_text)) {
        $query = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $user_id, $message_text);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Notification added successfully.</div>";
        } else {
            $message = "<div class='alert alert-error'>Error: Failed to add notification.</div>";
        }
    } else {
        $message = "<div class='alert alert-error'>All fields are required.</div>";
    }
}

// Handle marking a notification as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];
    $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification_id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Notification marked as read.</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: Could not mark notification as read.</div>";
    }
}

// Handle deleting a notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_notification'])) {
    $notification_id = $_POST['notification_id'];
    $query = "DELETE FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notification_id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Notification deleted successfully.</div>";
    } else {
        $message = "<div class='alert alert-error'>Error: Could not delete notification.</div>";
    }
}

// Fetch all users for the notification form
$users = $conn->query("SELECT id, name, role FROM users");

// Fetch all notifications (most recent first)
$notifications = $conn->query("SELECT n.id, n.message, n.is_read, u.name AS user_name 
                               FROM notifications n 
                               JOIN users u ON n.user_id = u.id
                               ORDER BY n.id DESC");

include '../includes/admin_header.php';
?>
<div class="container" style="width:90%; max-width:900px; margin:20px auto; padding:20px; background:#fff; box-shadow:0 2px 4px rgba(0,0,0,0.1); border-radius:8px;">
    <h1 style="text-align:center; color:#333;">Manage Notifications</h1>
    <?php if (!empty($message)) echo $message; ?>

    <!-- Notification Creation Form -->
    <form action="" method="POST" style="margin-bottom:20px;">
        <label for="user_id" style="display:block; margin-bottom:5px;">Select User:</label>
        <select id="user_id" name="user_id" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ccc; border-radius:4px;">
            <option value="">-- Select User --</option>
            <?php while ($row = $users->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['id']) ?>">
                    <?= htmlspecialchars($row['name']) ?> (<?= htmlspecialchars($row['role']) ?>)
                </option>
            <?php endwhile; ?>
        </select>
        <label for="message" style="display:block; margin-bottom:5px;">Notification Message:</label>
        <textarea id="message" name="message" rows="3" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; margin-bottom:15px;"></textarea>
        <button type="submit" name="add_notification" style="padding:10px 15px; border:none; background-color:#4CAF50; color:#fff; border-radius:4px; cursor:pointer;">Add Notification</button>
    </form>

    <!-- Notifications Table -->
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            <thead style="background-color:#4CAF50; color:#fff;">
                <tr>
                    <th style="padding:12px; border:1px solid #ddd;">Notification ID</th>
                    <th style="padding:12px; border:1px solid #ddd;">User Name</th>
                    <th style="padding:12px; border:1px solid #ddd;">Message</th>
                    <th style="padding:12px; border:1px solid #ddd;">Status</th>
                    <th style="padding:12px; border:1px solid #ddd;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($notifications && $notifications->num_rows > 0): ?>
                    <?php while ($row = $notifications->fetch_assoc()): ?>
                        <tr>
                            <td style="padding:12px; border:1px solid #ddd; text-align:center;"><?= htmlspecialchars($row['id']) ?></td>
                            <td style="padding:12px; border:1px solid #ddd; text-align:center;"><?= htmlspecialchars($row['user_name']) ?></td>
                            <td style="padding:12px; border:1px solid #ddd; text-align:center;"><?= htmlspecialchars($row['message']) ?></td>
                            <td style="padding:12px; border:1px solid #ddd; text-align:center;"><?= $row['is_read'] ? 'Read' : 'Unread' ?></td>
                            <td style="padding:12px; border:1px solid #ddd; text-align:center;">
                                <?php if (!$row['is_read']): ?>
                                    <form action="" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="notification_id" value="<?= htmlspecialchars($row['id']) ?>">
                                        <button type="submit" name="mark_read" style="padding:6px 10px; border:none; background-color:#007bff; color:#fff; border-radius:4px; cursor:pointer;">Mark as Read</button>
                                    </form>
                                <?php endif; ?>
                                <form action="" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this notification?');">
                                    <input type="hidden" name="notification_id" value="<?= htmlspecialchars($row['id']) ?>">
                                    <button type="submit" name="delete_notification" style="padding:6px 10px; border:none; background-color:#f44336; color:#fff; border-radius:4px; cursor:pointer;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:12px;">No notifications found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<footer style="background-color:#333; color:#fff; text-align:center; padding:10px 0;">
    &copy; <?= date("Y") ?> Hospital Management System. All rights reserved.
</footer>
</body>
</html>
