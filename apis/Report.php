<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

// Assuming db.php is your database connection file
require_once '../database/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extracting report details from the request body
    $reporter_id = $_POST['reporter_id']; // The ID of the user reporting
    $reported_id = $_POST['reported_id']; // The ID of the user/post/comment being reported
    $report_type = $_POST['report_type']; // Type of report (user, post, comment)
    $reason = $_POST['reason']; // Reason for the report
    $description = $_POST['description']; // Description of the issue

    // Prepare the SQL statement based on the report type
    $sql = "";
    switch ($report_type) {
        case 'user':
            $sql = "INSERT INTO reports (reporter_id, reported_user_id, report_type, reason, description) VALUES (?,?,?,?,?)";
            break;
        case 'post':
            $sql = "INSERT INTO reports (reporter_id, reported_post_id, report_type, reason, description) VALUES (?,?,?,?,?)";
            break;
        case 'comment':
            $sql = "INSERT INTO reports (reporter_id, reported_comment_id, report_type, reason, description) VALUES (?,?,?,?,?)";
            break;
        default:
            $response = array(
                'status' => 'error',
                'message' => 'Invalid report type'
            );
            echo json_encode($response);
            exit;
    }

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $reporter_id, $reported_id, $report_type, $reason, $description); // Bind parameters according to the SQL statement

    // Execute the statement
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Report submitted successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Failed to submit report'
        );
    }

    // Close the statement
    $stmt->close();

    // Return the response as JSON
    echo json_encode($response);
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );
    echo json_encode($response);
}
