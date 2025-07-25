<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit;
}

include('db-config.php');
$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT subject, marks FROM ia_results WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IA Results</title>
    <link rel="stylesheet" href="styles.css">
<style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 80%;
            max-width: 1000px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
            text-align: center;
        }
        p {
            color: #555;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 25px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 0 1px #ddd;
        }
        th {
            background: #3498db;
            color: white;
            padding: 15px;
            text-align: left;
        }
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        tr:hover {
            background-color: #f7f7f7;
        }
        tr:last-child td {
            border-bottom: none;
        }
        a {
            display: inline-block;
            padding: 12px 20px;
            margin-right: 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        a {
            display: inline-block;
            padding: 8px 15px;
            margin-right: 10px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        a[href="student-dashboard.php"] {
            background: #e74c3c;
            color: white;
        }
        a[href="student-dashboard.php"]:hover {
            background: #c0392b;
        }
        .print-button {
            background: #2ecc71;
            color: white;
        }
        .print-button:hover {
            background: #27ae60;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            a {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Results</h2>
        <p>Your marks for the subjects are listed below:</p> <!-- Added explanatory text -->
        <table>
            <tr>
                <th>Subject</th>
                <th>Marks</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['marks']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="student-dashboard.php">Back to Dashboard</a>
        <a href="javascript:window.print();" class="print-button">Print Results</a> <!-- Print button -->
    </div>
</body>
</html>
