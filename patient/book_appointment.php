<?php
// Include database connection
include_once '../includes/con.php';
session_start();

// Ensure the user is logged in as a patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: ../login.php');
    exit();
}

$patient_id = $_SESSION['user_id'];
$message = '';

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Validate inputs
    if (empty($doctor_id) || empty($date) || empty($time)) {
        $message = "<span style='color: red;'>All fields are required.</span>";
    } else {
        // Check if the doctor is available at the selected time
        $availability_query = "
            SELECT * FROM appointments 
            WHERE doctor_id = ? AND date = ? AND time = ?";
        $stmt = $conn->prepare($availability_query);
        $stmt->bind_param("iss", $doctor_id, $date, $time);
        $stmt->execute();
        $availability_result = $stmt->get_result();

        if ($availability_result->num_rows > 0) {
            $message = "<span style='color: red;'>The doctor is not available at the selected time. Please choose a different time.</span>";
        } else {
            // Insert the appointment into the database
            $query = "INSERT INTO appointments (patient_id, doctor_id, date, time) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiss", $patient_id, $doctor_id, $date, $time);
            if ($stmt->execute()) {
                $message = "<span style='color: green;'>Appointment booked successfully.</span>";
            } else {
                $message = "<span style='color: red;'>Error: Could not book the appointment. Please try again.</span>";
            }
        }
    }
}

// Fetch all doctors
$doctors = $conn->query("SELECT id, name FROM users WHERE role = 'doctor'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Book Appointment</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        form { max-width: 600px; margin: 20px auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        label, select, input { display: block; width: 100%; margin-bottom: 15px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #45a049; }
        .message { text-align: center; font-weight: bold; margin: 15px 0; }
    </style>
</head>
<body>
    <header>
        <h1>Book an Appointment</h1>
    </header>
    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <form action="" method="POST">
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

        <button type="submit">Book Appointment</button>
    </form>
</body>
</html>

