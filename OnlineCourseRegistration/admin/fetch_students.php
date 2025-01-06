

<?php
include('includes/config.php');
if(isset($_POST['query'])) {
    $query = $_POST['query'];
    $result = mysqli_query($bd, "SELECT StudentRegno, studentName FROM students 
    WHERE StudentRegno LIKE '%$query%' 
    OR studentName LIKE '%$query%' LIMIT 10");
    while($row = mysqli_fetch_assoc($result)) {
            echo '<li class="list-group-item" onclick="selectStudent(\'' . htmlentities($row['StudentRegno']) . '\')">'
            . htmlentities($row['StudentRegno']) . ' - ' . htmlentities($row['studentName']) . '</li>';

    }
}
?>
