<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once('../../database/db.php');


if (!isset($_GET['account_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Account ID is required"]);
    exit;
}
$accountId = isset($_GET['account_id']) ? intval($_GET['account_id']) : null;

try {
    $conn->begin_transaction();

    // Query to retrieve all followers for a specific account
    $sql = "SELECT FollowerUserID FROM FOLLOWERS WHERE AccountID =?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $followers = [];
        while ($row = $result->fetch_assoc()) {
            $followers[] = $row['FollowerUserID'];
        }

        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'data' => $followers]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'No followers found for this account']);
    }
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
