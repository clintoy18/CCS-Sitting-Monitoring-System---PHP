<?php
// Start output buffering

// Include the Composer autoloader to load TCPDF
require_once '../vendor/autoload.php';

include "../includes/connection.php"; 

// Fetch timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.sitin_purpose, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL
                   ORDER BY time_out DESC";
$result_timedout = $conn->query($query_timedout);
$timedout_results = $result_timedout->fetch_all(MYSQLI_ASSOC);

// Create PDF
function generate_pdf($timedout_results) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('CCS SIT-IN Monitoring System');
    $pdf->SetTitle('Timed-Out Sit-In Records');
    
    // Set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'CCS SIT-IN Monitoring System', 'Timed-Out Sit-In Records');
    
    // Set header and footer fonts
    $pdf->setHeaderFont(Array('helvetica', '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array('helvetica', '', PDF_FONT_SIZE_DATA));
    
    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Title
    $pdf->Cell(0, 10, 'Timed-Out Sit-In Records', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Date and Time
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y h:i A'), 0, 1, 'R');
    $pdf->Ln(5);
    
    // Table Header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(66, 139, 202); // Blue header background
    $pdf->SetTextColor(255); // White text
    
    // Column widths
    $w = array(25, 40, 20, 20, 35, 25, 25);
    
    // Header
    $header = array('Student ID', 'Name', 'Course', 'Lab', 'Purpose', 'Time In', 'Time Out');
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();
    
    // Reset text color and font for data
    $pdf->SetTextColor(0);
    $pdf->SetFont('helvetica', '', 9);
    
    // Table data
    $fill = false;
    foreach($timedout_results as $row) {
        // Alternate row colors
        $pdf->SetFillColor(245, 245, 245);
        $fill = !$fill;
        
        // Format the data
        $name = $row['fname'] . ' ' . $row['lname'];
        $time_in = date('h:i A', strtotime($row['time_in']));
        $time_out = date('h:i A', strtotime($row['time_out']));
        
        // Data cells
        $pdf->Cell($w[0], 6, $row['idno'], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[1], 6, $name, 'LR', 0, 'L', $fill);
        $pdf->Cell($w[2], 6, $row['course'], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[3], 6, $row['lab'], 'LR', 0, 'C', $fill);
        $pdf->Cell($w[4], 6, $row['sitin_purpose'], 'LR', 0, 'L', $fill);
        $pdf->Cell($w[5], 6, $time_in, 'LR', 0, 'C', $fill);
        $pdf->Cell($w[6], 6, $time_out, 'LR', 0, 'C', $fill);
        $pdf->Ln();
    }
    
    // Closing line
    $pdf->Cell(array_sum($w), 0, '', 'T');
    
    // Add summary
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(0, 6, 'Summary:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Total Records: ' . count($timedout_results), 0, 1, 'L');
    
    // Output the PDF
    $pdf->Output('sit_in_records_timedout.pdf', 'D');
}

// Call the PDF generation function
generate_pdf($timedout_results);

// End output buffering to prevent extra output
?>
