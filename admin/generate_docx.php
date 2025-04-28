<?php
// Include the Composer autoloader to load PhpOffice/PhpWord
require_once '../vendor/autoload.php';

include "../includes/connection.php";

// Create new PhpWord instance
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;

// Fetch timed-out sit-in records
$query_timedout = "SELECT s.idno, s.fname, s.lname, s.course, si.lab, si.sitin_purpose, si.time_in, si.time_out 
                   FROM sit_in_records si
                   JOIN studentinfo s ON si.idno = s.idno
                   WHERE si.time_out IS NOT NULL
                   ORDER BY time_out DESC";
$result_timedout = $conn->query($query_timedout);
$timedout_results = $result_timedout->fetch_all(MYSQLI_ASSOC);

// Create DOCX document
function generate_docx($timedout_results) {
    // Create new PhpWord instance
    $phpWord = new PhpWord();
    
    // Add a section
    $section = $phpWord->addSection();
    
    // Add a heading
    $section->addText(
        'Timed-Out Sit-In Records',
        ['bold' => true, 'size' => 16],
        ['alignment' => 'center']
    );
    
    // Add spacing
    $section->addTextBreak(1);
    
    // Create table
    $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000', 'width' => 100, 'unit' => 'pct']);
    
    // Add table header row
    $table->addRow();
    $table->addCell(1500)->addText('Student ID', ['bold' => true]);
    $table->addCell(2000)->addText('Name', ['bold' => true]);
    $table->addCell(1000)->addText('Course', ['bold' => true]);
    $table->addCell(1000)->addText('Laboratory', ['bold' => true]);
    $table->addCell(2000)->addText('Purpose', ['bold' => true]);
    $table->addCell(1500)->addText('Time In', ['bold' => true]);
    $table->addCell(1500)->addText('Time Out', ['bold' => true]);
    
    // Add data rows
    foreach ($timedout_results as $row) {
        $table->addRow();
        $table->addCell(1500)->addText($row['idno']);
        $table->addCell(2000)->addText($row['fname'] . ' ' . $row['lname']);
        $table->addCell(1000)->addText($row['course']);
        $table->addCell(1000)->addText($row['lab']);
        $table->addCell(2000)->addText($row['sitin_purpose']);
        $table->addCell(1500)->addText($row['time_in']);
        $table->addCell(1500)->addText($row['time_out']);
    }
    
    // Save file
    $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
    
    // Set headers for DOCX download
    header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
    header('Content-Disposition: attachment; filename="sit_in_records_timedout.docx"');
    header('Cache-Control: max-age=0');
    
    // Output to browser
    $objWriter->save('php://output');
}

// Call the DOCX generation function
generate_docx($timedout_results);
?> 