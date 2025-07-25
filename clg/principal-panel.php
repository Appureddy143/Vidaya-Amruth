<?php
session_start();
include('db-config.php'); // Include database connection

// Check if the user is logged in as Principal
if ($_SESSION['role'] !== 'principal') {
    header("Location: login.php");
    exit;
}

// Fetch all data for staff and HOD
$query = "SELECT * FROM users WHERE role IN ('staff', 'hod')";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Principal Panel</title>
    <link rel="stylesheet" href="styles.css">
    <style>       
    /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        h2 {
            color: #444;
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

        .navbar a:hover {
            background-color: #0056b3;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1em;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 0.75em;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        .pagination {
            margin: 1em 0;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 5px 10px;
            border: 1px solid #007bff;
            border-radius: 5px;
            color: #007bff;
        }

        .pagination a.active {
            background-color: #007bff;
            color: #fff;
        }

        .pagination a:hover {
            background-color: #0056b3;
            color: #fff;
        }

        /* Edit & Remove Buttons */
        .edit-btn, .remove-btn {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }

        .edit-btn {
            background-color: #28a745;
            color: white;
        }

        .remove-btn {
            background-color: #dc3545;
            color: white;
        }

        .remove-btn:hover {
            background-color: #c82333;
        }

        .edit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="push-notification.php">Notification</a>       
        <a href="manage-users.php">Manage Users</a>
        <a href="view-reports.php">View Reports</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h2>Welcome to Principal Panel</h2>
        <p>Manage all departments and staff.</p>

        <h3>Staff & HOD Members</h3>
        <div class="panel principal-panel">
            <table class="table">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php
                while ($user = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$user['first_name']} {$user['surname']}</td>
                        <td>{$user['email']}</td>
                        <td>{$user['role']}</td>
                        <td><a href='view-user.php?id={$user['id']}'>View</a></td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading">
        <div class="spinner"></div>
    </div>
</body>
</html>
