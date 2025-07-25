<?php
session_start();
include('db-config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'principal') {
    header("Location: principal-login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO notifications (message, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $message);
        if ($stmt->execute()) {
            $success = "Notification sent!";
        } else {
            $error = "Error sending notification.";
        }
    } else {
        $error = "Message cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Push Notification - Principal</title>
    <style>
              body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        
        .navbar {
            background-color: #007bff;
            padding: 1em;
            display: flex;
            justify-content: flex-end;
            gap: 1em;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            padding: 0.5em 1em;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .content {
            width: 95%;
            max-width: 1250px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            font-size: 13px;
        }

        th {
            background-color: #e0e0e0;
        }

        select, input[type="text"], input[type="time"] {
            width: 100%;
            padding: 4px;
            margin-top: 4px;
        }

        .custom-fields {
            display: none;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            margin: 15px 10px 0 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        label {
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .merge-label {
            margin-top: 6px;
        }
        .container { background:rgba(255, 255, 255, 0.7); padding: 70px; max-width: 500px; margin: auto; border-radius: 8px; }
        textarea { width: 100%; height: 100px; margin-bottom: 10px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .message { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
        <div class="navbar">
        <a href="principal-panel.php">Back</a>       
    </div>
<div class="container">
    <h2>Send Notification to Students</h2>
    <?php if (isset($success)) echo "<p class='message'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <textarea name="message" placeholder="Enter your notification here..." required></textarea>
        <button type="submit">Send</button>
    </form>
</div>
</body>
</html>
