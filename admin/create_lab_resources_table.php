<?php
include '../includes/connection.php';

// Create lab_resources table
$sql = "CREATE TABLE IF NOT EXISTS lab_resources (
    resource_id INT PRIMARY KEY AUTO_INCREMENT,
    resource_title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    resource_link VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table lab_resources created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 