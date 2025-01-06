<?php
session_start();
include('includes/config.php');

if (!isset($_GET['semester_id']) || !isset($_GET['student_id'])) {
    die("Invalid request.");
}

$semester_id = $_GET['semester_id'];
$student_id = mysqli_real_escape_string($bd, $_GET['student_id']);

$student_query = mysqli_query($bd, "SELECT * FROM students WHERE StudentRegno = '$student_id'");
$student = mysqli_fetch_assoc($student_query);

$semester_query = mysqli_query($bd, "SELECT semester FROM semester WHERE semester = '$semester_id'");
$semester = mysqli_fetch_assoc($semester_query);


$info_query = mysqli_query($bd, "SELECT * FROM studentsrecord WHERE enrollment_number = '$student_id'");

$courses_query = mysqli_query($bd, "
    SELECT c.courseCode, c.courseName, c.courseUnit 
    FROM courseenrolls ce
    JOIN course c ON ce.course = c.courseCode
    WHERE ce.studentRegno = '$student_id' AND ce.semester = '$semester_id'
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration Form</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <style>
    
.picture {
    width: 123px;
    height: 120px;
    background-color: rgb(199, 195, 195);
   
  }
  
  img{
    height: 100px;
    width: 100px;
  }

  .side{
    display: flex;
    align-items: start;
    justify-content: end;
  }

  .box{
    font-size: 14px;
    text-transform: lowercase;
    writing-mode: vertical-lr;
    transform: rotate(180deg);
    margin-top: 20px;
  }
  
  .head{
    align-items: center;
  }

  #fill{
    width: 180px;
    height: 25px;
    color: rgb(67, 64, 64);
    margin-left: 40px;
    border: 1px solid rgb(111, 109, 109);
    text-align: center;
  }
  p{
    width: 300px;
    height: 25px;
    color: rgb(67, 64, 64);
    
  }
  .form{
    display: flex;
    flex-direction: column;
    padding-top: 0;

  }
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.form-container {
  max-width: 1000px; 
  margin: 50px auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

 .form-group {
    display: flex;
    
    padding: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}

#contact, #name-student {
  width: 83%;
  padding: 5px;
  box-sizing: border-box;
}

.taken-courses, {
  width: 100%; 
  border-collapse: collapse;
  border: 2px solid black;
}

.taken-courses caption, .prerequired-courses caption {
  font-weight: bold;
  margin-bottom: 10px;
}

.taken-courses th, .prerequired-courses th, 
.taken-courses td, .prerequired-courses td{
    padding: 10px;
    border: 1px solid #ccc;
    text-align: left;
    
}

.taken-courses input, .prerequired-courses input,
.info-tables input {
    width: 100%; 
}

.courses-table {
  display: grid;
  grid-template-columns: 1fr 1fr; 
  width: 100%; 
}

.div {
  font-style: italic;
  width: 200px;
  height: 100px;
  border: 1px solid black;
  padding: 10px;
  margin: 10px;
}

.right {
  margin-left: auto; 
}

caption{
  padding:5px;
}

.heading {
  border: none; 
  border-collapse: collapse; 
}

.heading-elements {
  border: none; 
  padding: 8px; 
}

center{
  padding: 25px;
}

h5 {
margin: 0;
}
    </style>
</head>
<body onload="window.print()"> 
<div class="form-container">
        <div class="container">
            <table class="heading">
                <tbody>
                    <tr>
                        <td class="heading-elements">
                            <img src="Images/pkflogo.jpeg" alt="School logo">
                        </td>
                        <td class="heading-elements">
                            <h5>PKFokam Institute of Excellence</h5>
                            <h5>Dignity - Faith - Responsibility</h5>
                            <i style="font-size: 10px;">P.O.Box 11646 Yaounde, Cameroon / Tel : (237) 242.01.90.27 / <a href="https://pkfinstitute.com/" target="_blank">website: www.pkfinstitute.com</a></i>
                        </td>
                        <td class="heading-elements">
                            <div class="side">
                                <div class="box">Passport Size</div>
                                <div class="picture">
                                <img src="studentphoto/<?php echo htmlentities($student['studentPhoto']);?>" alt="Student Profile Picture" width="200" height="200">                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>   
            </table>
        </div>
        <form id="registration-form" >
            <h2>UNDERGRADUATE COURSE REGISTRATION FORM</h2>
            
            <center>
                <p><b>Semester:</b> <?php echo $semester_id; ?></p>
            </center>
            <?php
                
                while ($info = mysqli_fetch_assoc($info_query)) {
                ?>
            <table>
                <tbody id="info-tables">
                    <tr>
                        <td colspan="2">                
                        <p><b>Student Name:</b> <?php echo $info['first_name'] . " " . $info['last_name']; ?></p>
                        </td>
                    </tr>
                    <tr>  
                        <td colspan="2">
                            <p><b>Contact (tel & email):</b> <?php echo $info['phone_number'] . " " . $info['email']; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p><b>Date and Place of Birth:</b> <?php echo $info['date_of_birth']; ?></p>
                            <p><b>at</b> <?php echo $info['place_of_birth'] ; ?></p>
                        </td>
                        <td>
                            <p><b>Student Reg No:</b> <?php echo $student['StudentRegno']; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p><b>Program of Studies:</b> <?php echo $student['department']; ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
    
            <div class="courses-table">
                <table class="taken-courses">
                    <caption class="courses">COURSE TO BE TAKEN</caption>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>CODE AND TITLE</th>
                            <th>CREDITS</th>
                        </tr>
                    </thead>
                    <tbody id="courses-table">
                        <tr>
                        <?php
                        $cnt = 1;
                        $totalCredits = 0;
                        while ($course = mysqli_fetch_assoc($courses_query)) {
                            $totalCredits += $course['courseUnit'];
                            echo "<tr>
                                <td>{$cnt}</td>
                                <td>{$course['courseCode']}+ " " +{$course['courseName']}</td>
                                <td>{$course['courseUnit']}</td>
                            </tr>";
                            $cnt++;
                        }
                        ?>
                        </tr>
                        <tr>
                    <td colspan="3"><b>Total Credits</b></td>
                    <td><?php echo $totalCredits; ?></td>
                </tr>
                    </tbody>
                </table>
            </div>
            <?php   
                }
                ?>
            </form>
    </div>
        <button onclick="window.print()">Print Again</button> 
 
</body>
</html>
