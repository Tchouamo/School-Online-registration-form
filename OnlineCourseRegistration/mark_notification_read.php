<?php
session_start();
include('includes/config.php');

if (isset($_POST['id'])) {
    $notificationId = intval($_POST['id']);
    $query = "UPDATE notification SET is_read = 1 WHERE id = '$notificationId'";

    if (mysqli_query($bd, $query)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
