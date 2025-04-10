<?php
include "connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $feedback = $conn->real_escape_string($_POST['feedback']);

    $query = "INSERT INTO feedbacks (student_id, feedback) VALUES ('$student_id', '$feedback')";
    if ($conn->query($query)) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href='history.php';</script>";
    } else {
        echo "<script>alert('Error submitting feedback. Please try again.'); window.location.href='history.php';</script>";
    }
}
?>