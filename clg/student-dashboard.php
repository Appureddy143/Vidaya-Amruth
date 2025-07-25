<?php 
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: student-login.php");
    exit;
}

include('db-config.php');
$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT name, email, dob, address, usn, branch FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch subject-wise attendance
$subjectStmt = $conn->prepare("SELECT s.name AS subject_name, a.total_classes, a.attended_classes FROM attendance a JOIN subjects s ON a.subject_id = s.id WHERE a.student_id = ? AND s.branch = ?");
$subjectStmt->bind_param("is", $student_id, $student['branch']);
$subjectStmt->execute();
$attendanceResult = $subjectStmt->get_result();

$subjects = [];
$percentages = [];

while ($row = $attendanceResult->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
    $percentage = ($row['total_classes'] > 0) ? round(($row['attended_classes'] / $row['total_classes']) * 100) : 0;
    $percentages[] = $percentage;
}

// Fetch latest notifications from principal
$notificationStmt = $conn->prepare("SELECT message, created_at FROM notifications ORDER BY created_at DESC LIMIT 5");
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();
$notifications = $notificationResult->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
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
        .navbar a:hover {
            background-color: #0056b3;
        }
        .student-info {
            background-color: #003366;
            color: white;
            padding: 10px 20px;
            text-align: left;
            font-size: 16px;
        }
        .container {
            width: 60%;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
        }
        .btn {
            display: block;
            width: 60%;
            margin: 10px auto;
            padding: 10px;
            font-size: 18px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }
        .btn:hover {
            background: #0056b3;
        }
        .logout {
            background: red;
        }
        .logout:hover {
            background: darkred;
        }
        .description {
            margin-top: 10px;
            font-style: italic;
            color: #555;
        }
        .notifications {
            margin-top: 20px;
            padding: 10px;
            background: #fff3cd;
            border: -1px solid #ffeeba;
            border-radius: 14px;
            color:rgb(230, 74, 12);
        }
        .notifications h4 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="view-timetable.php">View Timetables</a>
    <a href="ia-results.php">IA Results</a>
    <a href="stlogout.php">Logout</a>
</div>

<div class="student-info">
    <strong>Name:</strong> <?= htmlspecialchars($student['name']) ?> &nbsp; | &nbsp;
    <strong>Branch:</strong> <?= htmlspecialchars($student['branch']) ?>
</div>

<div class="container">
    <h2>Welcome to Your Dashboard</h2>
<div class="notifications">
        <h4>✅ Attendance</h4>
        </div>

    <canvas id="attendanceGraph"></canvas>
    <p class="description">This graph shows your subject-wise attendance percentage based on classes attended.</p>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    const ctx = document.getElementById('attendanceGraph').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($subjects) ?>,
            datasets: [{
                label: 'Attendance (%)',
                data: <?= json_encode($percentages) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Attendance (%)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    </script>

    <div class="notifications">
        <h4>📢 Latest Notifications from Principal</h4>
        <ul>
            <?php foreach ($notifications as $note): ?>
                <li><strong><?= date("d-m-Y H:i", strtotime($note['created_at'])) ?>:</strong> <?= htmlspecialchars($note['message']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    </div>
</body>
</html>
