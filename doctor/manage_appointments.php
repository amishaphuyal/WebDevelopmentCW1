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
$message = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];

    // Validate status
    if (!in_array($status, ['Confirmed', 'Cancelled'])) {
        $message = "<span style='color: red;'>Invalid status value.</span>";
    } else {
        // Update the appointment status in the database
        $query = "UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $status, $appointment_id, $doctor_id);

        if ($stmt->execute()) {
            $message = "<span style='color: green;'>Appointment status updated successfully.</span>";
        } else {
            $message = "<span style='color: red;'>Error: Could not update appointment status. Please try again.</span>";
        }
    }
}

// Fetch appointments for the logged-in doctor
$query = "
    SELECT a.id AS appointment_id, u.name AS patient_name, a.date, a.time, a.status 
    FROM appointments a 
    JOIN users u ON a.patient_id = u.id 
    WHERE a.doctor_id = ?
    ORDER BY a.date, a.time";
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
    <title>Manage Appointments</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            line-height: 1.6;
        }

        /* Header Styles */
        header {
            background-color: #4CAF50;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        /* Navbar Styles */
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
      background-color: #4CAF50;
      color: #fff;
    }
        /* Message Box */
        .message {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
        }

        /* Table Styles */
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Form Elements */
        select, button {
            padding: 8px 12px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        button {
            background-color: #4CAF50;
            color: white;
        }

        button:hover {
            background-color: #45a049;
        }

        select {
            background-color: #fff;
        }

        select:hover {
            background-color: #f1f1f1;
        }

        form {
            display: inline-block;
        }

        /* Footer */
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
            }

            nav a {
                padding: 10px;
                text-align: left;
                width: 100%;
            }

            table {
                width: 100%;
            }
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
    <!-- Message Display -->
    <?php if (!empty($message)): ?>
        <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <!-- Appointment Table -->
    <table>
        <thead>
            <tr>
                <th>Appointment ID</th>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($appointments->num_rows > 0): ?>
                <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['appointment_id']) ?></td>
                        <td><?= htmlspecialchars($row['patient_name']) ?></td>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['time']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($row['appointment_id']) ?>">
                                <select name="status" required>
                                    <option value="">-- Select --</option>
                                    <option value="Confirmed">Confirmed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Your Clinic. All Rights Reserved.</p>
    </footer>
</body>
</html>
