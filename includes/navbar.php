<!-- Navbar Component -->
<!-- Usage: include 'includes/navbar.php'; -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">SLMS - <?php echo ucfirst($_SESSION['role']); ?></a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i> 
                <?php echo htmlspecialchars($_SESSION['name']); ?>
                <?php if($_SESSION['department']): ?>
                    <small class="text-white-50">(<?php echo htmlspecialchars($_SESSION['department']); ?>)</small>
                <?php endif; ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>
<style>
    .navbar { margin-bottom: 2rem; }
</style>
