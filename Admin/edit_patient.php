<?php
include '../includes/con.php';
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: /login.php");
    exit();
}

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    
    // Fetch patient data
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();

    if (!$patient) {
        echo "Patient not found!";
        exit();
    }
} else {
    header("Location: manage_patients.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];

    // Update patient data
    $update_stmt = $conn->prepare("UPDATE patients SET name = ?, age = ?, gender = ?, contact = ? WHERE id = ?");
    $update_stmt->bind_param("sissi", $name, $age, $gender, $contact, $patient_id);

    if ($update_stmt->execute()) {
        header("Location: index.php?success=Patient updated successfully");
        exit();
    } else {
        echo "Failed to update patient.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>Edit Patient</title>
</head>
<body>
    <?php include '../includes/header.php';
           include '../includes/noti_header.php';
    ?>

    <div class="container">
        <h1>Edit Patient</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" value="<?= $patient['name'] ?>" required>
            </div>
            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" name="age" id="age" value="<?= $patient['age'] ?>" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select name="gender" id="gender" required>
                    <option value="Male" <?= $patient['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $patient['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= $patient['gender'] === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" name="contact" id="contact" value="<?= $patient['contact'] ?>" required>
            </div>
            <button type="submit" class="btn btn-edit">Update</button>
            <a href="manage_patients.php" class="btn btn-back">Cancel</a>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>

