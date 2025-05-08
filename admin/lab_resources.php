<?php
// Start session and include necessary files
session_start();
include "../includes/adminlayout.php";
include "../includes/auth.php";

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit();
}

// Handle resource deletion
if (isset($_GET['delete'])) {
    $resource_id = $_GET['delete'];
    
    // Get image filename before deleting
    $get_image = "SELECT image FROM lab_resources WHERE resource_id = ?";
    $stmt = $conn->prepare($get_image);
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resource = $result->fetch_assoc();
    
    // Delete the resource
    $delete_query = "DELETE FROM lab_resources WHERE resource_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $resource_id);
    
    if ($stmt->execute()) {
        // Delete the image file if it exists
        if ($resource['image'] && file_exists("../assets/uploads/" . $resource['image'])) {
            unlink("../assets/uploads/" . $resource['image']);
        }
        echo "<script>alert('Resource deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting resource!');</script>";
    }
}

// Fetch all resources
$query = "SELECT * FROM lab_resources ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<div class="px-8 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-book text-blue-600 mr-3"></i>Lab Resources Management
        </h1>
        <a href="add_resource.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-plus mr-2"></i>
            Add New Resource
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 transition-all duration-300 hover:shadow-xl">
        <!-- Resources Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($resource = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <?php if ($resource['image']): ?>
                <img src="../assets/uploads/<?php echo $resource['image']; ?>" alt="<?php echo $resource['resource_title']; ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo $resource['resource_title']; ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo substr($resource['description'], 0, 100) . '...'; ?></p>
                    
                    <div class="flex justify-between items-center">
                        <a href="<?php echo $resource['resource_link']; ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            View Resource
                        </a>
                        
                        <div class="flex space-x-2">
                            <a href="edit_resource.php?id=<?php echo $resource['resource_id']; ?>" class="text-yellow-600 hover:text-yellow-800">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $resource['resource_id']; ?>" onclick="return confirm('Are you sure you want to delete this resource?')" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if ($result->num_rows === 0): ?>
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                <i class="fas fa-book text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Resources Available</h3>
            <p class="text-gray-600">Add new resources to help students with their lab sessions.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover\:shadow-lg:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .transition-shadow {
        transition-property: box-shadow;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 300ms;
    }
</style> 