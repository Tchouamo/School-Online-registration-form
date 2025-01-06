<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_GET['id'])) {
    $courseId = $_GET['id'];
    $query = "SELECT * FROM course WHERE id = '$courseId'";
    $result = mysqli_query($bd, $query);
    $course = mysqli_fetch_assoc($result);

    if (!$course) {
        $_SESSION['msg'] = "Course not found!";
        header('location:course.php');
        exit();
    }

    // Fetch schedule details
    $scheduleQuery = "SELECT * FROM courseschedule WHERE course_id = '" . $course['courseCode'] . "'";
    $scheduleResult = mysqli_query($bd, $scheduleQuery);
    $schedules = [];
    while ($scheduleRow = mysqli_fetch_assoc($scheduleResult)) {
        $schedules[$scheduleRow['day']] = [
            'start_time' => $scheduleRow['start_time'],
            'end_time' => $scheduleRow['end_time'],
            'classroom' => $scheduleRow['classroom']
        ];
    }
}

if (isset($_POST['update'])) {
    $coursecode = $_POST['coursecode'];
    $coursename = $_POST['coursename'];
    $courseunit = $_POST['courseunit'];
    $teacherassigned = $_POST['teacherassigned'];
    $passinggrade = $_POST['passinggrade'];
    $departments = $_POST['department'];
    $level = $_POST['level'];
    $daysschedule = $_POST['daysschedule'];
    $starttimes = $_POST['starttime'];
    $endtimes = $_POST['endtime'];
    $classrooms = $_POST['classroom'];
    $prerequisites = isset($_POST['prerequisites']) ? $_POST['prerequisites'] : array();

    mysqli_begin_transaction($bd);

    try {
        $updateCourse = "
            UPDATE course 
            SET courseCode='$coursecode', 
                courseName='$coursename', 
                courseUnit='$courseunit', 
                teacherAssigned='$teacherassigned', 
                level='$level', 
                passingGrade='$passinggrade',
                updationDate=NOW()

            WHERE id='$courseId'";
        mysqli_query($bd, $updateCourse);

        // Update departments
        mysqli_query($bd, "DELETE FROM course_departments WHERE course_id='$coursecode'");
        foreach ($departments as $department_id) {
            $departmentQuery = "INSERT INTO course_departments (course_id, department_id) VALUES ('$coursecode', '$department_id')";
            mysqli_query($bd, $departmentQuery);
        }

        // Update prerequisites
        mysqli_query($bd, "DELETE FROM prerequisites WHERE course_id='$coursecode'");
        foreach ($prerequisites as $prerequisite) {
            $prerequisiteQuery = "INSERT INTO prerequisites (course_id, prerequisite_id) VALUES ('$coursecode', '$prerequisite')";
            mysqli_query($bd, $prerequisiteQuery);
        }

        // Update schedule
        mysqli_query($bd, "DELETE FROM courseschedule WHERE course_id='$coursecode'");
        foreach ($daysschedule as $day) {
            $starttime = isset($starttimes[$day]) ? $starttimes[$day] : '';
            $endtime = isset($endtimes[$day]) ? $endtimes[$day] : '';
            $classroom = isset($classrooms[$day]) ? $classrooms[$day] : '';

            $scheduleQuery = "
                INSERT INTO courseschedule (course_id, day, start_time, end_time, classroom) 
                VALUES ('$coursecode', '$day', '$starttime', '$endtime', '$classroom')";
            mysqli_query($bd, $scheduleQuery);
        }

        mysqli_commit($bd);
        $_SESSION['msg'] = "Course successfully updated!";
        header('location:course.php');
        exit();
    } catch (Exception $e) {
        mysqli_rollback($bd);
        $_SESSION['msg'] = "Error updating course: " . $e->getMessage();
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
    <title>Edit Course</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    

</head>
<body>
<?php include('includes/header.php'); ?>
<?php if ($_SESSION['alogin'] != "") include('includes/menubar.php'); ?>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Edit Course</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Edit Course</div>
                    <div class="panel-body">
                        <form method="post">
                            <div class="form-group">
                                <label>Course Code</label>
                                <input type="text" class="form-control" name="coursecode" value="<?php echo htmlentities($course['courseCode']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label>Course Name</label>
                                <input type="text" class="form-control" name="coursename" value="<?php echo htmlentities($course['courseName']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label>Course Unit</label>
                                <input type="text" class="form-control" name="courseunit" value="<?php echo htmlentities($course['courseUnit']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label>Passing Grade</label>
                                <input type="text" class="form-control" name="passinggrade" value="<?php echo htmlentities($course['passinggrade']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label>Level</label>
                                <select class="form-control" name="level" required>
                                    <option value="">Select Level</option>
                                    <?php
                                    $levels = mysqli_query($bd, "SELECT id, level FROM level");
                                    while ($level = mysqli_fetch_assoc($levels)) {
                                        $selected = ($level['level'] == $course['level']) ? 'selected' : '';
                                        echo "<option value='" . $level['level'] . "' $selected>" . $level['level'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Departments</label>
                                <select class="form-control" id="department" name="department[]" multiple="multiple">
    <?php
    $departments = mysqli_query($bd, "SELECT id, department FROM department");

    $selectedDepartments = mysqli_query($bd, "
        SELECT department_id 
        FROM course_departments 
        WHERE course_id='" . mysqli_real_escape_string($bd, $course['courseCode']) . "'
    ");

    $selectedDeptIds = [];
    while ($dept = mysqli_fetch_assoc($selectedDepartments)) {
        $selectedDeptIds[] = $dept['department_id'];
    }

    while ($department = mysqli_fetch_assoc($departments)) {
        $selected = in_array($department['department'], $selectedDeptIds) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($department['department']) . "' $selected>" 
            . htmlspecialchars($department['department']) 
            . "</option>";
    }
    ?>
</select>

                            </div>
                            <div class="form-group">
    <label for="daysschedule">Select Days</label>
    <select class="form-control" id="daysschedule" name="daysschedule[]" multiple required>
        <option value="Monday" <?php echo isset($schedules['Monday']) ? 'selected' : ''; ?>>Monday</option>
        <option value="Tuesday" <?php echo isset($schedules['Tuesday']) ? 'selected' : ''; ?>>Tuesday</option>
        <option value="Wednesday" <?php echo isset($schedules['Wednesday']) ? 'selected' : ''; ?>>Wednesday</option>
        <option value="Thursday" <?php echo isset($schedules['Thursday']) ? 'selected' : ''; ?>>Thursday</option>
        <option value="Friday" <?php echo isset($schedules['Friday']) ? 'selected' : ''; ?>>Friday</option>
    </select>
</div>

<div id="day-details-container">
    <?php foreach ($schedules as $day => $details): ?>
        <div class="day-container">
            <h4><?php echo $day; ?></h4>

            <div class="form-group">
                <label for="starttime_<?php echo $day; ?>">Start Time for <?php echo $day; ?></label>
                <input type="time" class="form-control" id="starttime_<?php echo $day; ?>" name="starttime[<?php echo $day; ?>]" value="<?php echo $details['start_time']; ?>" required />
            </div>

            <div class="form-group">
                <label for="endtime_<?php echo $day; ?>">End Time for <?php echo $day; ?></label>
                <input type="time" class="form-control" id="endtime_<?php echo $day; ?>" name="endtime[<?php echo $day; ?>]" value="<?php echo $details['end_time']; ?>" required />
            </div>

            <div class="form-group">
                <label for="classroom_<?php echo $day; ?>">Classroom for <?php echo $day; ?></label>
                <select class="form-control" id="classroom_<?php echo $day; ?>" name="classroom[<?php echo $day; ?>]" required>
                    <option value="" disabled>Select a classroom</option>
                    <?php
                    $classrooms = mysqli_query($bd, "SELECT id, classroom FROM classroom");
                    while ($classroom = mysqli_fetch_assoc($classrooms)) {
                        $selected = ($classroom['classroom'] == $details['classroom']) ? 'selected' : '';
                        echo "<option value='" . $classroom['classroom'] . "' $selected>" . htmlspecialchars($classroom['classroom']) . "</option>";
                    }
                    ?>
                </select>
            </div>

        </div>
        <div class="form-group">
        <label for="lecturer">Lecturer Assigned</label>
        <select class="form-control" id="lecturer" name="lecturer" required>
            <option value="" disabled>Select a lecturer</option>
            <?php
            // Fetch all lecturers
            $lecturers = mysqli_query($bd, "SELECT lecturerId, lecturerName FROM lecturer");
            while ($lecturer = mysqli_fetch_assoc($lecturers)) {
                $selected = ($lecturer['lecturerId'] == $course['teacherAssigned']) ? 'selected' : '';
                echo "<option value='" . $lecturer['lecturerId'] . "' $selected>" 
                    . htmlspecialchars($lecturer['lecturerName']) 
                    . "</option>";
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="prerequisites">Prerequisites</label>
        <select class="form-control" id="prerequisites" name="prerequisites[]" multiple="multiple">
            <?php
            $allCourses = mysqli_query($bd, "SELECT courseCode, courseName FROM course");

            $currentPrerequisites = mysqli_query($bd, "
                SELECT prerequisite_id 
                FROM prerequisites 
                WHERE course_id = '" . mysqli_real_escape_string($bd, $course['courseCode']) . "'
            ");
            $selectedPrereqs = [];
            while ($row = mysqli_fetch_assoc($currentPrerequisites)) {
                $selectedPrereqs[] = $row['prerequisite_id'];
            }

            while ($courseOption = mysqli_fetch_assoc($allCourses)) {
                $selected = in_array($courseOption['courseCode'], $selectedPrereqs) ? 'selected' : '';
                echo "<option value='" . $courseOption['courseCode'] . "' $selected>" 
                    . htmlspecialchars($courseOption['courseName']) 
                    . "</option>";
            }
            ?>
        </select>
    </div>
    <?php endforeach; ?>
</div>

                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                            <a href="course.php" class="btn btn-default">Cancel</a>

                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="assets/js/jquery-1.11.1.js"></script>
<script src="assets/js/bootstrap.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
  $(document).ready(function () {
    $('#daysschedule').select2({
        placeholder: "Select days",
        allowClear: true,
        width: '100%' 
    });

    $('#prerequisites').select2({
        placeholder: "Select prerequisites",
        allowClear: true,
        width: '100%'
    });

    $('#department').select2({
        placeholder: "Select departments",
        allowClear: true,
        width: '100%'
    });
    
    $('#lecturer').select2({
        placeholder: "Select a lecturer",
        allowClear: true,
        width: '100%'
    });

    $('#prerequisites').select2({
        placeholder: "Select prerequisites",
        allowClear: true,
        width: '100%'
    });

    $('#teacherassigned').select2({
        placeholder: "Select lecturer",
        allowClear: true,
        width: '100%'
    });

    $(document).on('change', '#daysschedule', function () {
    var selectedDays = $(this).val();
    var currentData = {};  

    $('#day-details-container .day-container').each(function () {
        var day = $(this).find('h4').text(); // Le jour en cours
        currentData[day] = {
            starttime: $(this).find('input[name^="starttime"]').val(),
            endtime: $(this).find('input[name^="endtime"]').val(),
            classroom: $(this).find('select[name^="classroom"]').val()
        };
    });

    $('#day-details-container').empty();

    if (selectedDays) {
        selectedDays.forEach(function (day) {
            var dayContainer = `
                <div class="day-container">
                    <h4>${day}</h4>
                    <div class="form-group">
                        <label for="starttime_${day}">Start Time for ${day}</label>
                        <input type="time" class="form-control" id="starttime_${day}" name="starttime[${day}]" value="${currentData[day] ? currentData[day].starttime : ''}" required />
                    </div>
                    <div class="form-group">
                        <label for="endtime_${day}">End Time for ${day}</label>
                        <input type="time" class="form-control" id="endtime_${day}" name="endtime[${day}]" value="${currentData[day] ? currentData[day].endtime : ''}" required />
                    </div>
                    <div class="form-group">
                        <label for="classroom_${day}">Classroom for ${day}</label>
                        <select class="form-control classroom-select" id="classroom_${day}" name="classroom[${day}]" required>
                            <option value="" disabled>Select a classroom</option>
                            <?php
                            $classrooms = mysqli_query($bd, "SELECT id, classroom FROM classroom");
                            while ($classroom = mysqli_fetch_assoc($classrooms)) {
                                echo "<option value='" . $classroom['classroom'] . "'>" . htmlspecialchars($classroom['classroom']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>`;
            $('#day-details-container').append(dayContainer);

            $('#classroom_' + day).val(currentData[day] ? currentData[day].classroom : '').select2({
                placeholder: "Select a classroom",
                allowClear: true,
                width: '100%'
            });
        });
    }
});

});

</script>

</body>
</html>
