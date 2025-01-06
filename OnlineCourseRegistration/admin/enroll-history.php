<?php 
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
} else {
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
        .form-group {
            margin-bottom: 20px;
        }
    </style>
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
                <h1 class="page-head-line">Enroll History</h1>
            </div>
        </div>

        <!-- Filter Section: Select Semester, Department, and Level -->
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="semester">Select Semester</label>
                    <select class="form-control" name="semester" id="semester" onchange="loadEnrollments()">
                        <option value="" disabled>Select a semester</option>
                        <?php
                        // Fetch the ongoing semester (if exists)
                        $ongoingSemesterQuery = mysqli_query($bd, "SELECT id, semester FROM semester WHERE status='ongoing' LIMIT 1");
                        $ongoingSemester = mysqli_fetch_array($ongoingSemesterQuery);
                        $ongoingSemesterId = $ongoingSemester ? $ongoingSemester['id'] : null;

                        // Fetch all semesters
                        $allSemestersQuery = mysqli_query($bd, "SELECT id, semester FROM semester ORDER BY id");
                        while ($semester = mysqli_fetch_array($allSemestersQuery)) {
                            $selected = ($semester['id'] == $ongoingSemesterId) ? "selected" : "";
                            echo "<option value='" . htmlentities($semester['id']) . "' $selected>" . htmlentities($semester['semester']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Department Filter -->
<div class="form-group">
    <label for="department">Select Department</label>
    <select class="form-control" name="department" id="department" onchange="loadEnrollments()">
        <option value="" selected>All Departments</option>
        <!-- Department options will be populated dynamically -->
    </select>
</div>

<!-- Level Filter -->
<div class="form-group">
    <label for="level">Select Level</label>
    <select class="form-control" name="level" id="level" onchange="loadEnrollments()">
    <option value="" selected>All Levels</option>
    <!-- Level options will be populated dynamically -->
    </select>
</div>

        <!-- Table to Display Enrollments -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Enrollments for <span id="semesterTitle">All Semesters</span>
                    </div>

                    <div class="panel-body">
                        <div class="table-responsive table-bordered">
                            <table id="enrollmentTable" class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Reg no</th>
                                        <th>Student Name</th>
                                        <th>Department</th>
                                        
                                        <th>Level</th>
                                        <th>Semester</th>
                                        <th>Enrollment Date</th>
                                    </tr>
                                </thead>
                                <tbody id="enrollmentTableBody">
                                    <!-- Enrollment Data will be inserted here -->
                                </tbody>
                            </table>
                        </div>

                        <button id="exportButton" class="btn btn-success" onclick="exportToExcel()">Export to Excel</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include('includes/footer.php');?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

<script src="assets/js/jquery-1.11.1.js"></script>
<script src="assets/js/bootstrap.js"></script>

<script>
function loadEnrollments() {
    const semesterId = document.getElementById('semester').value;
    const departmentId = document.getElementById('department').value;
    const level = document.getElementById('level').value;
    const semesterTitle = document.getElementById('semesterTitle');
    const enrollmentTableBody = document.getElementById('enrollmentTableBody');
    
    const selectedSemester = document.getElementById('semester').selectedOptions[0]?.text;
    semesterTitle.innerText = selectedSemester || "All Semesters";

    const filters = {
        semester_id: semesterId,
        department_id: departmentId,
        level: level
    };

    $.ajax({
        url: 'get_enrollments.php',
        type: 'GET',
        data: filters,
        success: function(response) {
            enrollmentTableBody.innerHTML = response;
        }
    });

    loadDepartmentsAndLevels(semesterId);
}

function loadDepartmentsAndLevels(semesterId) {
    $.ajax({
        url: 'get_departments_and_levels.php',
        type: 'GET',
        data: { semester_id: semesterId },
        dataType: 'json',
        success: function (response) {
            const departments = response.departments || [];
            const levels = response.levels || [];

            const departmentSelect = document.getElementById('department');
            const levelSelect = document.getElementById('level');

            departmentSelect.innerHTML = '<option value="" selected>All Departments</option>';
            levelSelect.innerHTML = '<option value="" selected>All Levels</option>';

            departments.forEach(department => {
                const option = document.createElement('option');
                option.value = department.id;
                option.textContent = department.name;
                departmentSelect.appendChild(option);
            });

            levels.forEach(level => {
                const option = document.createElement('option');
                option.value = level.id;
                option.textContent = level.name;
                levelSelect.appendChild(option);
            });

            retrieveFilters();
        },
        error: function (xhr, status, error) {
            console.error("Error loading departments and levels:", error);
        }
    });
}

function storeFilters() {
    const departmentId = document.getElementById('department').value;
    const levelId = document.getElementById('level').value;

    localStorage.setItem('selectedDepartment', departmentId);
    localStorage.setItem('selectedLevel', levelId);
}

function retrieveFilters() {
    const departmentId = localStorage.getItem('selectedDepartment');
    const levelId = localStorage.getItem('selectedLevel');

    const departmentSelect = document.getElementById('department');
    const levelSelect = document.getElementById('level');

    if (departmentId) {
        const departmentOption = Array.from(departmentSelect.options).find(option => option.value === departmentId);
        if (departmentOption) {
            departmentOption.selected = true;
        }
    }

    if (levelId) {
        const levelOption = Array.from(levelSelect.options).find(option => option.value === levelId);
        if (levelOption) {
            levelOption.selected = true;
        }
    }
}

document.getElementById('department').addEventListener('change', () => {
    storeFilters();
    loadEnrollments(); 
});

document.getElementById('level').addEventListener('change', () => {
    storeFilters();
    loadEnrollments(); 
});

function exportToExcel() {
    const table = document.getElementById("enrollmentTable");
    const worksheet = XLSX.utils.table_to_book(table, { sheet: "Enrollments" });

    const columnWidths = [];
    const rows = worksheet.Sheets.Enrollments['!rows'];

    for (let col = 0; col < table.rows[0].cells.length; col++) {
        let maxWidth = 0;
        for (let row = 0; row < table.rows.length; row++) {
            const cellValue = table.rows[row].cells[col].innerText.trim();
            maxWidth = Math.max(maxWidth, cellValue.length);
        }
        columnWidths.push(maxWidth + 5);
    }

    worksheet.Sheets.Enrollments['!cols'] = columnWidths.map(width => ({ wpx: width * 10 }));

    const dateColumnIndex = 6;
    const range = worksheet.Sheets.Enrollments['!ref'];
    const dateCells = getColumnData(worksheet.Sheets.Enrollments, dateColumnIndex, range);

    dateCells.forEach(cell => {
        if (cell && !isNaN(Date.parse(cell))) {
            const date = new Date(cell);
            worksheet.Sheets.Enrollments[cell] = { v: date, t: 'd', z: 'yyyy-mm-dd' };
        }
    });

    XLSX.writeFile(worksheet, "Enrollments.xlsx");
}

function getColumnData(sheet, columnIndex, range) {
    const startCell = range.split(":")[0];
    const endCell = range.split(":")[1];
    const column = startCell[0];

    let columnData = [];
    for (let row = parseInt(startCell.slice(1)); row <= parseInt(endCell.slice(1)); row++) {
        const cellAddress = column + row;
        const cellValue = sheet[cellAddress] ? sheet[cellAddress].v : null;
        columnData.push(cellValue);
    }
    return columnData;
}

$(document).ready(function() {
    loadEnrollments();
    retrieveFilters(); 
});
</script>

</body>
</html>

<?php } ?>
