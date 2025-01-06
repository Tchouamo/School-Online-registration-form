<?php
include('includes/config.php');
if(isset($_POST['query'])) {
    $query = $_POST['query'];
    $result = mysqli_query($bd, "SELECT enrollment_number FROM studentsrecord WHERE status = 'Active' AND enrollment_number LIKE '%$query%'");
    while($row = mysqli_fetch_assoc($result)) {
        echo '<li class="list-group-item" onclick="selectRegNo(\'' . $row['enrollment_number'] . '\')">' . $row['enrollment_number'] . '</li>';
    }
}
?>
