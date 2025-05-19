<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
require_once 'config.php';

// Add/Edit Event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_date'], $_POST['event_title'])) {
    $event_date = trim($_POST['event_date']);
    $event_title = trim($_POST['event_title']);
    $event_id = isset($_POST['event_id']) && $_POST['event_id'] !== '' ? intval($_POST['event_id']) : null;

    if ($event_id) {
        $stmt = $conn->prepare("UPDATE events SET event_title = ?, event_date = ? WHERE id = ?");
        $stmt->bind_param("ssi", $event_title, $event_date, $event_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO events (event_title, event_date) VALUES (?, ?)");
        $stmt->bind_param("ss", $event_title, $event_date);
    }

    $stmt->execute();
    $stmt->close();
}

// Delete Event
if (isset($_GET['delete_event_id'])) {
    $event_id = intval($_GET['delete_event_id']);
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>PLURN – Calendar</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.css" rel="stylesheet">

<style>
    html, body {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #f4f6f9;
    }
    .wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .content {
        flex: 1;
        display: flex;
    }
    .header {
        height: 60px;
        background: #6f42c1;
        color: #fff;
        display: flex;
        align-items: center;
        padding: 0 20px;
        z-index: 1100;
    }
    .toggle-btn {
        background: none;
        border: 0;
        color: #fff;
        font-size: 24px;
        margin-right: 20px;
        cursor: pointer;
    }
    .header img.logo {
        height: 40px;
        margin-right: 10px;
    }
    .sidebar {
        width: 220px;
        background: #343a40;
        padding-top: 10px;
        color: #fff;
        transition: transform 0.3s ease;
        overflow-y: auto;
    }
    .sidebar a {
        display: block;
        color: #fff;
        padding: 12px 20px;
        text-decoration: none;
    }
    .sidebar a:hover {
        background: #495057;
    }
    .main-content {
        flex: 1;
        padding: 20px;
    }
    .footer {
        background: #6f42c1;
        color: #fff;
        text-align: center;
        padding: 15px 20px;
    }
    .calendar-box {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0,0,0,.1);
    }
    @media (max-width: 768px) {
        .sidebar {
            display: none;
        }
    }
</style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        <img src="icslogo.png" alt="ICS Logo" class="logo">
        <span class="home-title fw-bold">PLURN</span>

        <div class="dropdown ms-auto">
            <a class="btn btn-sm text-white dropdown-toggle" href="#" id="userDrop" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i> <span><?= htmlspecialchars($_SESSION['username']) ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDrop">
                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-id-badge me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Sidebar -->
      <div class="sidebar" id="sidebar">
            <a href="home.php"><i class="fas fa-home me-2"></i>Home</a>
            <a href="module.php"><i class="fas fa-book me-2"></i>Modules</a>
            <a href="quizzes.php"><i class="fas fa-pen me-2"></i>Quizzes</a>
            <a href="calendar.php"><i class="fas fa-calendar-alt me-2"></i>Calendar</a>
            <a href="history.php"><i class="fas fa-history me-2"></i>History</a>
            <a href="announcements.php"><i class="fas fa-bullhorn me-2"></i>Announcements</a>
        </div>


        <!-- Main -->
        <div class="main-content" id="main-content">
            <div class="calendar-box">
                <h4 class="mb-3">📅 My Calendar</h4>
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        &copy; <?= date('Y') ?> PLURN. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.style.display = (sidebar.style.display === 'none' || sidebar.style.display === '') ? 'block' : 'none';
}

$(document).ready(function () {
    $('#calendar').fullCalendar({
        editable: false,
        selectable: true,
        dayClick: function (date) {
            $('#modalTitle').text('Add Event');
            $('#event_id').val('');
            $('#modal_event_date').val(date.format());
            $('#modal_event_title').val('');
            $('#deleteBtn').hide();
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        },
        eventClick: function (event) {
            if (event.rendering === 'background') return;
            $('#modalTitle').text('Edit Event');
            $('#event_id').val(event.id);
            $('#modal_event_date').val(moment(event.start).format('YYYY-MM-DD'));
            $('#modal_event_title').val(event.title);
            $('#deleteBtn').show().off('click').on('click', function () {
                if (confirm('Delete this event?')) {
                    window.location.href = 'calendar.php?delete_event_id=' + event.id;
                }
            });
            new bootstrap.Modal(document.getElementById('eventModal')).show();
        },
        events: [
            <?php
            $res = mysqli_query($conn, "SELECT * FROM events ORDER BY event_date ASC");
            while ($row = mysqli_fetch_assoc($res)) {
                $id = (int)$row['id'];
                $title = htmlspecialchars($row['event_title'], ENT_QUOTES);
                $date = $row['event_date'];
                echo "{ id: $id, title: '$title', start: '$date' },";
            }
            $holidays = [
                '2025-04-09' => 'Araw ng Kagitingan',
                '2025-05-01' => 'Labor Day',
                '2025-06-12' => 'Independence Day',
                '2025-08-21' => 'Ninoy Aquino Day',
                '2025-11-30' => 'Bonifacio Day',
            ];
            foreach ($holidays as $date => $title) {
                echo "{
                    title: '$title',
                    start: '$date',
                    rendering: 'background',
                    backgroundColor: '#ff6f61',
                    textColor: '#fff'
                },";
            }
            ?>
        ]
    });
});
</script>

<!-- Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="modalTitle">Add Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="event_id" id="event_id">
                    <div class="mb-3">
                        <label for="modal_event_date">Date</label>
                        <input type="date" class="form-control" name="event_date" id="modal_event_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_event_title">Title</label>
                        <input type="text" class="form-control" name="event_title" id="modal_event_title" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteBtn" style="display:none;">Delete</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>
