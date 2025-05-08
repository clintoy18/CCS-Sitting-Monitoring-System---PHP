<?php
// Start session and include necessary files
session_start();
include "../includes/adminlayout.php";
include "../includes/auth.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $resource_title = $_POST['resource_title'];
    $description = $_POST['description'];
    $resource_link = $_POST['resource_link'];
    $image = null;

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
                        $image = $new_filename;
                    }
                }
            }
        }
    }

    // Insert resource
    $query = "INSERT INTO lab_resources (resource_title, description, image, resource_link) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $resource_title, $description, $image, $resource_link);

    if ($stmt->execute()) {
        echo "<script>
                alert('Resource added successfully!');
                window.location.href='lab_resources.php';
              </script>";
    } else {
        echo "<script>alert('Error adding resource!');</script>";
    }
}
?>

<div class="px-8 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-plus text-blue-600 mr-3"></i>Add New Resource
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
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label for="resource_link" class="block text-sm font-medium text-gray-700">Resource Link</label>
                    <input type="url" name="resource_link" id="resource_link" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="https://example.com">
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Resource Image</label>
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
                        Save Resource
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 