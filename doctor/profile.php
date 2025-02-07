<?php
include_once '../includes/con.php';
session_start();

// Ensure session variables are set
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header('Location: ../login.php');
    exit();
}

$doctor_id = $_SESSION['user_id'];
if (!isset($doctor_id)) {
    die("Error: User ID is not set in the session.");
}

// Check database connection
if (!$conn) {
    die("Error: Unable to connect to the database. " . $conn->connect_error);
}

// Fetch the doctor's profile
$query = "SELECT name, specialty, contact FROM users WHERE id = ? AND role = 'doctor'";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Error: Failed to prepare the query. " . $conn->error);
}

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<p>No data found for the logged-in doctor.</p>");
}

$doctor = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Doctor Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        h1 { text-align: center; color: #333; }
        form { max-width: 400px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input[type="text"] { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        p { text-align: center; color: #333; }
        footer { text-align: center; padding: 10px; background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <header>
        <h2>Doctor Dashboard - Profile</h2>
    </header>
    <h1>My Profile</h1>
    <form action="" method="POST">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($doctor['name'] ?? '') ?>" required>

        <label for="specialty">Specialty:</label>
        <input type="text" id="specialty" name="specialty" value="<?= htmlspecialchars($doctor['specialty'] ?? '') ?>" required>

        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?= htmlspecialchars($doctor['contact'] ?? '') ?>" required>

        <button type="submit">Update</button>
    </form>
    <?php if (!empty($message)): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>
</body>
</html>

