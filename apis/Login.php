<?php
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: POST");
// header("Access-Control-Allow-Headers: Content-Type");
// header('Content-Type: application/json');
// require_once('../database/db.php');

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     $email = $_POST['email'];
//     $password = $_POST['password'];


//     // Prepare the SQL statement
//     $sql = "SELECT * FROM users WHERE email = '$email'";

//     // Execute the SQL statement
//     $result = $conn->query($sql);

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         $userPasswordHash = $row['password'];



//         // Verify the password
//         if (password_verify($password, $userPasswordHash)) {
//             $user_id = $row['id'];
//             $token = bin2hex(random_bytes(16)); // Generate a random token

//             // Insert the token into the api_tokens table
//             $insert_sql = "INSERT INTO api_tokens (user_id, token) VALUES ('$user_id', '$token')";
//             $conn->query($insert_sql);
//             $result2 = null;
//             if ($row['type'] == 'Customer') {
//                 $user_id = $row['id'];

//                 // Prepare the SQL statement
//                 $sql2 = "SELECT * FROM users WHERE id = '$user_id'";
//                 // Execute the SQL statement
//                 $result2 = $conn->query($sql2);

//                 $row2 = $result2->fetch_assoc();
//             }

//             $response = array(
//                 'status' => 'success',
//                 'message' => 'Login successful',
//                 'token' => $token,
//                 'user' => $row,
//             );
//         } else {
//             $response = array(
//                 'status' => 'error',
//                 'message' => 'Invalid credentials'
//             );
//         }
//     } else {
//         $response = array(
//             'status' => 'error',
//             'message' => 'Invalid credentials'
//         );
//     }

//     // Return the response as JSON
//     header('Content-Type: application/json');
//     echo json_encode($response);
// }
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Assuming db.php correctly initializes $conn
require_once('../database/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if required parameters are provided
    if (!isset($_POST['email'], $_POST['password'])) {
        // Missing required parameters, return an error
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required parameters: email and password.'
        ]);
        exit; // Exit the script to prevent further execution
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepared statement to avoid SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // "s" indicates the variable type is string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userPasswordHash = $row['password'];

        if (password_verify($password, $userPasswordHash)) {
            $user_id = $row['id'];
            $token = bin2hex(random_bytes(16));

            // Insert the token into the api_tokens table
            $stmt = $conn->prepare("INSERT INTO api_tokens (user_id, token) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $token);
            $stmt->execute();

            if ($row['type'] == 'Customer') {
                // No additional action needed here since we're already fetching the user details
            }

            $response = [
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token,
                'user' => $row,
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Invalid credentials'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Invalid credentials'
        ];
    }

    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else{
    $response = array(
        'status' => 'error',
        'message' => 'Invalid request method'
    );

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
