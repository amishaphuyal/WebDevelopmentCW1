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

// Fetch prescriptions for the logged-in doctor
$query = "SELECT 
            p.id AS prescription_id, 
            p.medicine, 
            p.dosage, 
            p.notes, 
            u.name AS patient_name, 
            a.date, 
            a.time 
          FROM prescriptions p 
          JOIN appointments a ON p.appointment_id = a.id 
          JOIN users u ON p.patient_id = u.id 
          WHERE p.doctor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
includes '../includes/noti_header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>View Prescriptions</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f9f9f9; }
        header { background-color: #4CAF50; color: white; padding: 1rem; text-align: center; }
        h1 { text-align: center; color: #333; }
        table { width: 90%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); }
        table th, table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        table th { background-color: #4CAF50; color: white; }
        table tr:hover { background-color: #f1f1f1; }
        p { text-align: center; margin-top: 20px; color: #333; }
        footer { text-align: center; padding: 10px; background-color: #4CAF50; color: white; }
    </style>
</head>
<body>
    <header>
        <h2>Doctor Dashboard - View Prescriptions</h2>
    </header>
    <h1>My Prescriptions</h1>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Prescription ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Medicines</th>
                    <th>Dosage</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['prescription_id']) ?></td>
                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?> at <?= htmlspecialchars($row['time']) ?></td>
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
    <footer>
        <p>&copy; 2024 Hospital Management System</p>
    </footer>
</body>
</html>

