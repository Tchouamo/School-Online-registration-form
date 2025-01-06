<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['login']) == 0) {
    header('location:index.php');
    exit();
}

$studentRegno = $_SESSION['login'];
$query = "SELECT department, level FROM students WHERE StudentRegno = '$studentRegno'";
$result = mysqli_query($bd, $query);
if ($row = mysqli_fetch_assoc($result)) {
    $_SESSION['department_id'] = $row['department'];
    $_SESSION['level_id'] = $row['level'];
}

$departmentId = $_SESSION['department_id'];
$levelId = $_SESSION['level_id'];

$notifications = [];

// Fetch notifications specific to the student
$querySpecific = "SELECT * FROM notification WHERE studentRegno = '$studentRegno' ORDER BY creationDate DESC";
// Fetch notifications for the department
$queryDepartment = "SELECT * FROM notification WHERE department_id = '$departmentId' AND recipient_type = 'department' ORDER BY creationDate DESC";
$queryLevel = "SELECT * FROM notification WHERE level = '$levelId' ORDER BY creationDate DESC";
// Fetch notifications for all students
$queryAll = "SELECT * FROM notification WHERE recipient_type = 'all' ORDER BY creationDate DESC";

$resultSpecific = mysqli_query($bd, $querySpecific);
$resultDepartment = mysqli_query($bd, $queryDepartment);
$resultLevel = mysqli_query($bd, $queryLevel);

$resultAll = mysqli_query($bd, $queryAll);

while ($row = mysqli_fetch_assoc($resultSpecific)) {
    $notifications[] = $row;
}
while ($row = mysqli_fetch_assoc($resultDepartment)) {
    $notifications[] = $row;
}

while ($row = mysqli_fetch_assoc($resultLevel)) {
    $notifications[] = $row;
}

while ($row = mysqli_fetch_assoc($resultAll)) {
    $notifications[] = $row;
}

usort($notifications, function ($a, $b) {
    return strtotime($b['creationDate']) - strtotime($a['creationDate']);
});
?>


<!DOCTYPE html>
<html>
<head>
    <title>Student Notifications</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet" />
   
   <style>
.notification {
    padding: 15px;
    margin: 10px 0;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-family: Arial, sans-serif;
}

.notification.information {
    background-color: #e7f3ff;
    border-left: 5px solid #007bff; 
}

.notification.reminder {
    background-color: #fff3cd;
    border-left: 5px solid #f0ad4e; 
}

.notification.emergency {
    background-color: #f8d7da; 
    border-left: 5px solid #dc3545;
}

.notification.unread {
    border-left-width: 8px;
    box-shadow: 0px 0px 15px rgba(0, 123, 255, 0.1); 
}

.notification.read {
    opacity: 0.6; 
}

.notification .mark-as-read {
    background-color: #007bff;
    color: white;
    padding: 5px 15px;
    font-size: 12px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 10px;
}

.notification .mark-as-read:hover {
    background-color: #0056b3;
}
    </style>
</head>
<body>
<?php include('includes/header.php'); ?>
    
    <?php if ($_SESSION['login'] != "") {
        include('includes/menubar.php');
    } ?>

    <div class="content-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line">Notifications</h1>
                </div>
            </div>
            <div class="form-group">
                <label for="filterType">Filter by Notification Type:</label>
                <select id="filterType" class="form-control" onchange="filterNotifications()">
                    <option value="all">All</option>
                    <option value="information">Information</option>
                    <option value="reminder">Reminder</option>
                    <option value="emergency">Emergency</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </select>
            </div>

            <?php foreach ($notifications as $notification) : ?>
                <div class="notification 
                    <?php echo strtolower($notification['type']); ?> 
                    <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                    data-id="<?php echo $notification['id']; ?>" 
                    data-type="<?php echo strtolower($notification['type']); ?>">
                    <h4><?php echo htmlentities($notification['title']); ?></h4>
                    <p><?php echo htmlentities($notification['message']); ?></p>
                    <small>
                        <strong>Type:</strong> <?php echo htmlentities($notification['type']); ?> |
                        <strong>Sent On:</strong> <?php echo htmlentities($notification['creationDate']); ?>
                    </small>
                    <?php if (!$notification['is_read']) : ?>
                        <button class="mark-as-read btn btn-primary btn-sm">Mark as Read</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include('includes/footer.php');?>

    <script src="assets/js/jquery-1.11.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
    <script src="includes/menubar.php"></script>

    <script>
        $(document).on('click', '.mark-as-read', function () {
            const notificationDiv = $(this).closest('.notification');
            const notificationId = notificationDiv.data('id');

            $.ajax({
                url: 'mark_notification_read.php',
                method: 'POST',
                data: { id: notificationId },
                success: function (response) {
                    if (response === 'success') {
                        notificationDiv.removeClass('unread').addClass('read');
                        notificationDiv.find('.mark-as-read').remove();
                        updateUnreadCount();
                    } else {
                        alert('Failed to mark as read. Please try again.');
                    }
                }
            });
        });

        function filterNotifications() {
    const filterType = document.getElementById("filterType").value.toLowerCase();
    const notifications = document.querySelectorAll(".notification");

    notifications.forEach(notification => {
        const type = notification.getAttribute("data-type").toLowerCase();
        const isRead = notification.classList.contains("read");
        const isUnread = notification.classList.contains("unread");

        if (
            filterType === "all" || // Show all notifications
            (filterType === "information" && type === "information") ||
            (filterType === "reminder" && type === "reminder") ||
            (filterType === "emergency" && type === "emergency") ||
            (filterType === "unread" && isUnread) || // Show unread notifications
            (filterType === "read" && isRead)        // Show read notifications
        ) {
            notification.style.display = "block";
        } else {
            notification.style.display = "none";
        }
    });
}

    </script>
</body>
</html>
