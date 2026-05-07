<?php
session_start();
include '../db.php';
// Admin Check
if (!isset($_SESSION['user_id'])) { header("Location: ../authentication/login.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Operatives | Zen Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .sidebar { min-height: 100vh; background: #1a1d20; }
        .nav-link { color: rgba(255,255,255,0.7); }
        .nav-link:hover, .nav-link.active { color: #fff; background: rgba(13, 110, 253, 0.2); border-radius: 8px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR (Identical to index.php but active link changed) -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar px-3 pt-4 position-fixed">
            <div class="d-flex mb-4 px-2"><i class="bi bi-grid-3x3-gap-fill text-primary fs-3 me-2"></i><span class="text-white fw-bold h5 mb-0">ZEN ADMIN</span></div>
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a href="index.php" class="nav-link py-3 px-3"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                <li class="nav-item mb-2"><a href="users.php" class="nav-link active py-3 px-3"><i class="bi bi-people me-2"></i> Operatives</a></li>
                <li class="nav-item mb-2"><a href="stats.php" class="nav-link py-3 px-3"><i class="bi bi-bar-chart me-2"></i> Game Intel</a></li>
                <li class="nav-item mt-5"><hr class="text-secondary"></li>
                <li class="nav-item"><a href="../index.php" class="nav-link py-2 px-3 text-info"><i class="bi bi-controller me-2"></i> Return to Game</a></li>
            </ul>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-4">
            <div class="py-3 border-bottom mb-4 d-flex justify-content-between align-items-center">
                <h1 class="h3 fw-bold mb-0">Player Management</h1>
                <div class="input-group w-25">
                    <input type="text" class="form-control form-control-sm" placeholder="Search ID...">
                    <button class="btn btn-primary btn-sm"><i class="bi bi-search"></i></button>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr><th>ID</th><th>Username</th><th>Email</th><th>Created</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#001</td>
                            <td class="fw-bold">ZenMaster</td>
                            <td>zen@elite.com</td>
                            <td class="small">2024-03-01</td>
                            <td><span class="badge rounded-pill bg-success px-3">Active</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
</body>
</html>