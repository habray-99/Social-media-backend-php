<?php
require_once('../database/db.php');
header('Content-Type: application/json');
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['fullName'], $_POST['userName'], $_POST['phone'], $_POST['email'], $_POST['password'])) {
        $response = array(
            'status' => 'error',
            'message' => 'Incomplete credentials'
        );
        echo json_encode($response);
        exit();
    }

    // Retrieve the user details from the form
    $fullName = $_POST['fullName'];
    $userName = $_POST['userName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Encrypt the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (fullName, userName, phone, email, password) VALUES ('$fullName','$userName','$phone', '$email', '$hashedPassword')";
    $conn->query($sql);

    $user_id = $conn->insert_id; // Get the ID of the newly inserted user

    // Generate a token
    $token = bin2hex(random_bytes(16));

    // Store the token in the api_tokens table
    $tokenSql = "INSERT INTO api_tokens (user_id, token) VALUES ($user_id, '$token')";
    $conn->query($tokenSql);

    $sql2 = "SELECT * FROM users WHERE id = '$user_id'";
    // Execute the SQL statement
    $result2 = $conn->query($sql2);

    $row2 = $result2->fetch_assoc();

    $response = array(
        'status' => 'success',
        'message' => 'Login successful',
        'token' => $token,
        'user' => $row2,
    );
    echo json_encode($response);

    // Redirect to the dashboard page
    // header("Location: users.php");
    exit();
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
