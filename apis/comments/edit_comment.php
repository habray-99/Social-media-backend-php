<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

require_once('../../database/db.php');

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $commentId = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : null;
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
    $newContent = isset($_POST['content']) ? trim($_POST['content']) : null;

    if (!$commentId || !$userId || !$newContent) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    try {
        $conn->begin_transaction();

        $sql = "UPDATE COMMENTS SET USER_ID=?, CONTENT=? WHERE ID=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $userId, $newContent, $commentId);

        $stmt->execute();

        $affectedRows = $stmt->affected_rows;

        if ($affectedRows > 0) {
            http_response_code(200); // OK
            echo json_encode(["status" => "success", "message" => "Comment updated successfully"]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["status" => "error", "message" => "Comment not found or already deleted"]);
        }
    } catch (\Exception $e) {
        $conn->rollback();
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database operation failed: " . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
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
