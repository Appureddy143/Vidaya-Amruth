// Only allow edit if within 1 hour of marked_at
$check = $conn->prepare("SELECT marked_at FROM attendance WHERE id = ?");
$check->bind_param("i", $attendance_id);
$check->execute();
$res = $check->get_result()->fetch_assoc();

$now = new DateTime();
$marked_time = new DateTime($res['marked_at']);
$interval = $now->diff($marked_time);
$minutes = ($interval->h * 60) + $interval->i;

if ($minutes <= 60) {
    // Allow edit
} else {
    echo "❌ Attendance edit not allowed after 1 hour.";
}
