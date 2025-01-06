<?php
session_start();
include('includes/config.php');

$semesterId = isset($_GET['semester_id']) ? $_GET['semester_id'] : '';
$departmentId = isset($_GET['department_id']) ? $_GET['department_id'] : '';
$level = isset($_GET['level']) ? $_GET['level'] : '';

// Construct query with filters
$query = "SELECT 
            student_semesters.studentRegno as sr, 
            students.studentName as sn,
            students.department as department, 
            students.level as level,
            student_semesters.date_recorded as edate, 
            student_semesters.semester as sem
          FROM student_semesters
          JOIN students ON students.StudentRegno = student_semesters.studentRegno
          WHERE student_semesters.semester = (SELECT semester FROM semester WHERE id = '$semesterId')";

if ($departmentId) {
    $query .= " AND students.department = '$departmentId'";
}

if ($level) {
    $query .= " AND students.level = '$level'";
}

$sqlEnrollments = mysqli_query($bd, $query);

$cnt = 1;
$output = '';
while ($row = mysqli_fetch_array($sqlEnrollments)) {
    $output .= "<tr>
                    <td>".$cnt."</td>
                    <td>".htmlentities($row['sr'])."</td>
                    <td>".htmlentities($row['sn'])."</td>
                    <td>".htmlentities($row['department'])."</td>
                    <td>".htmlentities($row['level'])."</td>
                    <td>".htmlentities($row['sem'])."</td>
                    <td>".htmlentities($row['edate'])."</td>
                 </tr>";
    $cnt++;
}

echo $output;
?>
