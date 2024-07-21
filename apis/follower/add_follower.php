<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once('../../database/db.php'); // Assuming this is where your database connection logic is located

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['follower_id'], $_POST['followed_id'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Both follower_id and followed_id are required"]);
        exit;
    }
    $followerId = $_POST['follower_id'];
    $followedId = $_POST['followed_id'];

    // Prepare the SQL statement
    $sql = "INSERT INTO USER_RELATIONSHIPS (FOLLOWER_ID, FOLLOWED_ID) VALUES (?,?)";

    // Prepare and bind the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $followerId, $followedId);

    // Execute the statement
    $stmt->execute();

    $affectedRows = $stmt->affected_rows;

    if ($affectedRows > 0) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Follower added successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to add follower"]);
    }

    $stmt->close();
    $conn->close();
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
