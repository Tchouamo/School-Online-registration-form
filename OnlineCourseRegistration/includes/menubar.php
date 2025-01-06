<?php
session_start();
include('config.php');

$unreadCount = 0;

if (isset($_SESSION['login'])) {
    $studentRegno = $_SESSION['login'];
    $queryUnread = "SELECT COUNT(*) AS unread_count FROM notification WHERE studentRegno = '$studentRegno' AND is_read = 0";
    $result = mysqli_query($bd, $queryUnread);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $unreadCount = $row['unread_count'];
    }
}
?>

<section class="menu-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="navbar-collapse collapse">
                    <ul id="menu-top" class="nav navbar-nav navbar-right">
                        <li><a href="my-profile.php">My Profile</a></li>
                        <li><a href="pincode-verification.php">Enroll for Course </a></li>
                        <li><a href="enroll-history.php">Enroll History</a></li>
                        <li><a href="timetable.php">Timetable</a></li>
                        <li><a href="change-password.php">Change Password</a></li>
                        <li>
                            <a href="notification.php">
                                Messages
                                <?php if ($unreadCount > 0): ?>
                                    <span class="notification-count"><?php echo $unreadCount; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .notification-count {
        coslor: white;
        background-color: red;
        border-radius: 50%;
        padding: 3px 8px;
        font-size: 12px;
        margin-left: 5px;
        font-weight: bold;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateUnreadCount() {
        $.ajax({
            url: 'includes/get_unread_count.php',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                console.log("Unread count fetched:", data);
                if (data.unread_count !== undefined) {
                    const notificationCount = document.querySelector('.notification-count');

                    if (data.unread_count > 0) {
                        if (!notificationCount) {
                            const newCount = document.createElement('span');
                            newCount.className = 'notification-count';
                            newCount.textContent = data.unread_count;
                            document.querySelector('a[href="notification.php"]').appendChild(newCount);
                        } else {
                            notificationCount.textContent = data.unread_count;
                        }
                    } else if (notificationCount) {
                        notificationCount.remove();
                    }
                }
            },
            error: function (xhr, status, error) {
                console.error('Error fetching unread count:', error);
            }
        });
    }

    // Poll every 10 seconds to update the unread count dynamically
    setInterval(updateUnreadCount, 10000);

    // Initial call when the page loads
    $(document).ready(function () {
        updateUnreadCount();
    });
</script>

