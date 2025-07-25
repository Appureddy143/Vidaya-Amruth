<?php
session_start();
include("db-config.php");

// Fetch all subjects
$subjectsResult = $conn->query("SELECT id, name FROM subjects");

// Fetch all staff
$staffResult = $conn->query("SELECT id, first_name, surname FROM users WHERE role='staff'");

// Days and number of periods per day
$days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
$periodsPerDay = 6;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule = $_POST['schedule'];

    foreach ($schedule as $day => $periods) {
        foreach ($periods as $periodNumber => $details) {
            $subjectId = $details['subject'];
            $staffId = $details['staff'];
            $time = $details['time'];
            $merge = isset($details['merge']) ? 1 : 0;

            // Handle custom subject
            if ($subjectId === 'custom') {
                $customName = $details['custom_subject_name'];
                $customType = $details['custom_subject_type'];
                $stmt = $conn->prepare("INSERT INTO subjects (name, type) VALUES (?, ?)");
                $stmt->bind_param("ss", $customName, $customType);
                $stmt->execute();
                $subjectId = $conn->insert_id;
            }

            $stmt = $conn->prepare("INSERT INTO timetable (day, period, subject_id, staff_id, time_slot, is_merged) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiisi", $day, $periodNumber, $subjectId, $staffId, $time, $merge);
            $stmt->execute();
        }
    }

    echo "<script>alert('Timetable saved successfully!'); window.location.href='admin-dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Timetable</title>
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
    </style>
</head>
<body>
        <div class="navbar">
        <a href="admin-panel.php">Back to Admin Panel</a>
    </div>
<div class="content">
    <h2>Create Weekly Timetable</h2>
    <form method="POST">
        <table>
            <tr>
                <th>Day</th>
                <?php for ($i = 1; $i <= $periodsPerDay; $i++) echo "<th>Period $i</th>"; ?>
            </tr>
            <?php foreach ($days as $day): ?>
                <tr>
                    <td><strong><?= $day ?></strong></td>
                    <?php for ($periodNumber = 1; $periodNumber <= $periodsPerDay; $periodNumber++): ?>
                        <td>
                            <?php $id = $day . '_' . $periodNumber; ?>
                            
                            <!-- Subject -->
                            <label>Subject</label>
                            <select name="schedule[<?= $day ?>][<?= $periodNumber ?>][subject]" onchange="toggleCustomSubjectFields('<?= $id ?>', this)">
                                <option value="">Select Subject</option>
                                <option value="custom">Add Custom Subject</option>
                                <?php $subjectsResult->data_seek(0); ?>
                                <?php while ($row = $subjectsResult->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>

                            <!-- Custom Subject Fields -->
                            <div id="customSubjectFields_<?= $id ?>" class="custom-fields">
                                <input type="text" name="schedule[<?= $day ?>][<?= $periodNumber ?>][custom_subject_name]" placeholder="Subject Name" />
                                <input type="hidden" name="schedule[<?= $day ?>][<?= $periodNumber ?>][custom_subject_type]" value="lab_practical" />
                            </div>

                            <!-- Staff -->
                            <label>Staff</label>
                            <select name="schedule[<?= $day ?>][<?= $periodNumber ?>][staff]">
                                <option value="">Select Staff</option>
                                <?php $staffResult->data_seek(0); ?>
                                <?php while ($row = $staffResult->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['first_name'] . ' ' . $row['surname'] ?></option>
                                <?php endwhile; ?>
                            </select>

                            <!-- Time -->
                            <label>Time Slot</label>
                            <input type="text" name="schedule[<?= $day ?>][<?= $periodNumber ?>][time]" placeholder="e.g. 9:00AM - 10:00AM" />

                            <!-- Merge -->
                            <label class="merge-label">
                                <input type="checkbox" name="schedule[<?= $day ?>][<?= $periodNumber ?>][merge]" />
                                Merge Period
                            </label>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </table>

        <button type="submit">Save Timetable</button>
    </form>
</div>

<script>
    function toggleCustomSubjectFields(id, selectElement) {
        const div = document.getElementById('customSubjectFields_' + id);
        if (selectElement.value === 'custom') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';
        }
    }
</script>

</body>
</html>
