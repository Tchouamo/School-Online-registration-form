<?php
session_start();
include('includes/config.php');

function updateOrRecreateNotifications($semesterId, $newDeadline, $bd) {

    $updateNotificationsQuery = "
        UPDATE notification 
        SET title = CONCAT('Update'),
            message = CONCAT('Updated registration deadline: ', '$newDeadline'),
            is_read = 0 
        WHERE semester_id = (SELECT semester FROM semester WHERE id = '$semesterId') AND (title = 'Registration Reminder' OR title = 'Final Registration Reminder')
    ";
    mysqli_query($bd, $updateNotificationsQuery);

}

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $semesterId = $_GET['id'];

        $semesterQuery = mysqli_query($bd, "SELECT * FROM semester WHERE id = '$semesterId'");
        $semester = mysqli_fetch_assoc($semesterQuery);

        if (!$semester) {
            $_SESSION['msg'] = "Semester not found!";
            header('Location: semester.php');
            exit();
        }

        if (isset($_POST['submit'])) {
            $semesterName = mysqli_real_escape_string($bd, $_POST['semester']);
            $sdate = mysqli_real_escape_string($bd, $_POST['sdate']);
            $edate = mysqli_real_escape_string($bd, $_POST['edate']);
            $registration_deadline = mysqli_real_escape_string($bd, $_POST['registration_deadline']);

            if ($sdate >= $edate) {
                $_SESSION['msg'] = "Error: Start date must be earlier than end date.";
                header("Location: edit-semester.php?id=$semesterId");
                exit();
            }

            $updateQuery = "UPDATE semester SET 
                                semester = '$semesterName',
                                sdate = '$sdate',
                                edate = '$edate',
                                registration_deadline = '$registration_deadline', updationDate = NOW()
                            WHERE id = '$semesterId'";

            $updateResult = mysqli_query($bd, $updateQuery);

            if ($updateResult) {
                updateOrRecreateNotifications($semesterId, $registration_deadline, $bd);
                $_SESSION['msg'] = "Semester Updated Successfully!";
                header("Location: semester.php");
            } else {
                $_SESSION['msg'] = "Error: Unable to update the semester.";
            }
        }
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
    <title>Admin | Edit Semester</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
</head>

<body>
<?php include('includes/header.php');?>

<?php if ($_SESSION['alogin'] != "") {
    include('includes/menubar.php');
} ?>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Edit Semester</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Edit Semester Information
                    </div>
                    <font color="green" align="center"><?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg'] = "");?></font>

                    <div class="panel-body">
                        <form name="semester" method="post">
                            <div class="form-group">
                                <label for="semester">Semester</label>
                                <input type="text" class="form-control" id="semester" name="semester" value="<?php echo htmlentities($semester['semester']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label for="sdate">Start Date</label>
                                <input type="date" class="form-control" id="sdate" name="sdate" value="<?php echo htmlentities($semester['sdate']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label for="edate">End Date</label>
                                <input type="date" class="form-control" id="edate" name="edate" value="<?php echo htmlentities($semester['edate']); ?>" required />
                            </div>
                            <div class="form-group">
                                <label for="registration_deadline">Registration Deadline</label>
                                <input type="date" class="form-control" id="registration_deadline" name="registration_deadline" value="<?php echo htmlentities($semester['registration_deadline']); ?>" required />
                            </div>

                            <button type="submit" name="submit" class="btn btn-default">Update</button>
                            <a href="semester.php" class="btn btn-default">Cancel</a>

                        </form>
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

