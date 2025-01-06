<?php
if (isset($_GET['course_id'])) {
    $courseId = $_GET['course_id'];

    include('includes/config.php');
    
    $result = mysqli_query($bd, "SELECT course.courseName as courseName, course.courseUnit as courseUnit, course.passinggrade as passinggrade, lecturer.lecturerName as lecturer FROM course  JOIN lecturer ON course.teacherAssigned = lecturer.lecturerId WHERE courseCode = '$courseId'");
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode([
            'courseName' => $row['courseName'],
            'courseUnit' => $row['courseUnit'],
            'passinggrade' => $row['passinggrade'],
            'lecturer' => $row['lecturer'],

        ]);
    } else {
        echo json_encode(['error' => 'Course not found']);
    }
}
?>
