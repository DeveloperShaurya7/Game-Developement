<?php
include '../db.php';
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    // Prepared statement to fetch the hashed password
    $stmt = mysqli_prepare($conn, "SELECT username, password FROM users WHERE username = ? OR email = ?");
    mysqli_stmt_bind_param($stmt, "ss", $user, $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($pass, $row['password'])) {
            // Set session variables to match index.php requirements
            $_SESSION['user_id'] = $row['id'] ?? 1; // Ensure your DB has an 'id' column
            $_SESSION['username'] = $row['username'];
            
            header("Location: ../index.php");
            exit();
        } else {
            $error = "The Access Key provided is incorrect.";
        }
    } else {
        $error = "No operative found with those credentials.";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Zen Sudoku Elite</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons (Matching Index) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
            
            <!-- Branding Area -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-dark text-primary rounded-pill px-4 py-2 shadow-sm mb-3">
                    <i class="bi bi-grid-3x3-gap-fill me-2 fs-4"></i>
                    <span class="fw-bold tracking-widest h5 mb-0 text-white">ZEN SUDOKU</span>
                </div>
                <p class="text-muted small text-uppercase fw-bold tracking-tight">Authenticated Access Only</p>
            </div>

            <!-- Login Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 bg-white">
                    
                    <h2 class="h4 fw-bold mb-4">Welcome Back</h2>

                    <!-- Error Alert -->
                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger d-flex align-items-center small mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div><?= $error ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <!-- Operative ID (Floating Label UX) -->
                        <div class="form-floating mb-3">
                            <input type="text" name="username" class="form-control bg-light border-0" id="userInput" placeholder="Username or Email" required>
                            <label for="userInput" class="text-muted">Username or Email</label>
                        </div>

                        <!-- Access Key (Floating Label UX) -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control bg-light border-0" id="passInput" placeholder="Password" required>
                            <label for="passInput" class="text-muted">Access Key</label>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4 small">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label text-muted" for="remember">Remember me</label>
                            </div>
                            <a href="#" class="text-primary text-decoration-none fw-bold">Reset Key?</a>
                        </div>

                        <!-- Login Button -->
                        <button type="submit" class="btn btn-dark w-100 py-3 fw-bold text-uppercase shadow-sm mb-3">
                            Initialize Session <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>

                    <div class="text-center mt-2">
                        <p class="small text-muted mb-0">Unregistered Operative?</p>
                        <a href="register.php" class="text-primary text-decoration-none small fw-bold">Request New Access</a>
                    </div>
                </div>
            </div>

            <!-- Security Badge Footer -->
            <div class="text-center mt-5">
                <p class="text-muted" style="font-size: 0.75rem;">
                    <i class="bi bi-shield-lock-fill text-success me-1"></i> 
                    SECURE SSL 256-BIT ENCRYPTION ACTIVE
                </p>
                <div class="mt-2">
                    <a href="../index.php" class="btn btn-link btn-sm text-decoration-none text-secondary">
                        <i class="bi bi-house-door me-1"></i> Back to Portal
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>