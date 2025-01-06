<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
    {   
header('location:index.php');
}
else{
        $currentDate = date('Y-m-d');
        mysqli_query($bd, "
        UPDATE semester 
        SET status = 'concluded' 
        WHERE edate < '$currentDate' AND status = 'ongoing'
        ");
  

    if (isset($_POST['submit'])) {
    $semester = mysqli_real_escape_string($bd, $_POST['semester']);
    $sdate = mysqli_real_escape_string($bd, $_POST['sdate']);
    $edate = mysqli_real_escape_string($bd, $_POST['edate']);
    $registration_deadline = mysqli_real_escape_string($bd, $_POST['registration_deadline']);



    if ($sdate >= $edate) {
        $_SESSION['msg'] = "Error: Start date must be earlier than end date.";
        header("Location: semester.php");
        exit();
    }

    $overlapCheck = mysqli_query($bd, "
        SELECT * FROM semester 
        WHERE ('$sdate' <= edate AND '$edate' >= sdate)
    ");

    if (!$overlapCheck) {
        $_SESSION['msg'] = "Database Error: " . mysqli_error($bd);
        header("Location: semester.php");
        exit();
    }

    if (mysqli_num_rows($overlapCheck) > 0) {
        $_SESSION['msg'] = "Error: Semester dates overlap with an existing semester!";
        header("Location: semester.php");
        exit();
    }

    $check = mysqli_query($bd, "SELECT * FROM semester WHERE semester='$semester'");
    if (mysqli_num_rows($check) > 0) {
        $_SESSION['msg'] = "Error: Semester already exists!";
    } else {
        $ret = mysqli_query($bd, "
            INSERT INTO semester (semester, sdate, edate, registration_deadline, status) 
            VALUES ('$semester', '$sdate', '$edate', '$registration_deadline', 'ongoing') 
            ON DUPLICATE KEY UPDATE 
            sdate='$sdate', edate='$edate', status='ongoing'
        ");

        if ($ret) {
            $_SESSION['msg'] = "Semester Created Successfully !!";
        } else {
            $_SESSION['msg'] = "Error: Semester not created";
        }
    }

    header("Location: semester.php");
    exit();
}

      
if(isset($_GET['del']))
      {
              mysqli_query($bd, "delete from semester where id = '".$_GET['id']."'");
                  $_SESSION['delmsg']="semester deleted !!";
      }
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Admin | Semester</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
<style>
        .table td, .table th {
            vertical-align: middle; 
            text-align: center; 
        }
    </style>
<?php include('includes/header.php');?>
    
<?php if($_SESSION['alogin']!="")
{
 include('includes/menubar.php');
}
 ?>
    
    <div class="content-wrapper">
        <div class="container">
              <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Semester  </h1>
                    </div>
                </div>
                <div class="row" >
                  <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                        <div class="panel-heading">
                           Semester 
                        </div>
<font color="green" align="center"><?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?></font>


                        <div class="panel-body">
                       <form name="semester" method="post">
   <div class="form-group">
    <label for="semester">Add Semester  </label>
    <input type="text" class="form-control" id="semester" name="semester" placeholder="semester" required />
  </div>
  <div class="form-group">
    <label for="sdate">Start Date</label>
    <input type="date" class="form-control" id="sdate" name="sdate" required />
</div>
<div class="form-group">
    <label for="edate">End Date</label>
    <input type="date" class="form-control" id="edate" name="edate" required />
</div>
<div class="form-group">
    <label for="registration_deadline">Registration Deadline</label>
    <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" required />
</div>


 <button type="submit" name="submit" class="btn btn-default">Submit</button>
</form>
                            </div>
                            </div>
                    </div>
                  
                </div>
                <font color="red" align="center"><?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?></font>
                <div class="col-md-12">
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Manage Semester
                        </div>
                        <form method="post">
                            <button type="submit" name="updateStatus" class="btn btn-primary">Update Status</button>
                        </form>

                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                <thead>
    <tr>
        <th>#</th>
        <th>Semester</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Status</th>
        <th>Creation Date</th>
        <th>Updation Date</th>

        <th>Action</th>
    </tr>
</thead>
<tbody>
<?php
if (isset($_POST['updateStatus'])) {
    $currentDate = date('Y-m-d');
    mysqli_query($bd, "
        UPDATE semester 
        SET status = 'concluded' 
        WHERE edate < '$currentDate' AND status = 'ongoing'
    ");
    $_SESSION['msg'] = "Status Updated Successfully!";
}

$sql = mysqli_query($bd, "SELECT * FROM semester ORDER BY sdate DESC");
$cnt = 1;
while ($row = mysqli_fetch_array($sql)) {
?>
    <tr>
        <td><?php echo $cnt; ?></td>
        <td><?php echo htmlentities($row['semester']); ?></td>
        <td><?php echo htmlentities($row['sdate']); ?></td>
        <td><?php echo htmlentities($row['edate']); ?></td>
        <td><?php echo htmlentities($row['status']); ?></td>
        <td><?php echo htmlentities($row['creationDate']); ?></td>
        <td><?php echo htmlentities($row['updationDate']); ?></td>

        <td>
        <a href="edit-semester.php?id=<?php echo $row['id']; ?>">
    <button class="btn btn-primary">Edit</button>
</a>

            <a href="semester.php?id=<?php echo $row['id'] ?>&del=delete" onClick="return confirm('Are you sure you want to delete?')">
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
    </div>
  
  <?php include('includes/footer.php');?>
    
    <script src="assets/js/jquery-1.11.1.js"></script>
   
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
<?php } ?>
