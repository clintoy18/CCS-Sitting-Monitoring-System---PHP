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

// Get resource details
if (isset($_GET['id'])) {
    $resource_id = $_GET['id'];
    $query = "SELECT * FROM lab_resources WHERE resource_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resource = $result->fetch_assoc();

    if (!$resource) {
        header("Location: lab_resources.php");
        exit();
    }
} else {
    header("Location: lab_resources.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resource_title = $_POST['resource_title'];
    $description = $_POST['description'];
    $resource_link = $_POST['resource_link'];
    $image = $resource['image']; // Keep existing image by default

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/uploads/";
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;

        // Check if image file is a actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (5MB max)
            if ($_FILES["image"]["size"] <= 5000000) {
                // Allow certain file formats
                if ($file_extension == "jpg" || $file_extension == "png" || $file_extension == "jpeg") {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        // Delete old image if exists
                        if ($resource['image'] && file_exists("../assets/uploads/" . $resource['image'])) {
                            unlink("../assets/uploads/" . $resource['image']);
                        }
                        $image = $new_filename;
                    }
                }
            }
        }
    }

    // Update resource
    $query = "UPDATE lab_resources SET resource_title = ?, description = ?, image = ?, resource_link = ? WHERE resource_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $resource_title, $description, $image, $resource_link, $resource_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Resource updated successfully!');
                window.location.href='lab_resources.php';
              </script>";
    } else {
        echo "<script>alert('Error updating resource!');</script>";
    }
}
?>

<div class="px-8 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-edit text-blue-600 mr-3"></i>Edit Resource
                </h1>
                <a href="lab_resources.php" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Resources
                </a>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="resource_title" class="block text-sm font-medium text-gray-700">Resource Title</label>
                    <input type="text" name="resource_title" id="resource_title" required
                        value="<?php echo $resource['resource_title']; ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $resource['description']; ?></textarea>
                </div>

                <div>
                    <label for="resource_link" class="block text-sm font-medium text-gray-700">Resource Link</label>
                    <input type="url" name="resource_link" id="resource_link" required
                        value="<?php echo $resource['resource_link']; ?>"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="https://example.com">
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Resource Image</label>
                    <?php if ($resource['image']): ?>
                    <div class="mt-2 mb-4">
                        <img src="../assets/uploads/<?php echo $resource['image']; ?>" alt="Current Image" class="h-32 w-auto rounded-lg">
                    </div>
                    <?php endif; ?>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100">
                    <p class="mt-1 text-sm text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Update Resource
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 