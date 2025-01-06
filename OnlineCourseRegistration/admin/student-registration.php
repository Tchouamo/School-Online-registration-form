<?php
session_start();
include('includes/config.php');

if(strlen($_SESSION['alogin'])==0) {   
    header('location:index.php');
} else {
    if(isset($_POST['submit'])) {
        $studentname = $_POST['studentname'];
        $studentregno = $_POST['studentregno'];
        $password = md5($_POST['password']);
        $pincode = rand(100000, 999999);
        $level = $_POST['level'];

        $department = $_POST['department']; 
        $cgpa = 0.0; 
        $creationdate = date("Y-m-d H:i:s");
        $updationDate = date("Y-m-d H:i:s");

        $ret = mysqli_query($bd, "INSERT INTO students(studentName, StudentRegno, password, pincode, department, cgpa, level, status, creationdate, updationDate) 
            VALUES('$studentname', '$studentregno', '$password', '$pincode', '$department', '$cgpa', '$level', 'Active', '$creationdate', '$updationDate')");

        if($ret) {
            $_SESSION['msg'] = "Student Registered Successfully !!";
        } else {
            $_SESSION['msg'] = "Error: Student not Registered";
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
    <title>Admin | Student Registration</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />

    <style>
        #regno-suggestions {
            position: absolute;
            width: 100%; 
            max-height: 200px;
            overflow-y: auto;
            background-color: #fff;
            border: 1px solid #ddd;
            z-index: 1050; 
            border-radius: 4px;
        }

        #regno-suggestions li {
            padding: 8px;
            cursor: pointer;
        }

        #regno-suggestions li:hover {
            background-color: #f0f0f0;
        }

    </style>
</head>

<body>
<?php include('includes/header.php'); ?>

<?php if($_SESSION['alogin']!="") {
    include('includes/menubar.php');
} ?>

<div class="content-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Student Registration</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Student Registration
                    </div>
                    <font color="green" align="center"><?php echo htmlentities($_SESSION['msg']); ?><?php echo htmlentities($_SESSION['msg'] = ""); ?></font>

                    <div class="panel-body">
                        <form name="dept" method="post">
                            
                        <div class="form-group position-relative">
                            <label for="studentregno">Student Reg No</label>
                            <input type="text" class="form-control" id="studentregno" name="studentregno" 
                                placeholder="Enter or select Reg no" required onkeyup="fetchStudentRegNo()">
                            <ul id="regno-suggestions" class="list-group"></ul> 
                        </div>


                            <div class="form-group">
                                <label for="studentname">Student Name</label>
                                <input type="text" class="form-control" id="studentname" name="studentname" placeholder="Student Name" readonly required />
                            </div>

                            <div class="form-group">
                                <label for="level">Level</label>
                                <input type="text" class="form-control" id="level" name="level" placeholder="Level" readonly required />
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required />
                            </div>

                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" name="department" placeholder="Enter department" readonly required />
                            </div>
                            <button type="submit" name="submit" id="submit" class="btn btn-default">Submit</button>
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
<script>
function fetchStudentRegNo() {
    let regnoInput = document.getElementById("studentregno").value;
    let suggestionBox = document.getElementById("regno-suggestions");

    if (regnoInput.length > 2) {
        $.ajax({
            url: "fetch_regnos.php",
            type: "POST",
            data: { query: regnoInput },
            success: function(data) {
                suggestionBox.style.display = "block";
                suggestionBox.innerHTML = data;
            },
            error: function(xhr, status, error) {
                console.error("Error fetching suggestions: ", error);
                suggestionBox.style.display = "none";
            }
        });
    } else {
        suggestionBox.style.display = "none";
    }
}

function selectRegNo(regno) {
    document.getElementById("studentregno").value = regno;
    document.getElementById("regno-suggestions").style.display = "none";

    fetchStudentDetails(regno); 
}

function fetchStudentDetails(regno) {
    $.ajax({
        url: "fetch_student_details.php",
        type: "POST",
        data: { regno: regno },
        success: function(data) {
            let student = JSON.parse(data);
            document.getElementById("studentname").value = student.first_name + " " + student.last_name;
            document.getElementById("department").value = student.program;
            document.getElementById("level").value = student.level;

        },
        error: function(xhr, status, error) {
            console.error("Error fetching student details: ", error);
        }
    });
}


</script>
</body>
</html>
<?php } ?>
