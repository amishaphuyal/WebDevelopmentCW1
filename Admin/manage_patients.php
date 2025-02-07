<?php
include '../includes/con.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$query = "SELECT * FROM users WHERE role = 'patient'";
$result = $conn->query($query);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_patient'])) {
    $patient_id = $_POST['patient_id'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    
    $update_stmt = $conn->prepare("UPDATE users SET name = ?, contact = ? WHERE id = ?");
    $update_stmt->bind_param("ssi", $name, $contact, $patient_id);
    
    if ($update_stmt->execute()) {
        header("Location: manage_patients.php?success=Patient updated successfully");
        exit();
    } else {
        echo "Failed to update patient.";
    }
}
include '../includes/admin_header.php'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Patients</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        button {
            padding: 8px 15px;
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover {
            background: #218838;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.5s;
        }
        .modal-content {
            background: white;
            width: 50%;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: slideDown 0.5s;
        }
        .close {
            float: right;
            font-size: 20px;
            cursor: pointer;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    <script>
        function populateModal(id, name, contact) {
            document.getElementById('patient_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('contact').value = contact;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Manage Patients</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['name'] ?></td>
                    <td><?= $row['contact'] ?></td>
                    <td>
                        <button onclick="populateModal('<?= $row['id'] ?>', '<?= $row['name'] ?>', '<?= $row['contact'] ?>')">Edit</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Patient</h2>
            <form method="POST" action="">
                <input type="hidden" name="patient_id" id="patient_id">
                <label>Name:</label>
                <input type="text" name="name" id="name" required>
                <label>Contact:</label>
                <input type="text" name="contact" id="contact" required>
                <button type="submit" name="update_patient">Update</button>
            </form>
        </div>
    </div>
</body>
</html>
