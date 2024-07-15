<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Assuming db.php is your database connection file
require_once('../database/db.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $searchTitle = isset($_GET['searchTitle']) ? $_GET['searchTitle'] : ''; // The title to search for

    // Prepare the SQL statement to search for posts by title
    $sql = "SELECT * FROM posts WHERE title LIKE '%$searchTitle%'";

    // Execute the SQL statement
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Initialize an empty array to hold our results
        $posts = [];

        // Fetch all matching rows into the $posts array
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row; // Add each row to the $posts array
        }

        header('Content-Type: application/json');
        // Prepare the response
        $response = array(
            'status' => 'success',
            'message' => 'Posts found successfully',
            'posts' => $posts, // Include the found posts in the response
        );
    } else {
        // No posts found
        header('Content-Type: application/json');
        $response = array(
            'status' => 'error',
            'message' => 'No posts found with that title'
        );
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If the request method is not GET, return an error message
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
