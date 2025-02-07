<?php
// Include the database connection
include_once '../includes/con.php';
session_start();

// Ensure the user is logged in as a doctor
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header('Location: ../login.php');
    exit();
}

$doctor_id = $_SESSION['user_id'];
$message = ''; // Variable to store success or error message

// Handle the prescription submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_POST['patient_id'];
    $medicine = $_POST['medicine'];
    $dosage = $_POST['dosage'];
    $notes = $_POST['notes'];

    // Validate inputs
    if (empty($appointment_id) || empty($patient_id) || empty($medicine) || empty($dosage)) {
        $message = "Error: All fields are required.";
    } else {
        // Insert the prescription into the database
        $query = "INSERT INTO prescriptions (appointment_id, doctor_id, patient_id, medicine, dosage, notes) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiisss", $appointment_id, $doctor_id, $patient_id, $medicine, $dosage, $notes);

        if ($stmt->execute()) {
            $message = "<span style='color: green;'>Prescription added successfully.</span>";
        } else {
            $message = "<span style='color: red;'>Error: Failed to add prescription. " . htmlspecialchars($conn->error) . "</span>";
        }
    }
}

// Fetch appointments for the logged-in doctor
$query = "SELECT a.id AS appointment_id, u.id AS patient_id, u.name AS patient_name, a.date, a.time 
          FROM appointments a 
          JOIN users u ON a.patient_id = u.id 
          WHERE a.doctor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$appointments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Add Prescription</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        h1 { text-align: center; color: #333; }
        form { max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        label { font-weight: bold; margin-top: 10px; display: block; }
        textarea, input[type="text"], select { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; padding: 10px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
        p.message { text-align: center; font-weight: bold; }
        footer { text-align: center; padding: 10px; background-color: #4CAF50; color: white; }
            /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #333;
      padding: 15px 20px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .nav-brand {
      color: #fff;
      font-size: 1.5rem;
      font-weight: 700;
      text-decoration: none;
    }
    .nav-links {
      display: flex;
      list-style: none;
      gap: 20px;
    }
    .nav-links li {
      position: relative;
    }
    .nav-links a {
      color: #fff;
      text-decoration: none;
      padding: 10px;
      font-size: 1rem;
      font-weight: 600;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    .nav-links a:hover {
      background-color:rgb(26, 157, 186);
      color: #fff;
    }
    </style>
</head>
<body>

<div class="navbar">
    <a href="#" class="nav-brand">Doctor Dashboard</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="manage_appointments.php">Appointments</a></li>
      <li><a href="add_prescription.php">add prescription</a></li>
      <li><a href="../methods/logout.php">Logout</a></li>
    </ul>
  </div>
    
    <h1>Add Prescription</h1>
    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="appointment_id">Select Appointment:</label>
        <select id="appointment_id" name="appointment_id" required>
            <option value="">-- Select Appointment --</option>
            <?php while ($row = $appointments->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['appointment_id']) ?>" data-patient-id="<?= htmlspecialchars($row['patient_id']) ?>">
                    Appointment ID: <?= htmlspecialchars($row['appointment_id']) ?> - 
                    <?= htmlspecialchars($row['patient_name']) ?> on <?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="patient_id">Patient ID:</label>
        <input type="text" id="patient_id" name="patient_id" readonly required>

        <label for="medicine">Medicine:</label>
        <textarea id="medicine" name="medicine" rows="3" required></textarea>

        <label for="dosage">Dosage:</label>
        <textarea id="dosage" name="dosage" rows="3" required></textarea>

        <label for="notes">Additional Notes:</label>
        <textarea id="notes" name="notes" rows="4"></textarea>

        <button type="submit">Add Prescription</button>
    </form>
    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>

    <script>
        // Auto-fill the patient ID when an appointment is selected
        const appointmentSelect = document.getElementById('appointment_id');
        const patientIdInput = document.getElementById('patient_id');
        appointmentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const patientId = selectedOption.getAttribute('data-patient-id');
            patientIdInput.value = patientId || '';
        });
    </script>
</body>
</html>

