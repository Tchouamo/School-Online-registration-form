<?php
session_start();
error_reporting(1);
include("includes/config.php");

function sendNotification($studentId, $semesterId, $type, $title, $message, $currentDate, $bd) {
    $checkNotificationQuery = "
        SELECT * FROM notification 
        WHERE studentRegno = '$studentId' 
          AND sent_date = '$currentDate'
          AND semester_id = '$semesterId'
          AND title = '$title'
    ";
    $checkNotificationResult = mysqli_query($bd, $checkNotificationQuery);

    if (mysqli_num_rows($checkNotificationResult) == 0) {
        $notificationQuery = "
            INSERT INTO notification (title, message, type, studentRegno, recipient_type, semester_id, sent_date)
            VALUES ('$title', '$message', '$type', '$studentId', 'specific', '$semesterId', '$currentDate')
        ";
        $notificationResult = mysqli_query($bd, $notificationQuery);

        if ($notificationResult) {
            echo "Notification sent to student: $studentId.<br>";
        } else {
            echo "Failed to send notification to student: $studentId.<br>";
        }
    }
}

function processNotifications($bd) {
    $currentDate = date('Y-m-d');
    $sql = "SELECT * FROM semester WHERE registration_deadline >= '$currentDate' AND status = 'ongoing'";
    $result = mysqli_query($bd, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $semesterId = $row['semester'];
            $registrationDeadline = $row['registration_deadline'];

            // Calculate notification thresholds
            $oneWeekBefore = date('Y-m-d', strtotime($registrationDeadline . ' - 1 week'));
            $threeDaysBefore = date('Y-m-d', strtotime($registrationDeadline . ' - 3 days'));

            // Fetch unregistered students
            $studentsQuery = "
                SELECT students.StudentRegno, students.studentName 
FROM students
WHERE students.StudentRegno NOT IN (
    SELECT student_semesters.studentRegno 
    FROM student_semesters 
    WHERE student_semesters.semester = '$semesterId'
)

            ";
            $studentsResult = mysqli_query($bd, $studentsQuery);

            while ($student = mysqli_fetch_assoc($studentsResult)) {
                $studentId = $student['StudentRegno'];

                if ($currentDate <= $oneWeekBefore) {
                    sendNotification(
                        $studentId, $semesterId, 'information', 
                        'Registration Reminder', 
                        "Please remember to register for the semester. Deadline: $registrationDeadline", 
                        $currentDate, $bd
                    );
                }

                if ($currentDate >= $threeDaysBefore) {
                    sendNotification(
                        $studentId, $semesterId, 'emergency', 
                        'Final Registration Reminder', 
                        "URGENT: Registration deadline is approaching in 3 days. Complete your registration now!", 
                        $currentDate, $bd
                    );
                }
            }
        }
    }
}

function updateStudentLevel($studentId, $db) {
    $query = "SELECT l.id AS current_level_id, l.`order` AS current_order, l.level AS level_name
              FROM studentsrecord s
              JOIN level l ON s.level = l.level
              WHERE s.enrollment_number = '$studentId'";

    $result = mysqli_query($db, $query);
    if (!$result) {
        return "Error in SQL query: " . mysqli_error($db);
    }

    $student = mysqli_fetch_assoc($result);
    if (!$student) {
        return "Student not found.";
    }

    $currentOrder = $student['current_order'];
    $currentLevel = $student['level_name'];

    $query = "SELECT * FROM level ORDER BY `order` ASC";   
    $result = mysqli_query($db, $query);
    if (!$result) {
        return "Error fetching levels: " . mysqli_error($db);
    }

    while ($level = mysqli_fetch_assoc($result)) {
        $levelOrder = $level['order'];
        $levelName = $level['level'];

        if ($levelOrder >= $currentOrder) {
            $query = "SELECT courseCode FROM course WHERE level = '$levelName'";
            $coursesResult = mysqli_query($db, $query);
            if (!$coursesResult) {
                return "Error fetching courses for level $levelName: " . mysqli_error($db);
            }

            $requiredCourses = [];
            while ($courseRow = mysqli_fetch_assoc($coursesResult)) {
                $requiredCourses[] = $courseRow['courseCode'];
            }

            if (empty($requiredCourses)) {
                return "No courses defined for level $levelName.";
            }

            $placeholders = implode(",", array_fill(0, count($requiredCourses), "'%s'"));
            $courses = implode(",", array_map(function($course) { return "'" . $course . "'"; }, $requiredCourses));

            $query = "SELECT COUNT(*) AS total_courses,
                             SUM(sc.grade <= 'C') AS passed_courses
                      FROM course c
                      LEFT JOIN student_courses sc ON c.courseCode = sc.course_id AND sc.student_id = '$studentId'
                      WHERE c.courseCode IN ($courses)";

            $result = mysqli_query($db, $query);
            if (!$result) {
                return "Error checking completed courses for level $levelName: " . mysqli_error($db);
            }

            $courseStatus = mysqli_fetch_assoc($result);

            if ($courseStatus['total_courses'] != $courseStatus['passed_courses']) {
                return "Student has not completed all courses for level $levelName.";
            }
        }
    }

    $nextLevelOrder = $currentOrder + 1;
    $query = "SELECT level FROM level WHERE `order` = $nextLevelOrder";
    $result = mysqli_query($db, $query);
    if (!$result) {
        return "Error fetching next level: " . mysqli_error($db);
    }

    $nextLevel = mysqli_fetch_assoc($result);
    if ($nextLevel) {
        $nextLevel = $nextLevel['level'];

        $query = "UPDATE studentsrecord SET level = '$nextLevel' WHERE enrollment_number = '$studentId'";
        if (mysqli_query($db, $query)) {
            $query2 = "UPDATE students SET level = '$nextLevel' WHERE StudentRegno = '$studentId'";
            if (mysqli_query($db, $query2)) {
                return "Student level updated successfully to the next level.";
            } else {
                return "Failed to update student level in students table: " . mysqli_error($db);
            }
        } else {
            return "Failed to update student level in studentsrecord table: " . mysqli_error($db);
        }
    } else {
        return "No higher level available.";
    }
}

