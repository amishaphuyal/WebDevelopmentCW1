<?php
// Include the database connection
include_once '../includes/con.php';
session_start();

// Ensure the user is logged in (either as a doctor or admin)
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'doctor' && $_SESSION['role'] !== 'admin')) {
    header('Location: ../login.php');
    exit();
}

$message = ''; // Variable to store success or error message

// Handle the appointment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];

    // Validate inputs
    if (empty($patient_id) || empty($doctor_id) || empty($date) || empty($time) || empty($status)) {
        $message = "<span style='color: red;'>Error: All fields are required.</span>";
    } else {
        // Insert the appointment into the database
        $query = "INSERT INTO appointments (patient_id, doctor_id, date, time, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time, $status);

        if ($stmt->execute()) {
            $message = "<span style='color: green;'>Appointment added successfully.</span>";
        } else {
            $message = "<span style='color: red;'>Error: Failed to add appointment. " . htmlspecialchars($conn->error) . "</span>";
        }
    }
}

// Fetch all patients and doctors
$patients = $conn->query("SELECT id, name FROM users WHERE role = 'patient'");
$doctors = $conn->query("SELECT id, name FROM users WHERE role = 'doctor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Add Appointment</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        h1 { text-align: center; color: #333; }
        form { max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        label { font-weight: bold; margin-top: 10px; display: block; }
        select, input[type="text"], input[type="date"], input[type="time"] { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        p.message { text-align: center; font-weight: bold; }
        footer { text-align: center; padding: 10px; background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <header>
        <h2>Admin/Doctor Dashboard - Add Appointment</h2>
    </header>
    <h1>Add Appointment</h1>
    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="patient_id">Select Patient:</label>
        <select id="patient_id" name="patient_id" required>
            <option value="">-- Select Patient --</option>
            <?php while ($row = $patients->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="doctor_id">Select Doctor:</label>
        <select id="doctor_id" name="doctor_id" required>
            <option value="">-- Select Doctor --</option>
            <?php while ($row = $doctors->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['id']) ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label for="date">Appointment Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="time">Appointment Time:</label>
        <input type="time" id="time" name="time" required>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="">-- Select Status --</option>
            <option value="Confirmed">Confirmed</option>
            <option value="Pending">Pending</option>
            <option value="Cancelled">Cancelled</option>
        </select>

        <button type="submit">Add Appointment</button>
    </form>
    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>
</body>
</html>

