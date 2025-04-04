<?php
session_start();
include "connection.php";
include "layout.php"; 
?>

<!-- SITIN HISTORY -->
<h1 class="text-3xl font-semibold text-center text-gray-800 p-8 ">SIT-IN HISTORY</h1>
<div class="px-8 py-8 grid gap-2" data-hs-datatable='{
    "pageLength": 10,
    "pagingOptions": {
        "pageBtnClasses": "min-w-[40px] flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none"
    }
}'>
    <div class="overflow-x-auto min-h-[520px] bg-white rounded-lg shadow-lg p-4">
        <div class="min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full">
                    <thead class="border-b border-gray-200">
                        <tr>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Room Number
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Student/User ID
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Time/Session Start/End
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                            <th scope="col" class="py-1 group text-start font-normal focus:outline-none">
                                <div class="py-1 px-2.5 inline-flex items-center border border-transparent text-sm text-gray-500 rounded-md hover:border-gray-200">
                                    Activity/Status
                                    <svg class="size-3.5 ms-1 -me-0.5 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path class="hs-datatable-ordering-desc:text-blue-600" d="m7 15 5 5 5-5"></path>
                                        <path class="hs-datatable-ordering-asc:text-blue-600" d="m7 9 5-5 5 5"></path>
                                    </svg>
                                </div>
                            </th>
                          
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php
                        include 'connection.php'; // Include your database connection file
                        // Open the database connection

                        $sql = "SELECT * FROM reservations WHERE idno = '$userID' ";// Adjust the query as per your table structure
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td class='p-3 whitespace-nowrap text-sm font-medium text-gray-800'>" . $row["room_id"] . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-sm text-gray-800'>" . $row["idno"] . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-sm text-gray-800'>" . $row["start_time"] . " - " . $row["end_time"] . "</td>";
                             
                                $status = $row["status"];
                                $colorClass = ($status == "cancelled") ? "text-red-600" : (($status == "completed") ? "text-green-600" : "text-gray-800");
                              
                                echo "<td class='p-3 whitespace-nowrap text-sm  $colorClass'>" . $row["status"] . "</td>";
                                echo "<td class='p-3 whitespace-nowrap text-end text-sm font-medium text-center'>
                                    
                                    </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='p-3 whitespace-nowrap text-sm text-gray-800'>No records found</td></tr>";
                        }

                        mysqli_close($conn); // Close the database connection
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-1 mt-4 hidden" data-hs-datatable-paging="">
        <button type="button" class="p-2.5 min-w-[40px] inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" data-hs-datatable-ping-prev="">
            <span aria-hidden="true">«</span>
            <span class="sr-only">Previous</span>
        </button>
        <div class="flex items-center space-x-1 [&>.active]:bg-gray-100" data-hs-datatable-paging-pages=""></div>
        <button type="button" class="p-2.5 min-w-[40px] inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" data-hs-datatable-ping-next="">
            <span class="sr-only">Next</span>
            <span aria-hidden="true">»</span>
        </button>
    </div>
</div>

<script>
    window.addEventListener('load', () => {
        // Your other JavaScript code here

        const inputs = document.querySelectorAll('.dt-container thead input');

        inputs.forEach((input) => {
            input.addEventListener('keydown', function (evt) {
                if ((evt.metaKey || evt.ctrlKey) && evt.key === 'a') this.select();
            });
        });
    });
</script>