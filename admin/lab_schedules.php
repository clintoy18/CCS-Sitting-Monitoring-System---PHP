<?php
ob_start(); // Start output buffering
session_start();
include "../includes/adminlayout.php";
include "../includes/adminauth.php";
include "../includes/connection.php";

// Define business hours
define('OPENING_TIME', '07:30');
define('CLOSING_TIME', '22:00');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $room_id = $_POST['room_id'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
            $day_of_week = $_POST['day_of_week'];
            $is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
            $schedule_date = $is_recurring ? NULL : $_POST['schedule_date'];

            // Validate time against business hours
            if ($start_time < OPENING_TIME || $start_time > CLOSING_TIME) {
                $_SESSION['error'] = "Start time must be between " . date('h:i A', strtotime(OPENING_TIME)) . " and " . date('h:i A', strtotime(CLOSING_TIME));
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            }

            if ($end_time < OPENING_TIME || $end_time > CLOSING_TIME) {
                $_SESSION['error'] = "End time must be between " . date('h:i A', strtotime(OPENING_TIME)) . " and " . date('h:i A', strtotime(CLOSING_TIME));
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            }

            // Validate time
            if ($start_time >= $end_time) {
                $_SESSION['error'] = "End time must be after start time";
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            }

            // Check for overlapping schedules
            $check_overlap = "SELECT COUNT(*) as count FROM lab_schedules 
                             WHERE room_id = ? 
                             AND (
                                 (is_recurring = ? AND day_of_week = ?) OR
                                 (is_recurring = 0 AND schedule_date = ?)
                             )
                             AND (
                                 (start_time <= ? AND end_time > ?) OR
                                 (start_time < ? AND end_time >= ?) OR
                                 (start_time >= ? AND end_time <= ?)
                             )
                             AND status = 'active'";
            
            $stmt = $conn->prepare($check_overlap);
            $stmt->bind_param("iissssssss", 
                $room_id,
                $is_recurring,
                $day_of_week,
                $schedule_date,
                $start_time, $start_time,
                $end_time, $end_time,
                $start_time, $end_time
            );
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if ($row['count'] > 0) {
                $_SESSION['error'] = "This schedule overlaps with an existing schedule for this laboratory";
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            }

            // Insert new schedule
            $query = "INSERT INTO lab_schedules (room_id, day_of_week, schedule_date, start_time, end_time, is_recurring, status) 
                     VALUES (?, ?, ?, ?, ?, ?, 'active')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("issssi", 
                $room_id, 
                $day_of_week, 
                $schedule_date, 
                $start_time, 
                $end_time, 
                $is_recurring
            );
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Schedule added successfully";
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            } else {
                $_SESSION['error'] = "Failed to add schedule";
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            }
        } elseif ($_POST['action'] === 'delete') {
            $schedule_id = $_POST['schedule_id'];
            $query = "DELETE FROM lab_schedules WHERE schedule_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $schedule_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Schedule deleted successfully";
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            } else {
                $_SESSION['error'] = "Failed to delete schedule";
                header("Location: lab_schedules.php");
                ob_end_flush();
                exit;
            }
        }
    }
}

// Fetch existing schedules with room names
$query = "SELECT ls.*, r.room_name 
          FROM lab_schedules ls
          JOIN rooms r ON ls.room_id = r.room_id
          ORDER BY ls.is_recurring DESC, 
                   COALESCE(ls.schedule_date, '9999-12-31'),
                   FIELD(ls.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), 
                   ls.start_time";
$result = mysqli_query($conn, $query);

