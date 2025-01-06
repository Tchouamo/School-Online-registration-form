<?php
session_start();
include('includes/config.php');

if(strlen($_SESSION['alogin']) == 0) {   
    header('location:index.php');
    exit();
}

if (isset($_GET['id'])) {
    $levelId = intval($_GET['id']);
    $query = mysqli_query($bd, "SELECT * FROM level WHERE id = '$levelId'");
    $levelData = mysqli_fetch_assoc($query);
    if (!$levelData) {
        $_SESSION['msg'] = "Invalid Level ID.";
        header('location:level.php');
        exit();
    }
}

if (isset($_POST['update'])) {
    $levelId = intval($_POST['level_id']);
    $levelName = $_POST['level_name'];

    $updateQuery = mysqli_query($bd, "UPDATE level SET level = '$levelName', updationDate = NOW() WHERE id = '$levelId'");

    if ($updateQuery) {
        $_SESSION['msg'] = "Level Updated Successfully!";
        header('location:level.php');
        exit();
    } else {
        
        $_SESSION['msg'] = "Error: Level not updated.";
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>Admin | Edit Level</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
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
                <h1 class="page-head-line">Edit Level</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Edit Level
                    </div>
                    <div class="panel-body">
                        <form method="post">
                            <input type="hidden" name="level_id" value="<?php echo htmlentities($levelData['id']); ?>">
                            <div class="form-group">
                                <label for="level_name">Level Name</label>
                                <input type="text" class="form-control" id="level_name" name="level_name" 
                                    value="<?php echo htmlentities($levelData['level']); ?>" required>
                            </div>
                            <button type="submit" name="update" class="btn btn-primary">Update</button>
                            <a href="level.php" class="btn btn-default">Cancel</a>
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
</body>
</html>
