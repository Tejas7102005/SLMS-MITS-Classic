<?php
session_start();
include 'includes/db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['department'] = $row['department'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($row['role'] == 'faculty') {
                header("Location: faculty_dashboard.php");
            } else {
                header("Location: student_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid Email or Password";
        }
    } else {
        $error = "Invalid Email or Password";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – MITS SLMS</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* ── LEFT PANEL ── */
        .left-panel {
            display: none; /* hidden on small screens */
            width: 50%;
            background: url('assets/campus.jpg') center center / cover no-repeat;
            position: relative;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            .left-panel { display: flex; }
        }

        /* dark red overlay */
        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(139, 0, 0, 0.75);
            mix-blend-mode: multiply;
        }

        /* gradient from bottom */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.70) 0%, rgba(0,0,0,0.10) 100%);
        }

        .left-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 3rem;
        }

        /* College logo + name — top */
        .college-branding {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .college-branding img {
            height: 64px;
            width: auto;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));
        }

        .college-branding .college-text p:first-child {
            color: rgba(255,255,255,0.85);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .college-branding .college-text p:last-child {
            color: rgba(255,255,255,0.95);
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Welcome text — bottom */
        .welcome-text {
            margin-top: auto;
            color: #fff;
        }

        .welcome-text h1 {
            font-size: 3rem;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .welcome-text p {
            font-size: 1rem;
            opacity: 0.88;
            max-width: 380px;
        }

        /* ── RIGHT PANEL ── */
        .right-panel {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            padding: 2rem;
        }

        @media (min-width: 768px) {
            .right-panel { width: 50%; }
        }

        .form-box {
            width: 100%;
            max-width: 420px;
        }

        .form-box h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #111;
            text-align: center;
            margin-bottom: 0.4rem;
        }

        .form-box .subtitle {
            font-size: 0.85rem;
            color: #666;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-box .error-msg {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            padding: 0.7rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.2rem;
            text-align: center;
        }

        .form-box input {
            display: block;
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 0.9rem;
            color: #111;
            border: 1px solid #d1d5db;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }

        .form-box input:first-of-type {
            border-radius: 6px 6px 0 0;
        }

        .form-box input:last-of-type {
            border-radius: 0 0 6px 6px;
            border-top: none;
        }

        .form-box input:focus {
            border-color: #8b0000;
            box-shadow: 0 0 0 3px rgba(139,0,0,0.12);
            z-index: 1;
            position: relative;
        }

        .form-box input::placeholder { color: #9ca3af; }

        .form-box button {
            display: block;
            width: 100%;
            margin-top: 1.25rem;
            padding: 0.85rem;
            background: #8b0000;
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 12px rgba(139,0,0,0.35);
        }

        .form-box button:hover { background: #6b0000; }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
        }

        .footer-text p {
            font-size: 0.72rem;
            color: #9ca3af;
            line-height: 1.6;
        }

        .footer-text span {
            font-weight: 600;
            color: #6b7280;
        }
    </style>
</head>
<body>

<!-- LEFT: Campus image + overlay -->
<div class="left-panel">
    <div class="left-content">

        <!-- College logo + name at top -->
        <div class="college-branding">
            <img src="assets/mits_logo.png" alt="MITS Logo">
            <div class="college-text">
                <p>Madhav Institute of Technology &amp; Science</p>
                <p>Gwalior, Madhya Pradesh</p>
            </div>
        </div>

        <!-- Welcome text at bottom -->
        <div class="welcome-text">
            <h1>Welcome to<br>MITS Gwalior<br>Student Portal</h1>
            <p>Apply for leaves, track status, and manage approvals in one place.</p>
        </div>

    </div>
</div>

<!-- RIGHT: Login form -->
<div class="right-panel">
    <div class="form-box">

        <h2>Sign in to your account</h2>
        <p class="subtitle">Please enter your credentials to proceed.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="text" name="email" required placeholder="Email address" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Sign in</button>
        </form>

        <div class="footer-text">
            <p>&copy; 2026 Madhav Institute of Technology &amp; Science, Gwalior</p>
            <p>Developed by <span>Tejas Pratap Singh</span> &amp; <span>Shivanshu Yadav</span></p>
        </div>

    </div>
</div>

</body>
</html>
