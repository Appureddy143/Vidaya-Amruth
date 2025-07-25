<?php
session_start();
include('db-config.php');

// Check if the user is logged in as admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjects = $_POST['subjects'];
    $branch = $_POST['branch'];

    // Use prepared statement for security
    $stmt = $conn->prepare("INSERT INTO subjects (name, subject_code, branch) VALUES (?, ?, ?)");
    
    foreach ($subjects as $subject) {
        $subject_name = $subject['subject_name'];
        $subject_code = $subject['subject_code'];
        $stmt->bind_param("sss", $subject_name, $subject_code, $branch);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo "<script>alert('Subjects added successfully!'); window.location.href='add-subject.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subjects</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
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
            max-width: 800px;
            width: 90%;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #555;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .subject-form-container {
            margin-top: 20px;
        }
        .subject-form {
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .plus-icon, .remove-icon {
            font-size: 20px;
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
            margin-left: 15px;
        }
        .plus-icon { color: #4CAF50; }
        .remove-icon { color: #f44336; }
        @media (max-width: 600px) {
            .content { padding: 20px; }
            button { font-size: 14px; }
            .navbar a { font-size: 16px; }
        }
    </style>
</head>
<body>
        <div class="navbar">
        <a href="admin-panel.php">Back to Admin Panel</a>
    </div>
    <div class="content">
        <h2>Add Subjects</h2>
        <form action="add-subject.php" method="POST">
            <div class="form-group">
                <label>Branch:</label>
                <select name="branch" required>
                    <option value="cs">Computer Science</option>
                    <option value="ec">Electronics</option>
                    <option value="me">Mechanical</option>
                    <option value="ce">Civil</option>
                </select>
            </div>

            <div class="subject-form-container" id="subject-form-container">
                <div class="subject-form" id="subject-form-0">
                    <div class="form-group">
                        <label>Subject Name:</label>
                        <input type="text" name="subjects[0][subject_name]" required>
                    </div>
                    <div class="form-group">
                        <label>Subject Code:</label>
                        <input type="text" name="subjects[0][subject_code]" required>
                    </div>
                    <span class="plus-icon" onclick="addSubjectForm()">+</span>
                </div>
            </div>

            <button type="submit">Add Subjects</button>
        </form>
    </div>

    <script>
        let subjectCount = 1;
        const maxSubjects = 10;

        function addSubjectForm() {
            if (subjectCount >= maxSubjects) return;

            const container = document.getElementById('subject-form-container');
            const newForm = document.createElement('div');
            newForm.classList.add('subject-form');
            newForm.id = 'subject-form-' + subjectCount;
            newForm.innerHTML = `
                <div class="form-group">
                    <label>Subject Name:</label>
                    <input type="text" name="subjects[${subjectCount}][subject_name]" required>
                </div>
                <div class="form-group">
                    <label>Subject Code:</label>
                    <input type="text" name="subjects[${subjectCount}][subject_code]" required>
                </div>
                <span class="plus-icon" onclick="addSubjectForm()">+</span>
                <span class="remove-icon" onclick="removeSubjectForm(${subjectCount})">-</span>
            `;

            container.appendChild(newForm);
            subjectCount++;
        }

        function removeSubjectForm(id) {
            const form = document.getElementById('subject-form-' + id);
            if (form) {
                form.remove();
                subjectCount--;
            }
        }
    </script>
</body>
</html>
