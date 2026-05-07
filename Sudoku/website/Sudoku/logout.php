<?php
/**
 * ZEN SUDOKU - SECURE LOGOUT HANDLER
 */
session_start();

// 1. Clear all session variables
$_SESSION = array();

// 2. If it's desired to kill the session, also delete the session cookie.
// Note: This completely logs the user out from the browser side.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Finally, destroy the session on the server
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out | Zen Sudoku</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Meta refresh to redirect after 2 seconds -->
    <meta http-equiv="refresh" content="2;url=authentication/login.php">
</head>
<body class="bg-dark d-flex align-items-center min-vh-100 text-white">

    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                
                <!-- Spinner Animation -->
                <div class="mb-4">
                    <div class="spinner-border text-primary border-4" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <!-- Branding -->
                <h4 class="fw-bold mb-2">SECURE LOGOUT</h4>
                <p class="text-secondary small text-uppercase tracking-widest mb-4">Terminating Encrypted Session...</p>
                
                <div class="card bg-white bg-opacity-10 border-0 rounded-4 py-3 px-2">
                    <div class="card-body">
                        <p class="mb-0 text-light opacity-75">
                            <i class="bi bi-shield-check text-success me-2"></i>
                            Your session has been successfully cleared.
                        </p>
                    </div>
                </div>

                <!-- Immediate Link if Redirect Fails -->
                <div class="mt-4">
                    <p class="small text-muted">Redirecting you to the portal...</p>
                    <a href="authentication/login.php" class="btn btn-link btn-sm text-primary text-decoration-none fw-bold">
                        Click here to go now
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- JavaScript fallback for redirect -->
    <script>
        setTimeout(function(){
            window.location.href = "authentication/login.php";
        }, 2000);
    </script>
</body>
</html>