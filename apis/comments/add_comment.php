<?php
// Enable CORS headers for cross-origin requests
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Set the response content type to JSON
header('Content-Type: application/json');

// Include your database connection file
require_once('../../database/db.php');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input
    if (!isset($_POST['post_id'], $_POST['user_id'], $_POST['content'])) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    // Extract data from the POST request
    $postId = isset($_POST['post_id']) ? intval($_POST['post_id']) : null;
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $content = isset($_POST['content']) ? trim($_POST['content']) : null;

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Prepare the SQL statement
        $sql = "INSERT INTO COMMENTS (POST_ID, USER_ID, CONTENT) VALUES (?,?,?)";

        // Prepare and bind the statement
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $postId, $userId, $content);

        // Execute the statement
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // Get the ID of the newly inserted comment
        $lastId = $stmt->insert_id;

        if ($lastId > 0) {
            http_response_code(201); // Created
            echo json_encode(["status" => "success", "message" => "Comment added successfully", "commentId" => $lastId]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(["status" => "error", "message" => "Failed to add comment"]);
        }
    } catch (\Exception $e) {
        // Rollback in case of error
        $conn->rollback();
        http_response_code(500); // Internal Server Error
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close(); // Close the database connection
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
