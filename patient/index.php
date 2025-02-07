<?php
// Include the database connection
include_once '../includes/con.php';
session_start();

// Ensure the user is logged in as a patient
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: ../login.php');
    exit();
}

$patient_id = $_SESSION['user_id'];

// Fetch the patient's appointments
$appointments_query = "
    SELECT a.id AS appointment_id, d.name AS doctor_name, a.date, a.time, a.status 
    FROM appointments a 
    JOIN users d ON a.doctor_id = d.id 
    WHERE a.patient_id = ?";
$appointments_stmt = $conn->prepare($appointments_query);
$appointments_stmt->bind_param("i", $patient_id);
$appointments_stmt->execute();
$appointments = $appointments_stmt->get_result();

// Fetch the patient's prescriptions
$prescriptions_query = "
    SELECT p.id AS prescription_id, p.medicine, p.dosage, p.notes, d.name AS doctor_name 
    FROM prescriptions p 
    JOIN users d ON p.doctor_id = d.id 
    WHERE p.patient_id = ?";
$prescriptions_stmt = $conn->prepare($prescriptions_query);
$prescriptions_stmt->bind_param("i", $patient_id);
$prescriptions_stmt->execute();
$prescriptions = $prescriptions_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Patient Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        h1 { text-align: center; color: #333; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        table th, table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        table th { background-color: #4CAF50; color: white; }
        table tr:hover { background-color: #f1f1f1; }
        .section { margin: 20px auto; max-width: 90%; }
        footer { text-align: center; padding: 10px; background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <header>
        <h2>Patient Dashboard</h2>
    </header>
<?php
	include "../includes/noti_header.php"
?>
    <div class="section">
        <h1>My Appointments</h1>
        <?php if ($appointments->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Doctor Name</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($row['date']) ?></td>
                            <td><?= htmlspecialchars($row['time']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No appointments found.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h1>My Prescriptions</h1>
        <?php if ($prescriptions->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Prescription ID</th>
                        <th>Doctor Name</th>
                        <th>Medicines</th>
                        <th>Dosage</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $prescriptions->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['prescription_id']) ?></td>
                            <td><?= htmlspecialchars($row['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($row['medicine']) ?></td>
                            <td><?= htmlspecialchars($row['dosage']) ?></td>
                            <td><?= htmlspecialchars($row['notes']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No prescriptions found.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>
</body>
</html>

