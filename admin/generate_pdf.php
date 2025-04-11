<?php
// Start output buffering

// Include the Composer autoloader to load TCPDF
require_once '../vendor/autoload.php';

include "../includes/connection.php"; 

// Fetch timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL
                   ORDER BY time_out DESC";
$result_timedout = $conn->query($query_timedout);
$timedout_results = $result_timedout->fetch_all(MYSQLI_ASSOC);

// Create PDF
function generate_pdf($timedout_results) {
    // Create new PDF document
    $pdf = new TCPDF();
    $pdf->AddPage();
    
    // Set PDF title
    $pdf->SetTitle('Timed-Out Sit-In Records');

    // Add Timed-Out Sit-In Records Section
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Timed-Out Sit-In Records', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Ln(5);

    // Table Header with optimized column widths
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(200, 220, 255); // Light blue background for header

    // Adjusted column widths to ensure they fit within page width (190mm)
    $pdf->Cell(25, 8, 'Student ID', 1, 0, 'C', 1);  
    $pdf->Cell(40, 8, 'Name', 1, 0, 'C', 1);           // Reduced width for Name column
    $pdf->Cell(20, 8, 'Course', 1, 0, 'C', 1);         // Reduced width for Course column
    $pdf->Cell(20, 8, 'Laboratory', 1, 0, 'C', 1);     // Adjusted width for Lab column
    $pdf->Cell(40, 8, 'Time In', 1, 0, 'C', 1);        // Increased width for Time In
    $pdf->Cell(40, 8, 'Time Out', 1, 1, 'C', 1);       // Increased width for Time Out

    // Reset font for table data
    $pdf->SetFont('helvetica', '', 10);

    // Timed Out Students Data with improved alignment
    foreach ($timedout_results as $row) {
        // Cell height increased to 10 for better spacing
        $pdf->Cell(25, 10, $row['idno'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['fname'] . ' ' . $row['lname'], 1, 0, 'L'); // Left align name
        $pdf->Cell(20, 10, $row['course'], 1, 0, 'C');
        $pdf->Cell(20, 10, $row['lab'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['time_in'], 1, 0, 'C'); // Increased width for better fit
        $pdf->Cell(40, 10, $row['time_out'], 1, 1, 'C'); // Increased width for better fit
    }

    // Output the PDF to the browser
    $pdf->Output('sit_in_records_timedout.pdf', 'D');
}

// Call the PDF generation function
generate_pdf($timedout_results);

// End output buffering to prevent extra output
?>
