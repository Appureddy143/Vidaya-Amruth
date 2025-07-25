<?php
// Include your DB connection
include 'db-config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        h2 {
            text-align: center;
        }
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 90%;
            background-color: #fff;
        }
        th, td {
            padding: 10px 15px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #333;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            color: #0056b3;
        }
        .btn-add {
            display: block;
            width: fit-content;
            margin: 20px auto;
            padding: 10px 15px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-add:hover {
            background-color: #218838;
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
    </style>
</head>
<body>
    <div class="navbar">
        <a href="admin-panel.php">Back To Admin Panel</a>
    </div>
<h2>Subjects and Allocated Teachers</h2>

<a class="btn-add" href="add-subject.php">+ Add New Subject</a>

<?php
$query = "
    SELECT 
        s.id, 
        s.name AS subject_name, 
        s.branch, 
        sa.staff_id, 
        CONCAT(u.first_name, ' ', u.surname) AS teacher_name 
    FROM 
        subjects s 
    LEFT JOIN 
        subject_allocation sa ON s.id = sa.subject_id 
    LEFT JOIN 
        users u ON sa.staff_id = u.id
";

$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "<table>
        <tr>
            <th>ID</th>
            <th>Subject Name</th>
            <th>Branch</th>
            <th>Allocated Teacher</th>
            <th>Actions</th>
        </tr>";
    
    while ($row = $result->fetch_assoc()) {
        $teacher = $row['teacher_name'] ?? ''; // Show blank if not allocated

        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['subject_name']}</td>
            <td>{$row['branch']}</td>
            <td>{$teacher}</td>
            <td>
                <a href='edit-subject.php?id={$row['id']}'>Edit</a> |
                <a href='delete-subject.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this subject?');\">Delete</a>
            </td>
        </tr>";
    }

    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No subjects found.</p>";
}

$conn->close();
?>

</body>
</html>
