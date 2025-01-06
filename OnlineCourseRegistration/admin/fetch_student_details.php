<?php
include('includes/config.php');
if(isset($_POST['regno'])) {
    $regno = $_POST['regno'];
    $result = mysqli_query($bd, "SELECT first_name, last_name, program, level FROM studentsrecord WHERE enrollment_number='$regno'");
    $student = mysqli_fetch_assoc($result);
    echo json_encode($student);
}
?>