// Fetch available rooms for the dropdown
$rooms_query = "SELECT room_id, room_name FROM rooms ORDER BY room_name";
$rooms_result = mysqli_query($conn, $rooms_query);
?>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-calendar-alt mr-3 text-blue-600"></i>Lab Schedules
            </h1>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Add Schedule Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Add New Schedule</h2>
            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="add">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Laboratory</label>
                    <select name="room_id" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Laboratory</option>
                        <?php while ($room = mysqli_fetch_assoc($rooms_result)): ?>
                            <option value="<?= $room['room_id'] ?>">Laboratory <?= htmlspecialchars($room['room_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Day of Week</label>
                    <select name="day_of_week" required class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Day</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time" required 
                           min="<?= OPENING_TIME ?>" max="<?= CLOSING_TIME ?>"
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Available: <?= date('h:i A', strtotime(OPENING_TIME)) ?> - <?= date('h:i A', strtotime(CLOSING_TIME)) ?></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time" required 
                           min="<?= OPENING_TIME ?>" max="<?= CLOSING_TIME ?>"
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Available: <?= date('h:i A', strtotime(OPENING_TIME)) ?> - <?= date('h:i A', strtotime(CLOSING_TIME)) ?></p>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="is_recurring" id="is_recurring" checked
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Recurring Weekly Schedule</span>
                    </label>
                </div>

                <div class="md:col-span-2" id="specific_date_container" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Specific Date</label>
                    <input type="date" name="schedule_date" 
                           min="<?= date('Y-m-d') ?>"
                           class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="md:col-span-4">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>Add Schedule
                    </button>
                </div>
            </form>
        </div>

        <!-- Schedules Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Laboratory</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day/Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Laboratory <?= htmlspecialchars($row['room_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($row['is_recurring']): ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                Recurring
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                                One-time
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($row['is_recurring']): ?>
                                            <?= htmlspecialchars($row['day_of_week']) ?>
                                        <?php else: ?>
                                            <?= date('M d, Y', strtotime($row['schedule_date'])) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('h:i A', strtotime($row['start_time'])) ?> - 
                                        <?= date('h:i A', strtotime($row['end_time'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M d, Y h:i A', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <form action="" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="schedule_id" value="<?= $row['schedule_id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No schedules found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const isRecurringCheckbox = document.getElementById('is_recurring');
    const specificDateContainer = document.getElementById('specific_date_container');
    const specificDateInput = document.querySelector('input[name="schedule_date"]');
    
    // Set min and max time attributes
    startTimeInput.min = '<?= OPENING_TIME ?>';
    startTimeInput.max = '<?= CLOSING_TIME ?>';
    endTimeInput.min = '<?= OPENING_TIME ?>';
    endTimeInput.max = '<?= CLOSING_TIME ?>';

    // Handle recurring checkbox change
    isRecurringCheckbox.addEventListener('change', function() {
        specificDateContainer.style.display = this.checked ? 'none' : 'block';
        specificDateInput.required = !this.checked;
    });

    // Validate time inputs
    form.addEventListener('submit', function(e) {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (startTime >= endTime) {
            e.preventDefault();
            alert('End time must be after start time');
            return;
        }

        if (startTime < '<?= OPENING_TIME ?>' || startTime > '<?= CLOSING_TIME ?>') {
            e.preventDefault();
            alert('Start time must be between <?= date('h:i A', strtotime(OPENING_TIME)) ?> and <?= date('h:i A', strtotime(CLOSING_TIME)) ?>');
            return;
        }

        if (endTime < '<?= OPENING_TIME ?>' || endTime > '<?= CLOSING_TIME ?>') {
            e.preventDefault();
            alert('End time must be between <?= date('h:i A', strtotime(OPENING_TIME)) ?> and <?= date('h:i A', strtotime(CLOSING_TIME)) ?>');
            return;
        }

        if (!isRecurringCheckbox.checked && !specificDateInput.value) {
            e.preventDefault();
            alert('Please select a specific date for one-time schedule');
            return;
        }
    });

    // Update end time min when start time changes
    startTimeInput.addEventListener('change', function() {
        endTimeInput.min = this.value;
        if (endTimeInput.value && endTimeInput.value <= this.value) {
            endTimeInput.value = '';
        }
    });
});
</script> 