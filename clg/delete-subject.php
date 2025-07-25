<?php
include("db-config.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // First delete from subject_allotment to avoid foreign key error
    $conn->query("DELETE FROM subject_allotment WHERE subject_id = $id");
    // Then delete from subjects
    $conn->query("DELETE FROM subjects WHERE id = $id");
}

header("Location: view-subjects.php");
exit;
?>
