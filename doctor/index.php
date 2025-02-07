<?php
// Start the session
session_start();

// Include the database connection
include_once '../includes/con.php';

// Ensure the user is logged in and has the 'doctor' role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header("Location: /login.php");
    exit();
}

// Get the doctor ID from the session
$doctor_id = $_SESSION['user_id'];
if (!isset($doctor_id)) {
    die("Error: User ID is not set in the session.");
}

// Check database connection
if (!$conn) {
    die("Error: Unable to connect to the database.");
}

// Fetch appointments for the logged-in doctor
$query = "
    SELECT a.id, u.name AS patient_name, a.date, a.time, a.status
    FROM appointments a
    JOIN users u ON a.patient_id = u.id
    WHERE a.doctor_id = ?
";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Error: Failed to prepare the query. " . $conn->error);
}

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Doctor Dashboard | View Appointments</title>
  <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">

  <style>
    /* Global Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    html, body {
      height: 100%;
      font-family: 'Nunito', sans-serif;
      background-color: #f4f7fc;
    }
    
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

    /* Page Header */
    .page-header {
      background-color: #4CAF50;
      color: white;
      text-align: center;
      padding: 20px;
      font-size: 1.5rem;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 20px;
      margin-bottom: 60px; /* Footer space */
    }

    /* Table */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 8px;
      background-color: #fff;
    }
    thead {
      background-color: #4CAF50;
      color: #fff;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #ddd;
      font-size: 0.95rem;
    }
    tbody tr:hover {
      background-color: #f9f9f9;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .no-appointments {
      text-align: center;
      font-size: 1.2rem;
      color: #888;
      margin-top: 40px;
    }

    /* Footer */
    footer {
      background-color: #333;
      color: white;
      padding: 15px;
      text-align: center;
      position: absolute;
      bottom: 0;
      width: 100%;
      font-size: 0.9rem;
    }

    /* Button Styles */
    .button {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 15px;
      font-size: 1rem;
      font-weight: 600;
      border-radius: 5px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .button:hover {
      background-color: #45a049;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }
      .nav-links {
        flex-direction: column;
        gap: 15px;
      }
    }

  </style>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const rows = document.querySelectorAll("table tbody tr");
      rows.forEach(row => {
        row.addEventListener("click", () => {
          const appointmentID = row.querySelector("td:first-child").textContent;
          alert("You clicked on Appointment ID: " + appointmentID);
        });
      });
    });
  </script>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <a href="#" class="nav-brand">Doctor Dashboard</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="manage_appointments.php">Appointments</a></li>
      <li><a href="add_apppointments.php">Add Appointments</a></li>
      <li><a href="../methods/logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">

    <h1>My Appointments</h1>

    <?php if ($result->num_rows > 0): ?>
      <!-- Table -->
      <table>
        <thead>
          <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($row['id']) ?></td>
              <td><?= htmlspecialchars($row['patient_name']) ?></td>
              <td><?= htmlspecialchars($row['date']) ?></td>
              <td><?= htmlspecialchars($row['time']) ?></td>
              <td><?= htmlspecialchars($row['status']) ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-appointments">No appointments found.</p>
    <?php endif; ?>
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2024 Hospital Management System. All Rights Reserved.</p>
  </footer>

</body>
</html>
