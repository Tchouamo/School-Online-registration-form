<?php
session_start();
include('includes/config.php');

if (isset($_GET['semester_id'])) {
    $semesterId = $_GET['semester_id'];

    $departmentsQuery = mysqli_query($bd, "
        SELECT DISTINCT department
        FROM students
        JOIN student_semesters ON students.StudentRegno = student_semesters.studentRegno
        WHERE student_semesters.semester = (SELECT semester FROM semester WHERE id = '$semesterId')
    ");
    
    $departments = [];
    while ($row = mysqli_fetch_array($departmentsQuery)) {
        $departments[] = ['id' => $row['department'], 'name' => $row['department']];
    }

    $levelsQuery = mysqli_query($bd, "
        SELECT DISTINCT level
        FROM students
        JOIN student_semesters ON students.StudentRegno = student_semesters.studentRegno
        WHERE student_semesters.semester = (SELECT semester FROM semester WHERE id = '$semesterId')
    ");
    
    $levels = [];
    while ($row = mysqli_fetch_array($levelsQuery)) {
        $levels[] = ['id' => $row['level'], 'name' => $row['level']];
    }

    echo json_encode(['departments' => $departments, 'levels' => $levels]);
} else {
    echo json_encode(['error' => 'No semester selected']);
}
?>
