<?php
// Start session and include necessary files
session_start();
include "../includes/layout.php";
include "../includes/auth.php";

// Check if student is logged in
if (!isset($_SESSION["idno"])) {
    header("Location: login.php");
    exit();
}

// Fetch all resources
$query = "SELECT * FROM lab_resources ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<div class="px-8 py-6">
    <h1 class="text-3xl font-bold text-center text-gray-800 mb-8 flex items-center justify-center">
        <i class="fas fa-book text-blue-600 mr-3"></i>Laboratory Resources
    </h1>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 mb-8 transition-all duration-300 hover:shadow-xl">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-3 flex items-center">
                <i class="fas fa-graduation-cap text-blue-600 mr-2"></i>Learning Materials
            </h2>
            <p class="text-gray-600">Access learning materials and helpful resources for your lab sessions.</p>
        </div>

        <!-- Resources Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($resource = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-200">
                <?php if ($resource['image']): ?>
                <img src="../assets/uploads/<?php echo $resource['image']; ?>" alt="<?php echo $resource['resource_title']; ?>" class="w-full h-48 object-cover">
                <?php else: ?>
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                </div>
                <?php endif; ?>
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2"><?php echo $resource['resource_title']; ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo $resource['description']; ?></p>
                    
                    <a href="<?php echo $resource['resource_link']; ?>" target="_blank" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Access Resource
                    </a>
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
            <p class="text-gray-600">Check back later for new learning materials.</p>
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