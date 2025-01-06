<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{   
    header('location:index.php');
}
else {

    if (isset($_POST['submit'])) {
        $coursecode = $_POST['coursecode'];
        $coursename = $_POST['coursename'];
        $courseunit = $_POST['courseunit'];
        $teacherassigned = $_POST['teacherassigned'];  
        $passinggrade = $_POST['passinggrade']; 
        $departments = $_POST['department'];        $level = $_POST['level'];    
        $daysschedule = $_POST['daysschedule'];  
        $starttimes = $_POST['starttime'];      
        $endtimes = $_POST['endtime'];         
        $classrooms = $_POST['classroom'];  
        $prerequisites = isset($_POST['prerequisites']) ? $_POST['prerequisites'] : array(); 
        
        mysqli_begin_transaction($bd);

            $ret = mysqli_query($bd, "INSERT INTO course (courseCode, courseName, courseUnit, teacherAssigned, level) 
                VALUES ('$coursecode', '$coursename', '$courseunit', '$teacherassigned', '$level')");
              
            foreach ($departments as $department_id) {
        $departmentQuery = "INSERT INTO course_departments (course_id, department_id) VALUES ('$coursecode', '$department_id')";
        $departmentRet = mysqli_query($bd, $departmentQuery);
    }
            foreach ($daysschedule as $day) {
                $starttime = isset($starttimes[$day]) ? $starttimes[$day] : '';
                $endtime = isset($endtimes[$day]) ? $endtimes[$day] : '';
                $classroom = isset($classrooms[$day]) ? $classrooms[$day] : '';

                $scheduleQuery = "INSERT INTO courseschedule (course_id, day, start_time, end_time, classroom) 
                                  VALUES ('$coursecode', '$day', '$starttime', '$endtime', '$classroom')";

                $scheduleRet = mysqli_query($bd, $scheduleQuery);

            foreach ($prerequisites as $prerequisite) {
                $ret2 = mysqli_query($bd, "INSERT INTO prerequisites (course_id, prerequisite_id) 
                    VALUES ('$coursecode','$prerequisite')");
            }
      

            mysqli_commit($bd);
            $_SESSION['msg'] = "Course successfully created!";
    }
}
}
if (isset($_GET['del'])) {
    mysqli_query($bd, "DELETE FROM course WHERE id = '" . $_GET['id'] . "'");
    $_SESSION['delmsg'] = "Course Deleted!";
}
?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Admin | Course</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        .table td, .table th {
            vertical-align: middle; 
            text-align: center; 
        }
    </style>
</head>

<body>
<?php include('includes/header.php');?>

<?php if($_SESSION['alogin']!="") {
    include('includes/menubar.php');
} ?>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Course</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Course
                    </div>
                    <font color="green" align="center"><?php echo htmlentities($_SESSION['msg']); ?><?php echo htmlentities($_SESSION['msg'] = ""); ?></font>

                    <div class="panel-body">
                        <form name="dept" method="post">
                            <div class="form-group">
                                <label for="coursecode">Course Code</label>
                                <input type="text" class="form-control" id="coursecode" name="coursecode" placeholder="Course Code" required />
                            </div>

                            <div class="form-group">
                                <label for="coursename">Course Name</label>
                                <input type="text" class="form-control" id="coursename" name="coursename" placeholder="Course Name" required />
                            </div>

                            <div class="form-group">
                                <label for="courseunit">Course Unit </label>
                                <input type="text" class="form-control" id="courseunit" name="courseunit" placeholder="Course Unit (e.g 1, 2...)" required />
                            </div>
                            <div class="form-group">
                                <label for="passinggrade">Passing Grade</label>
                                <input type="text" class="form-control" id="passinggrade" name="passinggrade" placeholder="Passing Grade (e.g A, B...)" required />
                            </div>
                            <div class="form-group">
                                <label for="department">Select Departments</label>
                                <select class="form-control" id="department" name="department[]" multiple="multiple" required>
                                    <?php
                                    $query = "SELECT department FROM department";
                                    $result = mysqli_query($bd, $query);

                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . $row['department'] . "'>" . htmlspecialchars($row['department']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="level">Level</label>
                                <select class="form-control" id="level" name="level" required>
                                <option value="" selected disabled>Select Level</option>
                                    <?php
                                    $query = "SELECT id, level FROM level";
                                    $result = mysqli_query($bd, $query);
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . $row['level'] . "'>" . htmlspecialchars($row['level']) . " (" . htmlspecialchars($row['id']) . ")</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="prerequisites">Select Prerequisites</label>
                                <select class="form-control" id="prerequisites" name="prerequisites[]" multiple="multiple">
                            <?php
                            $query = "SELECT courseCode, courseName FROM course";
                            $result = mysqli_query($bd, $query);

                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['courseCode'] . "'>" . htmlspecialchars($row['courseName']) . " (" . htmlspecialchars($row['courseCode']) . ")</option>";
                            }
                            ?>
                        </select>

                            </div>


                            <div class="form-group">
                                <label for="daysschedule">Select Days</label>
                                <select class="form-control" id="daysschedule" name="daysschedule[]" multiple required>
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                </select>
                            </div>

                            <div id="day-details-container"></div>

                            <div class="form-group">
                                <label for="teacherassigned">Teacher Asssigned</label>
                                <select class="form-control" id="teacherassigned" name="teacherassigned" required>
                                <option value="">Select Lecturer</option>
                                    <?php
                                    $query = "SELECT lecturerId, lecturerName FROM lecturer";
                                    $result = mysqli_query($bd, $query);
                                    
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<option value='" . $row['lecturerId'] . "'>" . htmlspecialchars($row['lecturerName']) . " (" . htmlspecialchars($row['lecturerId']) . ")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            

                            <button type="submit" name="submit" class="btn btn-default">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <font color="red" align="center"><?php echo htmlentities($_SESSION['delmsg']); ?><?php echo htmlentities($_SESSION['delmsg'] = ""); ?></font>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Manage Course
                </div>

                <div class="panel-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Course Unit</th>
                                    <th>Level</th>
                                    <th>Creation Date</th>
                                    <th>Updation Date</th>

                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$sql = mysqli_query($bd, "SELECT * FROM course");
$cnt = 1;
while($row = mysqli_fetch_array($sql)) {
?>
<tr>
    <td><?php echo $cnt; ?></td>
    <td><?php echo htmlentities($row['courseCode']); ?></td>
    <td><?php echo htmlentities($row['courseName']); ?></td>
    <td><?php echo htmlentities($row['courseUnit']); ?></td>
    <td><?php echo htmlentities($row['level']); ?></td>
    <td><?php echo htmlentities($row['creationDate']); ?></td>
    <td><?php echo htmlentities($row['updationDate']); ?></td>

    <td>
        <a href="edit-course.php?id=<?php echo $row['id']; ?>">
            <button class="btn btn-primary"><i class="fa fa-edit"></i> Edit</button>
        </a>

        <a href="course.php?id=<?php echo $row['id']?>&del=delete" onClick="return confirm('Are you sure you want to delete?')">
            <button class="btn btn-danger">Delete</button>
        </a>
    </td>
</tr>
<?php
    $cnt++;
}
?>


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include('includes/footer.php');?>

<script src="assets/js/jquery-1.11.1.js"></script>
<script src="assets/js/bootstrap.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>

<?php
$query = "SELECT id, classroom FROM classroom";
$result = mysqli_query($bd, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $options .= "<option value='" . $row['classroom'] . "'>" . htmlspecialchars($row['classroom']) . "</option>";
}
?>

    $(document).ready(function() {

     $('#daysschedule').select2({
        placeholder: "Select days",
        allowClear: true
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

    $('#department').select2({
            placeholder: "Select departments",
            allowClear: true,
            width: '100%' 
        });

    
        $('#daysschedule').change(function() {
    var selectedDays = $(this).val();  
    var currentData = {}; // Stocker les informations actuelles des jours

    // Récupérer les valeurs existantes pour chaque jour si elles sont déjà présentes
    $('#day-details-container .day-container').each(function() {
        var day = $(this).find('h4').text(); // Le jour en cours
        currentData[day] = {
            starttime: $(this).find('input[name^="starttime"]').val(),
            endtime: $(this).find('input[name^="endtime"]').val(),
            classroom: $(this).find('select[name^="classroom"]').val()
        };
    });

    $('#day-details-container').empty();  

    if (selectedDays) {
        selectedDays.forEach(function(day) {
            var dayContainer = $('<div class="day-container"></div>');
            dayContainer.append('<h4>' + day + '</h4>'); 

            var starttimeValue = currentData[day] ? currentData[day].starttime : '';
            dayContainer.append(
                '<div class="form-group">' +
                '<label for="starttime_' + day + '">Start Time for ' + day + '</label>' +
                '<input type="time" class="form-control" id="starttime_' + day + '" name="starttime[' + day + ']" value="' + starttimeValue + '" required />' +
                '</div>'
            );

            var endtimeValue = currentData[day] ? currentData[day].endtime : '';
            dayContainer.append(
                '<div class="form-group">' +
                '<label for="endtime_' + day + '">End Time for ' + day + '</label>' +
                '<input type="time" class="form-control" id="endtime_' + day + '" name="endtime[' + day + ']" value="' + endtimeValue + '" required />' +
                '</div>'
            );

            var classroomValue = currentData[day] ? currentData[day].classroom : '';
            dayContainer.append(`
                <div class="form-group">
                    <label for="classroom_${day}">Classroom for ${day}</label>
                    <select class="form-control" id="classroom_${day}" name="classroom[${day}]" required>
                        <option value="" disabled selected>Select a classroom</option>
                        <?php echo $options; ?>
                    </select>
                </div>
            `);

            dayContainer.find('select[name^="classroom"]').val(classroomValue);

            $('#day-details-container').append(dayContainer);
        });
    }
});

});

</script>

</body>
</html>

