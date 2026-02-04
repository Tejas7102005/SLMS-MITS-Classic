<?php
session_start();
include 'db_connect.php';
include 'includes/session_check.php';

// Check if user is faculty
if ($_SESSION['role'] !== 'faculty') {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";

// Handle student registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $rollNumber = mysqli_real_escape_string($conn, $_POST['rollNumber']);

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "User with this email already exists!";
    } else {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new student
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, department, rollNumber) VALUES (?, ?, ?, 'student', ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $department, $rollNumber);

        if ($stmt->execute()) {
            $message = "Student added successfully!";
        } else {
            $error = "Error adding student: " . $conn->error;
        }
    }
    $stmt->close();
}

// Handle leave approval/rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_leave'])) {
    $leaveId = intval($_POST['leave_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $facultyComment = mysqli_real_escape_string($conn, $_POST['facultyComment']);

    $stmt = $conn->prepare("UPDATE leaves SET status = ?, facultyComment = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $facultyComment, $leaveId);

    if ($stmt->execute()) {
        $message = "Leave request updated successfully!";
    } else {
        $error = "Error updating leave: " . $conn->error;
    }
    $stmt->close();
}

// Fetch pending leave requests with student details
$pendingLeaves = $conn->query("
    SELECT l.*, u.name as student_name, u.email as student_email, u.department as student_dept, u.rollNumber
    FROM leaves l
    JOIN users u ON l.user_id = u.id
    WHERE l.status = 'Pending'
    ORDER BY l.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard - SLMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; }
        .dashboard-container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .card { border: none; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card-header { background-color: #198754; color: white; font-weight: 600; border-radius: 10px 10px 0 0 !important; }
        .table { margin-bottom: 0; }
        .badge { padding: 0.5em 0.8em; }
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

    <!-- Student Registration Section -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-person-plus-fill"></i> Register New Student
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control" placeholder="Name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="rollNumber" class="form-control" placeholder="Roll Number" required>
                    </div>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="department" class="form-control" placeholder="Department" required>
                    </div>
                    <div class="col-md-2">
                        <input type="password" name="password" class="form-control" placeholder="Password" required minlength="6">
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" name="add_student" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Register Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Leave Requests Section -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-clipboard-check"></i> Pending Leave Requests
        </div>
        <div class="card-body">
            <?php if($pendingLeaves->num_rows == 0): ?>
                <p class="text-muted">No pending leave requests.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Department</th>
                                <th>Reason</th>
                                <th>Dates</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($leave = $pendingLeaves->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($leave['student_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($leave['student_email']); ?></small><br>
                                        <small class="text-muted">Roll: <?php echo htmlspecialchars($leave['rollNumber']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($leave['student_dept']); ?></td>
                                    <td><?php echo htmlspecialchars($leave['reason']); ?></td>
                                    <td>
                                        <?php echo date('M d, Y', strtotime($leave['startDate'])); ?><br>
                                        <small class="text-muted">to</small><br>
                                        <?php echo date('M d, Y', strtotime($leave['endDate'])); ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="updateLeave(<?php echo $leave['id']; ?>, 'Approved')">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="updateLeave(<?php echo $leave['id']; ?>, 'Rejected')">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
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

<!-- Hidden form for leave updates -->
<form id="leaveUpdateForm" method="POST" action="" style="display: none;">
    <input type="hidden" name="leave_id" id="leave_id">
    <input type="hidden" name="status" id="status">
    <input type="hidden" name="facultyComment" id="facultyComment">
    <input type="hidden" name="update_leave" value="1">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateLeave(leaveId, status) {
    const comment = prompt(`Enter reason for ${status} (Optional):`);
    if (comment !== null) { // User didn't cancel
        document.getElementById('leave_id').value = leaveId;
        document.getElementById('status').value = status;
        document.getElementById('facultyComment').value = comment;
        document.getElementById('leaveUpdateForm').submit();
    }
}
</script>
</body>
</html>
