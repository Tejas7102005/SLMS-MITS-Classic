<?php
session_start();
include 'db_connect.php';
include 'includes/session_check.php';

// Check if user is student
if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";

// Handle leave application
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);
    $startDate = mysqli_real_escape_string($conn, $_POST['startDate']);
    $endDate = mysqli_real_escape_string($conn, $_POST['endDate']);
    $userId = $_SESSION['user_id'];

    // Validate dates
    if (strtotime($endDate) < strtotime($startDate)) {
        $error = "End date cannot be before start date!";
    } else {
        $stmt = $conn->prepare("INSERT INTO leaves (user_id, reason, startDate, endDate, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("isss", $userId, $reason, $startDate, $endDate);

        if ($stmt->execute()) {
            $message = "Leave application submitted successfully!";
        } else {
            $error = "Error submitting leave: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch student's leave history
$userId = $_SESSION['user_id'];
$leaves = $conn->query("SELECT * FROM leaves WHERE user_id = $userId ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - SLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .dashboard-container { max-width: 1000px; margin: 0 auto; padding: 2rem; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card-header { background-color: #0d6efd; color: white; font-weight: 600; border-radius: 10px 10px 0 0 !important; }
        .badge { padding: 0.5em 0.8em; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-approved { background-color: #198754; color: #fff; }
        .status-rejected { background-color: #dc3545; color: #fff; }
    </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="dashboard-container">
    <?php if($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Apply for Leave Section -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-calendar-plus"></i> Apply for Leave
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea name="reason" class="form-control" rows="3" required placeholder="Enter reason for leave..."></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="startDate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="endDate" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Submit Leave Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leave History Section -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-clock-history"></i> My Leave History
        </div>
        <div class="card-body">
            <?php if($leaves->num_rows == 0): ?>
                <p class="text-muted">No leave applications yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Reason</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Faculty Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($leave = $leaves->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($leave['startDate'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($leave['endDate'])); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-pending';
                                        if ($leave['status'] == 'Approved') $statusClass = 'status-approved';
                                        if ($leave['status'] == 'Rejected') $statusClass = 'status-rejected';
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>">
                                            <?php echo $leave['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        echo $leave['facultyComment'] 
                                            ? htmlspecialchars($leave['facultyComment']) 
                                            : '<span class="text-muted">-</span>'; 
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
