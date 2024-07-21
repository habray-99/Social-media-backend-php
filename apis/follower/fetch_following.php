<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once('../../database/db.php');

$accountId = isset($_GET['account_id']) ? intval($_GET['account_id']) : null;

if (!isset($_GET['account_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Account ID is required"]);
    exit;
}

try {
    $conn->begin_transaction();

    // Query to retrieve all following for a specific account
    $sql = "SELECT FollowingUserID FROM FOLLOWERS WHERE AccountID =?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $following = [];
        while ($row = $result->fetch_assoc()) {
            $following[] = $row['FollowingUserID'];
        }

        http_response_code(200); // OK
        echo json_encode(['status' => 'success', 'data' => $following]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'No following found for this account']);
    }
} catch (\Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
