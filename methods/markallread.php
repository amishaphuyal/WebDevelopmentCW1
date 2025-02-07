<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_all_read'])) {
    $mark_query = "UPDATE notifications SET is_read = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($mark_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header('Location: notifications.php');
    exit();
}
?>
