<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit; 
} else {
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $message = mysqli_real_escape_string($bd, $_POST['message']);
        $semester_id = isset($_POST['semester_id']) && $_POST['semester_id'] != "" ? mysqli_real_escape_string($bd, $_POST['semester_id']) : null;
        $level = isset($_POST['level_id']) && $_POST['level_id'] != "" ? mysqli_real_escape_string($bd, $_POST['level_id']) : null;
        $notificationAudience = $_POST['notificationAudience'];
        $notificationType = $_POST['notificationType'];
    
        $query = ""; 
    
        if ($notificationAudience === "specific") {
            $studentRegno = isset($_POST['studentRegno']) ? mysqli_real_escape_string($bd, $_POST['studentRegno']) : null;
            if ($studentRegno) {
                $query = "INSERT INTO notification (title, message, type, semester_id, studentRegno, recipient_type) 
                          VALUES ('$title', '$message', '$notificationType', '$semester_id', '$studentRegno', 'specific')";
            } else {
                $_SESSION['msg'] = "Please select a valid student registration number.";
                header('location: notification.php');
                exit();
            }
        } elseif ($notificationAudience === "department") {
            $department_id = isset($_POST['department_id']) ? mysqli_real_escape_string($bd, $_POST['department_id']) : null;
            if ($department_id) {
                $query = "INSERT INTO notification (title, message, type, semester_id, department_id, recipient_type) 
                          VALUES ('$title', '$message', '$notificationType', '$semester_id', '$department_id', 'department')";
            } else {
                $_SESSION['msg'] = "Please select a valid department.";
                header('location: notification.php');
                exit();
            }
        } elseif ($notificationAudience === "all") {
            $query = "INSERT INTO notification (title, message, type, semester_id, is_for_all, recipient_type) 
                      VALUES ('$title', '$message', '$notificationType', '$semester_id', 'yes', 'all')";
        }elseif ($notificationAudience === "level") {
            $query = "INSERT INTO notification (title, message, type, semester_id, level, recipient_type) 
                      VALUES ('$title', '$message', '$notificationType', '$semester_id', '$level', 'level')";
        }
    
        if (!empty($query) && mysqli_query($bd, $query)) {
            $_SESSION['msg'] = "Notification Created Successfully!";
        } else {
            $_SESSION['msg'] = "Error: Notification not created. " . mysqli_error($bd);
        }
    
        header("Location: notification.php");
        exit();
    }
    
    

    if (isset($_GET['del'])) {
        $notificationId = mysqli_real_escape_string($bd, $_GET['id']);
        mysqli_query($bd, "DELETE FROM notification WHERE id = '$notificationId'");
        $_SESSION['delmsg'] = "Notification deleted !!";
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
    <title>Admin | Notifications</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
    <style>
      
        .table td, .table th {
            vertical-align: middle; 
            text-align: center; 
        }
    #studentSuggestions {
        position: absolute;
        z-index: 1000;
        background-color: white;
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        width: 100%;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    #studentSuggestions li {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    #studentSuggestions li:hover {
        background-color: #f0f0f0;
    }
</style>

</head>

<body>
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
                <h1 class="page-head-line">Send Notifications</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Create Notification
                    </div>
                    <font color="green" align="center"><?php echo htmlentities($_SESSION['msg']); ?><?php echo htmlentities($_SESSION['msg'] = ""); ?></font>

                    <div class="panel-body">
                        <form name="notification" method="post">
                        <?php 
                            $sql=mysqli_query($bd, "select semester from semester where status ='ongoing' ");
                            while($row=mysqli_fetch_array($sql))
                            {
                            ?>
                        <div class="form-group">
                            <label for="semester_id">Semester  </label>
                            <input type="text" class="form-control" id="semester_id" name="semester_id" readonly value="<?php echo htmlentities($row['semester']);?>" required  />
                        </div>
                        <?php } ?>
                        <div class="form-group">
                            <label for="notificationType">Notification Type</label>
                            <select class="form-control" id="notificationType" name="notificationType" required onchange="toggleFields(this.value)">
                                <option value="" disabled selected>Select Notification Type</option>
                                <option value="INFORMATION">Information</option>
                                <option value="REMINDER">Reminder</option>
                                <option value="EMERGENCY">Emergency</option>
                            </select>
                        </div>
                        <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" placeholder="Enter your title" required>
                            </div>
                            <div class="form-group">
    <label for="notificationAudience">Notification Audience</label>
    <select class="form-control" id="notificationAudience" name="notificationAudience" onchange="toggleFields(this.value)" >
        <option value="" disabled selected>Select Notification Audience</option>
        <option value="specific">Specific Student</option>
        <option value="department">All Students in a Department</option>
        <option value="level">All Students in a Level</option>

        <option value="all">All Students</option>
    </select>
</div>

