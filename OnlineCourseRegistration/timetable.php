<?php 
session_start();
include('includes/config.php');

if(strlen($_SESSION['login'])==0)
{   
    header('location:index.php');
}
else {

    $studentId = $_SESSION['login'];

    $ongoingSemesterQuery = mysqli_query($bd, "SELECT id, semester FROM semester WHERE status='ongoing' LIMIT 1");
    $ongoingSemester = mysqli_fetch_array($ongoingSemesterQuery);

    if (!$ongoingSemester) {
        die("No ongoing semester found. Please check the semester configuration.");
    }

    $semesterId = isset($_POST['semester']) ? $_POST['semester'] : $ongoingSemester['semester'];

    $query = "
    SELECT cs.day, cs.start_time, cs.end_time, cs.classroom, c.courseName, c.courseCode
    FROM courseschedule cs
    JOIN course c ON cs.course_id = c.courseCode
    JOIN courseenrolls ce ON c.courseCode = ce.course
    WHERE ce.studentRegno = '$studentId'
    AND ce.semester = '$semesterId'
    ORDER BY FIELD(cs.day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), cs.start_time;
    ";

    $result = mysqli_query($bd, $query);
    if (!$result) {
        die("Query failed: " . mysqli_error($bd)); 
    }
    

    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    $timetable = [];
    foreach ($days as $day) {
        $timetable[$day] = [];
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $timetable[$row['day']][] = $row;
    }

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Schedule</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <style>
        .day-heading {
            font-weight: bold;
            font-size: 1.5em;
            padding: 10px;
            margin: 10px 0;
        }

        .monday { background-color: #e3f2fd; color: #0d47a1; }
        .tuesday { background-color: #ffebee; color: #c62828; }
        .wednesday { background-color: #e8f5e9; color: #2e7d32; }
        .thursday { background-color: #fff3e0; color: #ef6c00; }
        .friday { background-color: #ede7f6; color: #6a1b9a; }
        .saturday { background-color: #fbe9e7; color: #d84315; }
        .sunday { background-color: #f3e5f5; color: #4a148c; }
    </style>
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
<?php include('includes/header.php'); ?>
<?php if ($_SESSION['login'] != "") include('includes/menubar.php'); ?>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Timetable</h1>
            </div>
        </div>

        <div class="form-group">
            <label for="Semester">Select Semester</label>
            <form method="POST" action="timetable.php">
                <select class="form-control" name="semester" id="semester" required>
                    <option value="" disabled selected>Select a semester</option>
                    <?php
                    $allSemestersQuery = mysqli_query($bd, "SELECT semester FROM semester ORDER BY id");
                    while ($semester = mysqli_fetch_array($allSemestersQuery)) {
                        $selected = ($semester['semester'] == $semesterId) ? "selected" : "";
                        echo "<option value='" . htmlentities($semester['semester']) . "' $selected>" . htmlentities($semester['semester']) . "</option>";
                    }
                    ?>
                    
                </select>
                <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Apply</button>
            </form>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">Timetable</div>
            <div class="panel-body">
                <?php foreach ($timetable as $day => $courses): ?>
                    <h3 class="day-heading <?php echo strtolower($day); ?>"><?php echo $day; ?></h3>
                    <?php if (count($courses) > 0): ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Course</th>
                                    <th>Classroom</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?php echo htmlentities($course['start_time']); ?></td>
                                        <td><?php echo htmlentities($course['end_time']); ?></td>
                                        <td><?php echo htmlentities($course['courseCode'].' - '.$course['courseName']); ?></td>
                                        <td><?php echo htmlentities($course['classroom']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No courses scheduled for this day.</p>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>
<script src="assets/js/jquery-1.11.1.js"></script>
<script src="assets/js/bootstrap.js"></script>
</body>
</html>
<?php } ?>
