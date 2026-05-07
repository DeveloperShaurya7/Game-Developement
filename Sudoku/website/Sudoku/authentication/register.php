<?php
include '../db.php';
session_start();

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Basic Validation
    if ($pass !== $confirm_pass) {
        $error = "Access keys do not match.";
    } else {
        // 2. Check if username or email already exists
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? OR email = ?");
        mysqli_stmt_bind_param($check, "ss", $user, $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $error = "Operative ID or Email already registered in system.";
        } else {
            // 3. Hash password and Insert
            $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
            $insert = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert, "sss", $user, $email, $hashed_pass);
            
            if (mysqli_stmt_execute($insert)) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "System error during initialization.";
            }
            mysqli_stmt_close($insert);
        }
        mysqli_stmt_close($check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Zen Sudoku Elite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light d-flex align-items-center min-vh-100 py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            
            <!-- Branding -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-dark text-primary rounded-pill px-4 py-2 shadow-sm mb-3">
                    <i class="bi bi-person-plus-fill me-2 fs-4"></i>
                    <span class="fw-bold tracking-widest h5 mb-0 text-white">NEW OPERATIVE</span>
                </div>
            </div>

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 bg-white">
                    <h2 class="h4 fw-bold mb-1">Create Account</h2>
                    <p class="text-muted small mb-4">Initialize your secure Sudoku profile</p>

                    <!-- Feedback Alerts -->
                    <?php if($error): ?>
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small" role="alert">
                            <i class="bi bi-shield-exclamation me-2"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>

                    <?php if($success): ?>
                        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success small" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <!-- Username -->
                        <div class="form-floating mb-3">
                            <input type="text" name="username" class="form-control bg-light border-0" id="userIn" placeholder="Username" required>
                            <label for="userIn">Operative Username</label>
                        </div>

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control bg-light border-0" id="emailIn" placeholder="Email" required>
                            <label for="emailIn">Email Address</label>
                        </div>

                        <div class="row g-2">
                            <!-- Password -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" name="password" class="form-control bg-light border-0" id="passIn" placeholder="Access Key" required>
                                    <label for="passIn">Access Key</label>
                                </div>
                            </div>
                            <!-- Confirm Password -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="password" name="confirm_password" class="form-control bg-light border-0" id="confIn" placeholder="Confirm" required>
                                    <label for="confIn">Confirm Key</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-4 small">
                            <input class="form-check-input" type="checkbox" id="terms" required>
                            <label class="form-check-label text-muted" for="terms">
                                I agree to the <span class="text-primary fw-bold">Rules of Conduct</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase shadow-sm mb-3">
                            Register Operative
                        </button>
                    </form>

                    <div class="text-center mt-2">
                        <p class="small text-muted mb-0">Already registered?</p>
                        <a href="login.php" class="text-primary text-decoration-none small fw-bold">Sign In to System</a>
                    </div>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-4">
                <a href="../index.php" class="text-secondary text-decoration-none small">
                    <i class="bi bi-house me-1"></i> Return to Portal
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>