<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
    exit; // Ensure no further processing after redirect
} else {
    // Delete student record
    if(isset($_GET['del']) && isset($_GET['id'])) {
        $studentRegNo = mysqli_real_escape_string($bd, $_GET['id']);
        mysqli_query($bd, "DELETE FROM students WHERE StudentRegno = '$studentRegNo'");
        $_SESSION['delmsg'] = "Student record deleted !!";
    }

    // Reset student password
    if(isset($_GET['pass']) && isset($_GET['id'])) {
        $studentRegNo = mysqli_real_escape_string($bd, $_GET['id']);
        $password = "12345"; // New password
        $newpass = md5($password); // Hash the password
        mysqli_query($bd, "UPDATE students SET password='$newpass' WHERE StudentRegno = '$studentRegNo'");
        $_SESSION['delmsg'] = "Password Reset. New Password is 12345";
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
<?php include('includes/header.php'); ?>
   
<?php if($_SESSION['alogin'] != "") {
    include('includes/menubar.php');
} ?>
   
    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line">Manage Students</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <font color="red" align="center"><?php echo htmlentities($_SESSION['delmsg']); ?><?php echo htmlentities($_SESSION['delmsg'] = ""); ?></font>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Student List
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive table-bordered">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Reg No</th>
                                            <th>Student Name</th>
                                            <th>Department</th>
                                            <th>Level</th>
                                            <th>CGPA</th>
                                            <th>Pincode</th>
                                            <th>Reg Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php
$sql = mysqli_query($bd, "SELECT * FROM students");
$cnt = 1;
while($row = mysqli_fetch_array($sql)) {
?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlentities($row['StudentRegno']); ?></td>
                                            <td><?php echo htmlentities($row['studentName']); ?></td>
                                            <td><?php echo htmlentities($row['department']); ?></td>
                                            <td><?php echo htmlentities($row['level']); ?></td>
                                            <td><?php echo htmlentities($row['cgpa']); ?></td>
                                            <td><?php echo htmlentities($row['pincode']); ?></td>
                                            <td><?php echo htmlentities($row['creationdate']); ?></td>
                                            <td>
                                                <a href="manage-students.php?id=<?php echo $row['StudentRegno']; ?>&del=delete" onClick="return confirm('Are you sure you want to delete?')">
                                                    <button class="btn btn-danger">Delete</button>
                                                </a>
                                                <a href="manage-students.php?id=<?php echo $row['StudentRegno']; ?>&pass=update" onClick="return confirm('Are you sure you want to reset password?')">
                                                    <button type="button" class="btn btn-default">Reset Password</button>
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
    
    <?php include('includes/footer.php'); ?>
    
    <script src="assets/js/jquery-1.11.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
</body>
</html>
<?php } ?>
