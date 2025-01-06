<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['login'])==0 or strlen($_SESSION['pcode'])==0)
    {   
header('location:index.php');
}
else{

function hasCompletedPrerequisites($studentRegno, $courseId, $bd) {
  $prerequisitesQuery = "SELECT prerequisite_id FROM prerequisites WHERE course_id = ?";
  $stmt = $bd->prepare($prerequisitesQuery);
  $stmt->bind_param("i", $courseId);
  $stmt->execute();
  $result = $stmt->get_result();
  
  $prerequisites = [];
  while ($row = $result->fetch_assoc()) {
      $prerequisites[] = $row['prerequisite_id'];
  }

  foreach ($prerequisites as $prerequisiteId) {
      $completedQuery = "SELECT * FROM student_courses WHERE student_id = ? AND course_id = ?";
      $checkStmt = $bd->prepare($completedQuery);
      $checkStmt->bind_param("si", $studentRegno, $prerequisiteId);
      $checkStmt->execute();
      $completedResult = $checkStmt->get_result();
      if ($completedResult->num_rows == 0) {
          return false;
      }
  }

  return true; 
}

function isFromHisDepartment($studentRegno, $courseId, $bd) {
  $result = mysqli_query($bd, "SELECT department_id FROM course_departments WHERE course_id = '$courseId'");
  
  if (mysqli_num_rows($result) == 0) {
      return false;
  }

  $departments = [];
  
  while ($row = mysqli_fetch_assoc($result)) {
      $departments[] = $row['department_id'];
  }

  foreach ($departments as $departmentId) {
      $completedResult = mysqli_query($bd, "SELECT * FROM students WHERE StudentRegno = '$studentRegno' AND department = '$departmentId'");
      
      if (mysqli_num_rows($completedResult) > 0) {
          return true;
      }
  }

  return false;
}


function isFromHisLevel($studentRegno, $courseId, $bd) {
  
  $checklevel = mysqli_query($bd, "SELECT * FROM course WHERE level = (SELECT level FROM students WHERE StudentRegno = '$studentRegno' ) AND courseCode = '$courseId'");
  if (mysqli_num_rows($checklevel) == 0) {
    return false;
  }
  return true; 
}


function hasCompletedCourse($studentRegno, $courseId, $bd) {
  
  $checkcourse = mysqli_query($bd, "SELECT * FROM student_courses WHERE student_id = '$studentRegno' AND course_id = '$courseId'");
  if (mysqli_num_rows($checkcourse) == 0) {
    return false;
  }
  return true; 
}

if(isset($_POST['submit']))
{
$studentregno=$_POST['studentregno'];
$pincode=$_POST['Pincode'];
$department=$_POST['Department'];
$sem=$_POST['Semester'];

$isEnrolled = false;
$checkEnrollment = mysqli_query($bd, "SELECT * FROM courseenrolls WHERE studentRegno='$studentregno' AND semester='$sem'");
if (mysqli_num_rows($checkEnrollment) > 0) {
    $isEnrolled = true;
    $_SESSION['msg'] = "You've already registered for this semester!";
    header('location:timetable.php'); 
    exit();
}

$enrollmentSuccessful = true;

foreach ($_POST['courses'] as $course) {

    $ret = mysqli_query($bd, "INSERT INTO courseenrolls(studentRegno, pincode, department, course, semester) 
                          VALUES('$studentregno', '$pincode', '$department', '$course', '$sem')");
}

if($ret) {
  $_SESSION['msg'] = "Registration Successful!";
} else {
  $enrollmentSuccessful = false;
  $_SESSION['msg'] = "Error: Failure.";
}

if ($enrollmentSuccessful && !$isEnrolled) {
  $updateStatus = mysqli_query($bd, "INSERT INTO student_semesters(studentRegno, semester, status)
                                    VALUES ('$studentregno', '$sem', 'enrolled')");

  if ($updateStatus ) {
      $notificationStatus = mysqli_query($bd, 
          "INSERT INTO notification (title, message, type, studentRegno, recipient_type)
          VALUES (
              'ENROLLMENT UPDATE', 
              'Dear $studentregno, you have successfully registered your courses for semester $sem. Courage!', 
              'information', 
              '$studentregno', 
              'specific'
          )"
      );

      if ($notificationStatus) {
          $_SESSION['msg'] = "Registration Successful and notification sent!";
      } else {
          $_SESSION['msg'] = "Registration Successful, but failed to send notification.";
      }
  } else {
      $_SESSION['msg'] = "Registration Successful but failed to update status.";
  }
} else {
  $_SESSION['msg'] = "Error: Failure in registration.";
}
}}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Course Enroll</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    
    <style>
      
    </style>

  </head>

<body>
<?php include('includes/header.php');?>
    
<?php if($_SESSION['login']!="")
{
 include('includes/menubar.php');
}
 ?>
    <!-- MENU SECTION END-->
    <div class="content-wrapper">
        <div class="container">
              <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Course Enroll </h1>
                    </div>
                </div>
                <div class="row" >
                  <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                        <div class="panel-heading">
                          Course Enroll
                        </div>
<font color="green" align="center"><?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?></font>
<?php $sql=mysqli_query($bd, "select * from students where StudentRegno='".$_SESSION['login']."'");
$cnt=1;
while($row1=mysqli_fetch_array($sql))
{ ?>

                        <div class="panel-body">
                       <form name="dept" method="post" enctype="multipart/form-data">
   <div class="form-group">
    <label for="studentname">Student Name  </label>
    <input type="text" class="form-control" id="studentname" name="studentname" readonly value="<?php echo htmlentities($row1['studentName']);?>"  />
  </div>

 <div class="form-group">
    <label for="studentregno">Student Reg No   </label>
    <input type="text" class="form-control" id="studentregno" name="studentregno" value="<?php echo htmlentities($row1['StudentRegno']);?>"  placeholder="Student Reg no" readonly />
    
  </div>

<div class="form-group">
    <label for="Pincode">Pincode  </label>
    <input type="text" class="form-control" id="Pincode" name="Pincode" readonly value="<?php echo htmlentities($row1['pincode']);?>" required />
  </div>
  
  <div class="form-group">
    <label for="Level">Department  </label>
    <input type="text" class="form-control" id="level" name="Level" readonly value="<?php echo htmlentities($row1['level']);?>" required />
</div> 

<div class="form-group">
    <label for="Pincode">Student Photo  </label>
   <?php if($row1['studentPhoto']==""){ ?>
   <img src="studentphoto/noimage.png" width="200" height="200"><?php } else {?>
   <img src="studentphoto/<?php echo htmlentities($row1['studentPhoto']);?>" width="200" height="200">
   <?php } ?>
  </div>

<div class="form-group">
    <label for="Department">Department  </label>
    <input type="text" class="form-control" id="department" name="Department" readonly value="<?php echo htmlentities($row1['department']);?>" required />
</div> 

<?php 
    $sql=mysqli_query($bd, "select semester from semester where status ='ongoing' ");
    while($row=mysqli_fetch_array($sql))
    {
    ?>
  <div class="form-group">
    <label for="Semester">Semester  </label>
    <input type="text" class="form-control" id="Semester" name="Semester" readonly value="<?php echo htmlentities($row['semester']);?>" required  />
  </div>
<?php } ?>
  
  
<div class="form-group">
  <label for="Course">Courses</label><br>
  <?php 
                            $sql = mysqli_query($bd, "SELECT * FROM course");
                            while($row = mysqli_fetch_array($sql)) {
                                $courseCredits = htmlentities($row['courseUnit']);
                                $courseCode = htmlentities($row['courseCode']);
                                
                                $checkprereq = mysqli_query($bd, "SELECT prerequisite_id FROM prerequisites WHERE course_id = '{$row['courseCode']}'");
                                if(isFromHisLevel($row1['StudentRegno'], $row['courseCode'], $bd) && isFromHisDepartment($row1['StudentRegno'], $row['courseCode'], $bd)){
                                 if (!hasCompletedCourse($row1['StudentRegno'], $row['courseCode'], $bd)){
                                    if (mysqli_num_rows($checkprereq) == 0 || hasCompletedPrerequisites($row1['StudentRegno'], $row['courseCode'], $bd)) {
                                    
                             ?>
                                    
                                    <input type="checkbox" name="courses[]" value="<?php echo $courseCode; ?>" data-credit="<?php echo $courseCredits; ?>">
                                    <a href="#" class="course-name" data-course-id="<?php echo $courseCode; ?>">
                                        <?php echo $courseCode; ?> - <?php echo $row['courseName']; ?> (Credits: <?php echo $courseCredits; ?>)
                                    </a><br>
                                    
                            <?php 
                                    } }
                              }
                            }

                            ?>
</div>

  <div id="credits-remaining">
    Remaining Credits : <span id="remaining-credits">21</span>
  </div>
  <?php } ?>


 <button type="submit" name="submit" id="submit" class="btn btn-default">Enroll</button>

</form>

                            </div>
                            </div>
                    </div>
                  
                </div>

            </div>





        </div>
    </div>


    <div class="modal fade" id="courseInfoModal" tabindex="-1" role="dialog" aria-labelledby="courseInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="courseInfoModalLabel">Course Information</h4>
      </div>
      <div class="modal-body">
        <p><strong>Name:</strong> <span id="modal-course-name"></span></p>
        <p><strong>Credits:</strong> <span id="modal-course-credits"></span></p>
        <p><strong>Passing Grade:</strong> <span id="modal-course-grade"></span></p>
        <p><strong>Lecturer:</strong> <span id="modal-course-lecturer"></span></p>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

 
  <?php include('includes/footer.php');?>
    <script src="assets/js/jquery-1.11.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('input[name="courses[]"]');
    const maxCredits = 21; 
    const remainingCreditsDisplay = document.getElementById('remaining-credits');
    let totalCreditsUsed = 0;

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const courseCredits = parseInt(this.dataset.credit);

            if (this.checked) {
                if (totalCreditsUsed + courseCredits > maxCredits) {
                    alert("You exceeded 21 credits.");
                    this.checked = false; 
                    return;
                }
                totalCreditsUsed += courseCredits;
            } else {
                totalCreditsUsed -= courseCredits;
            }

            remainingCreditsDisplay.textContent = maxCredits - totalCreditsUsed;
        });
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const courseNames = document.querySelectorAll('.course-name');

    courseNames.forEach(course => {
        course.addEventListener('click', function (event) {
            event.preventDefault(); 

            const courseId = this.dataset.courseId; 

            fetch(`get_course_info.php?course_id=${courseId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(`Error: ${data.error}`);
                    } else {
                        document.getElementById('modal-course-name').textContent = data.courseName;
                        document.getElementById('modal-course-credits').textContent = data.courseUnit;
                        document.getElementById('modal-course-grade').textContent = data.passinggrade;
                        document.getElementById('modal-course-lecturer').textContent = data.lecturer;

                        $('#courseInfoModal').modal('show'); 
                    }
                })
                .catch(error => console.error('Error fetching course information:', error));
        });
    });
});


</script>

</body>
</html>

