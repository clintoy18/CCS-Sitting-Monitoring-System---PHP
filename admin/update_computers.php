<?php
include "../includes/connection.php";
include "../includes/adminauth.php";

// Start transaction to ensure all changes are made together
$conn->begin_transaction();

try {
    // First, delete all existing computers
    $conn->query("DELETE FROM computers");
    
    // Reset auto-increment
    $conn->query("ALTER TABLE computers AUTO_INCREMENT = 1");
    
    // Get all rooms
    $rooms_result = $conn->query("SELECT room_id, room_name FROM rooms");
    
    // Create 30 computers for each room with standardized naming
    while ($room = $rooms_result->fetch_assoc()) {
        $room_id = $room['room_id'];
        $room_name = $room['room_name'];
        
        echo "Adding computers to Room {$room_name} (ID: {$room_id})...<br>";
        
        // Add 30 computers per room
        for ($i = 1; $i <= 30; $i++) {
            $computer_name = "PC-{$i}";
            
            $stmt = $conn->prepare("INSERT INTO computers (room_id, computer_name, status) VALUES (?, ?, 'available')");
            $stmt->bind_param("is", $room_id, $computer_name);
            $stmt->execute();
            
            echo "Added {$computer_name}<br>";
        }
        
        echo "Completed adding computers to Room {$room_name}<br><br>";
    }
    
    // Commit transaction
    $conn->commit();
    
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #d4edda; color: #155724; border-radius: 5px;'>
            <strong>Success!</strong> All computers have been updated with standardized naming (PC-1 to PC-30).
            <br><br>
            <a href='admindashboard.php' style='background-color: #155724; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Return to Dashboard</a>
          </div>";
    
} catch (Exception $e) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    
    echo "<div style='margin-top: 20px; padding: 15px; background-color: #f8d7da; color: #721c24; border-radius: 5px;'>
            <strong>Error!</strong> Failed to update computers: {$e->getMessage()}
            <br><br>
            <a href='admindashboard.php' style='background-color: #721c24; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;'>Return to Dashboard</a>
          </div>";
}

// Close connection
$conn->close();
?> 