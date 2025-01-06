<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
    {   
header('location:index.php');
}
else{



?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Enroll History</title>
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
  
<?php if($_SESSION['login']!="")
{
 include('includes/menubar.php');
}
 ?>

    <div class="content-wrapper">
        <div class="container">
              <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Enroll History  </h1>
                    </div>
                </div>

                <div class="form-group">
    <label for="Semester">Select Semester</label>
    <select class="form-control" name="semester" id="semester" required>
        <?php
        $ongoingSemesterQuery = mysqli_query($bd, "SELECT id, semester FROM semester WHERE status='ongoing' LIMIT 1");
        $ongoingSemester = mysqli_fetch_array($ongoingSemesterQuery);

        $allSemestersQuery = mysqli_query($bd, "SELECT id, semester FROM semester ORDER BY id");

        while ($semester = mysqli_fetch_array($allSemestersQuery)) {
            $selected = ($semester['id'] == $ongoingSemester['id']) ? "selected" : "";
            ?>
          <option value="" disabled selected>Select a semester</option>
          <?php    echo "<option value='" . htmlentities($semester['id']) . "' $selected>" . htmlentities($semester['semester']) . "</option>";
        }
        ?>
    </select>
</div>
                <div class="row" >
            
                <div class="col-md-12">
                   
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           Enroll History
                        </div>

                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Semester</th>
                                            <th>Course Name </th>
                                             <th>Enrollment Date</th>
                                             <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
$selectedSemester = isset($_POST['semester']) ? $_POST['semester'] : $ongoingSemester['id'];

$sql = mysqli_query($bd, "SELECT courseenrolls.course as cid, 
                                 course.courseName as courname, 
                                 courseenrolls.enrollDate as edate, 
                                 semester.semester as sem 
                          FROM courseenrolls 
                          JOIN course ON course.courseCode = courseenrolls.course 
                          JOIN semester ON semester.semester = courseenrolls.semester 
                          WHERE courseenrolls.studentRegno = '" . $_SESSION['login'] . "' 
                          AND semester.id = '$selectedSemester'");
$cnt = 1;
while ($row = mysqli_fetch_array($sql)) {
?>
    <tr>
        <td><?php echo $cnt; ?></td>
        <td><?php echo htmlentities($row['sem']); ?></td>
        <td><?php echo htmlentities($row['courname']); ?></td>
        <td><?php echo htmlentities($row['edate']); ?></td>
        <td>
    <a href="generate_form.php?semester_id=<?php echo $row['sem']; ?>&student_id=<?php echo $_SESSION['login']; ?>" target="_blank">
        <button class="btn btn-primary"><i class="fa fa-print"></i> Print Form</button>
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
    </div>
   
  <?php include('includes/footer.php');?>
   
    <script src="assets/js/jquery-1.11.1.js"></script>
    
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
<?php } ?>
