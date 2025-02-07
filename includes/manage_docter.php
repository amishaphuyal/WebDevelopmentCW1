<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit;
}

include '../includes/db.php';

// Fetch all doctors
$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    $sql = "INSERT INTO doctors (name, specialization, contact, email) VALUES ('$name', '$specialization', '$contact', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "Doctor added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Doctors</title>
</head>
<body>
<h1>Manage Doctors</h1>
<form method="POST">
    <input type="text" name="name" placeholder="Doctor Name" required>
    <input type="text" name="specialization" placeholder="Specialization" required>
    <input type="text" name="contact" placeholder="Contact" required>
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit">Add Doctor</button>
</form>

<h2>Doctor List</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Specialization</th>
        <th>Contact</th>
        <th>Email</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['name'] ?></td>
            <td><?= $row['specialization'] ?></td>
            <td><?= $row['contact'] ?></td>
            <td><?= $row['email'] ?></td>
        </tr>
    <?php } ?>
</table>
</body>
</html>

