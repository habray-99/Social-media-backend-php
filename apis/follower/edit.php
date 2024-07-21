<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Assuming db.php is your database connection file
require_once('../../database/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['id'], $_POST['fullName'], $_POST['userName'], $_POST['email'], $_POST['password'])) {
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters.'
        ]);
        exit;
    }

    // Extract user details from the request body
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $fullName = isset($_POST['fullName']) ? $_POST['fullName'] : null;
    $userName = isset($_POST['userName']) ? $_POST['userName'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $type = isset($_POST['type']) ? $_POST['type'] : null;
    $image = isset($_POST['image']) ? $_POST['image'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $bio = isset($_POST['bio']) ? $_POST['bio'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;
    $website = isset($_POST['website']) ? $_POST['website'] : null;
    $isActive = isset($_POST['isActive']) ? filter_var($_POST['isActive'], FILTER_VALIDATE_BOOLEAN) : false;
    $isPremium = isset($_POST['isPremium']) ? filter_var($_POST['isPremium'], FILTER_VALIDATE_BOOLEAN) : false;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
    $premiumExpiry = isset($_POST['premiumExpiry']) ? $_POST['premiumExpiry'] : null; // Assuming this is a date string

    // Hash the new password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Directly insert values into the SQL query (Not recommended)
    $sql = "UPDATE USERS SET FULLNAME='$fullName', USERNAME='$userName', EMAIL='$email', TYPE='$type', IMAGE='$image', PASSWORD='$hashedPassword', BIO='$bio', LOCATION='$location', WEBSITE='$website', IS_ACTIVE=$isActive, IS_PREMIUM=$isPremium, PHONE='$phone', PREMIUM_EXPIRY='$premiumExpiry' WHERE ID=$id";

    // Execute the statement
    if ($conn->execute_query($sql)) {
        // Fetch the updated user details
        $sqlFetch = "SELECT * FROM USERS WHERE ID=$id";
        $result = $conn->query($sqlFetch);

        if ($result && $row = $result->fetch_assoc()) {
            // Prepare the response
            $response = [
                'status' => 'success',
                'message' => 'User details updated successfully',
                'userDetails' => $row
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Failed to retrieve updated user details'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Failed to update user details'
        ];
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // If the request method is not POST, return an error message
    $response = [
        'status' => 'error',
        'message' => 'Invalid request method'
    ];

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
