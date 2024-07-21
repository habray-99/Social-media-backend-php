<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once('../../database/db.php');


if (!isSet($_GET["post_id"])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Post ID is required"]);
    exit;
}
$postId = isset($_GET['post_id']);

try {
    $conn->begin_transaction();

    // Joining POSTS, COMMENTS, and USERS tables to get all columns
    $sql = "SELECT P.*, C.*, U.*
            FROM POSTS P
            JOIN COMMENTS C ON P.ID = C.POST_ID
            LEFT JOIN USERS U ON C.USER_ID = U.ID
            WHERE P.ID =?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $postId);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $comments = [];
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }

        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'data' => $comments]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'No comments found for this post']);
    }
} catch (\Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