if(isset($_POST['submit']))
{
    $regno=$_POST['regno'];
    $password=md5($_POST['password']);
$query=mysqli_query($bd, "SELECT * FROM students WHERE StudentRegno='$regno' and password='$password'");
if(mysqli_num_rows($query)>0)
{
$num=mysqli_fetch_array($query);
$extra="my-profile.php";
if ($num['status'] === 'Active') {
$_SESSION['login']=$_POST['regno'];
$_SESSION['id']=$num['studentRegno'];
$_SESSION['sname']=$num['studentName'];
} else {
    $_SESSION['errmsg'] = "Your account is inactive. Please contact administration.";
    header("Location: index.php");
    exit();
}
processNotifications($bd);
$studentId = $_SESSION['login'];
        $updateMessage = updateStudentLevel($studentId, $bd);
        $_SESSION['level_update_msg'] = $updateMessage;

$uip=$_SERVER['REMOTE_ADDR'];
$status=1;
$log=mysqli_query($bd, "insert into userlog(studentRegno,userip,status) values('".$_SESSION['login']."','$uip','$status')");

$host=$_SERVER['HTTP_HOST'];
$uri=rtrim(dirname($_SERVER['PHP_SELF']),'/\\');
header("location:http://$host$uri/$extra");
exit();
}
else
{
$_SESSION['errmsg']="Invalid Reg no or Password";
$extra="index.php";
$host  = $_SERVER['HTTP_HOST'];
$uri  = rtrim(dirname($_SERVER['PHP_SELF']),'/\\');

header("location:http://$host$uri/$extra");
exit();
}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Student Login</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>
<body>
    <?php include('includes/header.php');?>
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-head-line">Please Login To Enter </h4>

                </div>

            </div>
             <span style="color:red;" ><?php echo htmlentities($_SESSION['errmsg']); ?><?php echo htmlentities($_SESSION['errmsg']="");?></span>
            <form name="admin" method="post">
            <div class="row">
                <div class="col-md-6">
                     <label>Enter Reg no : </label>
                        <input type="text" name="regno" class="form-control"  />
                        <label>Enter Password :  </label>
                        <input type="password" name="password" class="form-control"  />
                        <hr />
                        <button type="submit" name="submit" class="btn btn-info"><span class="glyphicon glyphicon-user"></span> &nbsp;Log Me In </button>&nbsp;
                </div>
                </form>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        This is a free bootstrap admin template with basic pages you need to craft your project. 
                        Use this template for free to use for personal and commercial use.
                        <br />
                         <strong> Some of its features are given below :</strong>
                        <ul>
                            <li>
                                Responsive Design Framework Used
                            </li>
                            <li>
                                Easy to use and customize
                            </li>
                            <li>
                                Font awesome icons included
                            </li>
                            <li>
                                Clean and light code used.
                            </li>
                        </ul>
                       
                    </div>
                                    </div>

            </div>
        </div>
    </div>
 
    <?php include('includes/footer.php');?>
   
    <script src="assets/js/jquery-1.11.1.js"></script>

    <script src="assets/js/bootstrap.js"></script>

</body>   
</html>
