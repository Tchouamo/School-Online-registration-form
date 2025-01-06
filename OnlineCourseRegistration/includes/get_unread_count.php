<?php
session_start();
include('config.php');

if (!isset($_SESSION['login']) || strlen($_SESSION['login']) == 0) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$studentRegno = $_SESSION['login'];
$query = "SELECT COUNT(*) AS unread_count FROM notification WHERE (studentRegno = '$studentRegno' OR is_for_all = 'yes' OR department_id = (SELECT department FROM students WHERE StudentRegno = '$studentRegno') OR level = (SELECT level FROM students WHERE StudentRegno = '$studentRegno' ) )AND is_read = 0";
$result = mysqli_query($bd, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode(['unread_count' => $row['unread_count']]);
} else {
    echo json_encode(['error' => 'Failed to fetch count']);
}
?>
