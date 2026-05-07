<?php
session_start();
include '../db.php';

// Simple Admin Check (Assumes you've logged in)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Zen Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .sidebar { min-height: 100vh; background: #1a1d20; }
        .nav-link { color: rgba(255,255,255,0.7); transition: 0.3s; }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(13, 110, 253, 0.2); border-radius: 8px; }
        .stat-card { transition: transform 0.2s; border: none; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- SHARED SIDEBAR -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar px-3 pt-4 shadow position-fixed">
            <div class="d-flex align-items-center mb-4 px-2">
                <i class="bi bi-grid-3x3-gap-fill text-primary fs-3 me-2"></i>
                <span class="text-white fw-bold h5 mb-0">ZEN ADMIN</span>
            </div>
            
            <ul class="nav flex-column">
                <li class="nav-item mb-2">
                    <a href="index.php" class="nav-link active py-3 px-3"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="users.php" class="nav-link py-3 px-3"><i class="bi bi-people me-2"></i> Operatives</a>
                </li>
                <li class="nav-item mb-2">
                    <a href="stats.php" class="nav-link py-3 px-3"><i class="bi bi-bar-chart me-2"></i> Game Intel</a>
                </li>
                <li class="nav-item mt-5"><hr class="text-secondary"></li>
                <li class="nav-item">
                    <a href="../index.php" class="nav-link py-2 px-3 text-info"><i class="bi bi-controller me-2"></i> Return to Game</a>
                </li>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link py-2 px-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Terminate</a>
                </li>
            </ul>
        </nav>

        <!-- MAIN CONTENT -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
            <div class="py-3 border-bottom mb-4 d-flex justify-content-between align-items-center">
                <h1 class="h3 fw-bold mb-0">System Summary</h1>
                <span class="badge bg-dark px-3 py-2">Admin Active</span>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm p-4 bg-white rounded-4">
                        <div class="text-muted small fw-bold text-uppercase">Total Players</div>
                        <div class="h2 fw-bold mt-2">1,284</div>
                        <div class="text-success small mt-1"><i class="bi bi-arrow-up"></i> 12% increase</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm p-4 bg-white rounded-4 border-start border-primary border-4">
                        <div class="text-muted small fw-bold text-uppercase">Active Puzzles</div>
                        <div class="h2 fw-bold mt-2">42</div>
                        <div class="text-primary small mt-1">Live Boards</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm p-4 bg-white rounded-4">
                        <div class="text-muted small fw-bold text-uppercase">Server Health</div>
                        <div class="h2 fw-bold mt-2 text-success">Optimal</div>
                        <div class="text-muted small mt-1">Latency: 24ms</div>
                    </div>
                </div>
            </div>

            <div class="mt-5 card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-0"><h6 class="fw-bold mb-0">Recent Login Events</h6></div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr><th>User</th><th>Timestamp</th><th>IP Address</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>AceSudoku</td><td>10 mins ago</td><td>192.168.1.1</td><td><span class="badge bg-primary">Login</span></td></tr>
                            <tr><td>ZenMaster</td><td>25 mins ago</td><td>192.168.1.5</td><td><span class="badge bg-primary">Login</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>