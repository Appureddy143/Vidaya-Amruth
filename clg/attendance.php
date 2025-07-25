<?php
session_start();
include 'db-config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

$staff_id = $_SESSION['user_id'];

// Fetch allocated subjects for this staff
$subject_sql = $conn->prepare("SELECT s.id, s.name FROM subject_allocation sa 
    JOIN subjects s ON sa.subject_id = s.id WHERE sa.staff_id = ?");
$subject_sql->bind_param("i", $staff_id);
$subject_sql->execute();
$subjects = $subject_sql->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];
    $attendance_date = date('Y-m-d');

    foreach ($_POST['attendance'] as $student_id => $status) {
        // Check if already marked
        $check = $conn->prepare("SELECT id, marked_at FROM attendance WHERE student_id=? AND subject_id=? AND date=?");
        $check->bind_param("iis", $student_id, $subject_id, $attendance_date);
        $check->execute();
        $check_res = $check->get_result();

        if ($check_res->num_rows === 0) {
            // Insert new attendance
            $insert = $conn->prepare("INSERT INTO attendance (student_id, subject_id, date, status, marked_by) 
                                      VALUES (?, ?, ?, ?, ?)");
            $insert->bind_param("iissi", $student_id, $subject_id, $attendance_date, $status, $staff_id);
            $insert->execute();
        }
    }

    echo "<p>✅ Attendance submitted successfully for today!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Attendance</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        select, table, button { margin-top: 10px; }
        table { width: 100%; background: #fff; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #222; color: #fff; }
        input[type=radio] { transform: scale(1.2); }
    </style>
</head>
<body>
<h2>Take Attendance</h2>
<form method="POST">
    <label>Select Subject:</label>
    <select name="subject_id" required onchange="this.form.submit()">
        <option value="">-- Select --</option>
        <?php while ($row = $subjects->fetch_assoc()) { ?>
            <option value="<?= $row['id'] ?>" <?= isset($_POST['subject_id']) && $_POST['subject_id'] == $row['id'] ? 'selected' : '' ?>>
                <?= $row['name'] ?>
            </option>
        <?php } ?>
    </select>
</form>

<?php
// Show students for selected subject
if (isset($_POST['subject_id'])) {
    $subject_id = $_POST['subject_id'];

    // Fetch students (based on subject's branch)
    $branch_sql = $conn->prepare("SELECT branch FROM subjects WHERE id = ?");
    $branch_sql->bind_param("i", $subject_id);
    $branch_sql->execute();
    $branch_result = $branch_sql->get_result()->fetch_assoc();
    $branch = $branch_result['branch'];

    $students_sql = $conn->prepare("SELECT * FROM students WHERE email LIKE ?");
    $email_filter = '%' . strtolower($branch) . '%'; // Match email containing branch
    $students_sql->bind_param("s", $email_filter);
    $students_sql->execute();
    $students = $students_sql->get_result();

    if ($students->num_rows > 0) {
        echo "<form method='POST'>";
        echo "<input type='hidden' name='subject_id' value='$subject_id'>";
        echo "<table><tr><th>Name</th><th>Status</th></tr>";
        while ($student = $students->fetch_assoc()) {
            echo "<tr>
                    <td>{$student['name']}</td>
                    <td>
                        <label><input type='radio' name='attendance[{$student['id']}]' value='present' checked> Present</label>
                        <label><input type='radio' name='attendance[{$student['id']}]' value='absent'> Absent</label>
                    </td>
                </tr>";
        }
        echo "</table><button type='submit'>Submit Attendance</button></form>";
    } else {
        echo "<p>No students found for branch: $branch</p>";
    }
}
?>
</body>
</html>
