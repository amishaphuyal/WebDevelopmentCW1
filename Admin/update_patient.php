<?php
include '../includes/con.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_patient_id'])) {
    $edit_patient_id = $_POST['edit_patient_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact'];

    $update_query = "UPDATE users SET name = ?, email = ?, age = ?, gender = ?, contact = ? WHERE id = ? AND role = 'patient'";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssissi", $name, $email, $age, $gender, $contact, $edit_patient_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Patient updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating patient']);
    }
    exit();
}
?>
