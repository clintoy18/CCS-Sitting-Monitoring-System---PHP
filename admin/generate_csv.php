<?php
include '../database.php';

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to get timed-out sit-in records
$sql = "SELECT s.student_number, s.first_name, s.last_name, s.email, s.college, s.program, s.year_level, sts.computer_number, sts.time_in, sts.time_out 
        FROM students s
        INNER JOIN student_time_sessions sts ON s.student_number = sts.student_number
        WHERE sts.time_out IS NOT NULL
        ORDER BY sts.time_out DESC";

$result = mysqli_query($conn, $sql);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="timed_out_students_report.csv"');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Add CSV header row
fputcsv($output, array('Student Number', 'First Name', 'Last Name', 'Email', 'College', 'Program', 'Year Level', 'Computer Number', 'Time In', 'Time Out'));

// Add data rows
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        fputcsv($output, array(
            $row['student_number'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['college'],
            $row['program'],
            $row['year_level'],
            $row['computer_number'],
            $row['time_in'],
            $row['time_out']
        ));
    }
}

// Close the file pointer
fclose($output);

// Close the database connection
mysqli_close($conn);
?> 