<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once('../../database/db.php'); // Assuming this is where your database connection logic is located

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    
    if (!isset($_GET["followerId"], $_GET["followedId"])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Both follower_id and followed_id are required"]);
        exit;
    }
    $followedId = $_GET['followed_id'] ?? null;
    $followerId = $_GET['follower_id'] ?? null;

    // Prepare the SQL statement
    $sql = "DELETE FROM USER_RELATIONSHIPS WHERE FOLLOWER_ID =? AND FOLLOWED_ID =?";

    // Prepare and bind the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $followerId, $followedId);

    // Execute the statement
    $stmt->execute();

    $affectedRows = $stmt->affected_rows;

    if ($affectedRows > 0) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Follower removed successfully"]);
    } else {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "No follower found to remove"]);
    }

    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
