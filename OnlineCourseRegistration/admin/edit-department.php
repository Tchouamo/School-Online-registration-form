<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $department_id = $_GET['id'];

        $departmentQuery = mysqli_query($bd, "SELECT * FROM department WHERE id = '$department_id'");
        $departmentData = mysqli_fetch_assoc($departmentQuery);
        
        if (isset($_POST['submit'])) {
            $department = $_POST['department'];
            
            $updateQuery = mysqli_query($bd, "UPDATE department SET department = '$department', updationDate = NOW() WHERE id = '$department_id'");

            if ($updateQuery) {
                $_SESSION['msg'] = "Department updated successfully!";
                header('Location: department.php');
                exit();
            } else {
                $_SESSION['msg'] = "Error: Department not updated!";
            }
        }
    }

    if (isset($_GET['del'])) {
        $deleteId = $_GET['id'];
        mysqli_query($bd, "DELETE FROM department WHERE id = '$deleteId'");
        $_SESSION['delmsg'] = "Department deleted!";
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Admin | Department</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>

    <?php if ($_SESSION['alogin'] != "") {
        include('includes/menubar.php');
    }
    ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line">Department</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <?php echo isset($departmentData) ? "Edit Department" : "Add Department"; ?>
                        </div>

                        <font color="green" align="center"><?php echo htmlentities($_SESSION['msg']);
                                                            $_SESSION['msg'] = ""; ?></font>

                        <div class="panel-body">
                            <form name="dept" method="post">
                                <div class="form-group">
                                    <label for="department">Department Name</label>
                                    <input type="text" class="form-control" id="department" name="department" placeholder="Department Name" value="<?php echo isset($departmentData) ? htmlentities($departmentData['department']) : ''; ?>" required />
                                </div>
                                <button type="submit" name="submit" class="btn btn-default">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <font color="red" align="center"><?php echo htmlentities($_SESSION['delmsg']);
                                            $_SESSION['delmsg'] = ""; ?></font>

            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Manage Department
                    </div>

                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Department</th>
                                        <th>Creation Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = mysqli_query($bd, "SELECT * FROM department");
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_array($sql)) {
                                    ?>

                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlentities($row['department']); ?></td>
                                            <td><?php echo htmlentities($row['creationDate']); ?></td>
                                            <td>
                                                <a href="department.php?id=<?php echo $row['id'] ?>">
                                                    <button class="btn btn-primary"><i class="fa fa-edit"></i> Edit</button>
                                                </a>
                                                <a href="department.php?id=<?php echo $row['id'] ?>&del=delete" onClick="return confirm('Are you sure you want to delete?')">
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

    <?php include('includes/footer.php'); ?>

    <script src="assets/js/jquery-1.11.1.js"></script>

    <script src="assets/js/bootstrap.js"></script>
</body>

</html>
<?php } ?>