<div class="form-group" id="specificStudentField" style="display: none;">
    <label for="studentRegno">Specific Student Regno</label>
    <input type="text" class="form-control" id="studentRegno" name="studentRegno" placeholder="Enter student registration number" onkeyup="fetchStudents(this.value)">
    <ul id="studentSuggestions" class="list-group" style="display: none;"></ul>
</div>

<div class="form-group" id="departmentField" style="display: none;">
    <label for="department_id">Select Department</label>
    <select class="form-control" id="department_id" name="department_id">
        <option value="" disabled selected>Select Department</option>
        <?php
        $dept_sql = mysqli_query($bd, "SELECT * FROM department");
        while ($dept_row = mysqli_fetch_array($dept_sql)) {
            echo '<option value="' . $dept_row['department'] . '">' . htmlentities($dept_row['department']) . '</option>';
        }
        ?>
    </select>
</div>
<div class="form-group" id="levelField" style="display: none;">
    <label for="level_id">Select Level</label>
    <select class="form-control" id="level_id" name="level_id">
        <option value="" disabled selected>Select Level</option>
        <?php
        $lev_sql = mysqli_query($bd, "SELECT * FROM level");
        while ($lev_row = mysqli_fetch_array($lev_sql)) {
            echo '<option value="' . $lev_row['level'] . '">' . htmlentities($lev_row['level']) . '</option>';
        }
        ?>
    </select>
</div>
                 <div class="form-group">
                                <label for="message">Message</label>
                                <textarea class="form-control" id="message" name="message" placeholder="Enter your notification message" required></textarea>
                            </div>
                            <button type="submit" name="submit" class="btn btn-default">Send Notification</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <font color="red" align="center"><?php echo htmlentities($_SESSION['delmsg']); ?><?php echo htmlentities($_SESSION['delmsg'] = ""); ?></font>
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Manage Notifications
                </div>
                <div class="panel-body">
                    <div class="table-responsive table-bordered">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Semester</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Department</th>
                                    <th>Level</th>
                                    <th>Target Student</th>
                                    <th>All students</th>
                                    <th>Creation Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
<?php
$sql = mysqli_query($bd, "SELECT * FROM notification");
$cnt = 1;
while($row = mysqli_fetch_array($sql)) {
?>
<tr>
    <td><?php echo $cnt; ?></td>
    <td><?php echo htmlentities($row['semester_id']); ?></td>
    <td><?php echo htmlentities($row['type']); ?></td>
    <td><?php echo htmlentities($row['title']); ?></td>
    <td><?php echo htmlentities($row['message']); ?></td>
    <td><?php echo htmlentities($row['department_id']); ?></td>
    <td><?php echo htmlentities($row['level']); ?></td>
    <td><?php echo htmlentities($row['studentRegno']); ?></td>
    <td><?php echo htmlentities($row['is_for_all']); ?></td>
    <td><?php echo htmlentities($row['creationDate']); ?></td>
    <td>
        <a href="edit-course.php?id=<?php echo $row['id']?>">
            <button class="btn btn-primary"><i class="fa fa-edit"></i> Edit</button>
        </a>
        <a href="notification.php?id=<?php echo $row['id']?>&del=delete" onClick="return confirm('Are you sure you want to delete?')">
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

<?php include('includes/footer.php'); ?>
<script src="assets/js/jquery-1.11.1.js"></script>
<script src="assets/js/bootstrap.js"></script>


<script>

function fetchStudents(query) {
    const suggestions = document.getElementById("studentSuggestions");

    if (query.length > 2) { 
        $.ajax({
            url: "fetch_students.php",
            method: "POST",
            data: { query: query },
            success: function(data) {
                suggestions.style.display = "block";
                suggestions.innerHTML = data; 
            },
            error: function(xhr, status, error) {
                console.error("Error fetching students: ", error); 
                suggestions.style.display = "none";
            }
        });
    } else {
        suggestions.style.display = "none"; 
    }
}

function selectStudent(regno) {
    const inputField = document.getElementById("studentRegno");
    const suggestions = document.getElementById("studentSuggestions");

    inputField.value = regno; 
    suggestions.style.display = "none"; 
}


function toggleFields(value) {
        const specificStudentField = document.getElementById("specificStudentField");
        const departmentField = document.getElementById("departmentField");
        const levelField = document.getElementById("levelField");

        if (value === "specific") {
            specificStudentField.style.display = "block";
            levelField.style.display = "none";
            departmentField.style.display = "none";
        } else if (value === "department") {
            specificStudentField.style.display = "none";
            departmentField.style.display = "block";
        } else if (value === "level") {
            specificStudentField.style.display = "none";
            departmentField.style.display = "none";
            levelField.style.display = "block";
        } else if (value === "all") {
            specificStudentField.style.display = "none";
            levelField.style.display = "none";
            departmentField.style.display = "none";
        } else {
            specificStudentField.style.display = "none";
            departmentField.style.display = "none";
        }
    }

</script>

</body>
</html>
