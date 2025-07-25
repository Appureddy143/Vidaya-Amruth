<?php
$servername = "localhost";
$username = "root"; // Change if different
$password = "";     // Your MySQL password
$dbname = "college_exam_portal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from form
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$course = $_POST['course'];
$message = $_POST['message'];

// Insert data into table
$sql = "INSERT INTO Enquiry (full_name, email, phone, course, message)
        VALUES ('$full_name', '$email', '$phone', '$course', '$message')";

if ($conn->query($sql) === TRUE) {
    echo "Enquiry submitted successfully.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
