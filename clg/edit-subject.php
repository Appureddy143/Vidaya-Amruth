<?php
include("db-config.php");

if (!isset($_GET['id'])) {
    echo "No subject selected!";
    exit;
}

$subject_id = intval($_GET['id']);

// Fetch subject details
$sql = "SELECT * FROM subjects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Subject not found!";
    exit;
}

$subject = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $code = trim($_POST['code']);
    $branch = trim($_POST['branch']);

    $update_sql = "UPDATE subjects SET name=?, code=?, branch=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $name, $code, $branch, $subject_id);

    if ($update_stmt->execute()) {
        header("Location: view-subjects.php?updated=1");
        exit;
    } else {
        $error = "Failed to update subject.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
    <style>
        body {
            font-family: Arial;
            background: #f9f9f9;
        }
        .container {
            width: 50%;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #aaa;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        a.back-btn {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Subject</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <label>Subject Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($subject['name']) ?>" required>

        <label>Subject Code:</label>
        <input type="text" name="code" value="<?= htmlspecialchars($subject['code']) ?>" required>

        <label>Branch:</label>
        <input type="text" name="branch" value="<?= htmlspecialchars($subject['branch']) ?>" required>

        <button type="submit">Update Subject</button>
    </form>
    <a class="back-btn" href="view-subjects.php">← Back to Subject List</a>
</div>
</body>
</html>
