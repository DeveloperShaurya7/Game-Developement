<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: authentication/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Player';
$email = $_SESSION['email'] ?? '';

$message = "";
$message_type = "success";

/* ================= HANDLE ACTIONS ================= */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /* UPDATE PROFILE */
    if (isset($_POST['update_profile'])) {

        $new_name = mysqli_real_escape_string($conn, trim($_POST['username']));
        $new_email = mysqli_real_escape_string($conn, trim($_POST['email']));

        mysqli_query($conn,
        "UPDATE users
        SET username='$new_name',
        email='$new_email'
        WHERE id=$user_id");

        $_SESSION['username'] = $new_name;
        $_SESSION['email'] = $new_email;

        $username = $new_name;
        $email = $new_email;

        $message = "Profile updated successfully!";
        $message_type = "success";
    }

    /* CHANGE PASSWORD */
    if (isset($_POST['change_password'])) {

        $current = $_POST['current'];
        $new = $_POST['new'];

        $res = mysqli_query($conn,
        "SELECT password FROM users WHERE id=$user_id");

        $row = mysqli_fetch_assoc($res);

        if ($row && password_verify($current, $row['password'])) {

            $hash = password_hash($new, PASSWORD_DEFAULT);

            mysqli_query($conn,
            "UPDATE users
            SET password='$hash'
            WHERE id=$user_id");

            $message = "Password updated successfully!";
            $message_type = "success";

        } else {

            $message = "Current password is incorrect!";
            $message_type = "danger";
        }
    }

    /* DELETE ACCOUNT */
    if (isset($_POST['delete_account'])) {

        mysqli_query($conn,
        "DELETE FROM users WHERE id=$user_id");

        session_destroy();

        header("Location: authentication/login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Settings | Sudoku Elite</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


<style>

body{
    background:#0f172a;
    color:white;
    font-family:'Segoe UI',sans-serif;
    overflow-x:hidden;
}

/* MAIN */
.main-content{
    padding:30px;
}

/* HEADER */
.settings-header{
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    border-radius:24px;
    padding:30px;
    margin-bottom:30px;
    box-shadow:0 15px 40px rgba(0,0,0,0.35);
}

.profile-badge{
    background:rgba(255,255,255,0.15);
    padding:12px 18px;
    border-radius:14px;
    backdrop-filter:blur(10px);
    font-weight:600;
}

/* CARDS */
.settings-card{
    background:#1e293b;
    border:none;
    border-radius:24px;
    color:white;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,0.35);
}

.settings-card .card-body{
    padding:30px;
}

/* INPUTS */
.form-control{
    background:#0f172a;
    border:1px solid #334155;
    color:white;
    padding:14px;
    border-radius:14px;
}

.form-control:focus{
    background:#0f172a;
    color:white;
    border-color:#2563eb;
    box-shadow:none;
}

.form-control::placeholder{
    color:#94a3b8;
}

label{
    margin-bottom:8px;
    color:#cbd5e1;
    font-weight:500;
}

/* BUTTONS */
.custom-btn{
    border-radius:14px;
    padding:12px;
    font-weight:600;
}

/* SWITCHES */
.form-check-input{
    width:55px;
    height:28px;
    cursor:pointer;
}

.form-check-label{
    margin-left:10px;
    font-size:16px;
}

/* DANGER */
.danger-card{
    border:2px solid rgba(239,68,68,0.3);
}

.danger-card h4{
    color:#ef4444;
}

/* ICONS */
.section-icon{
    width:55px;
    height:55px;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:22px;
    background:#0f172a;
}

/* ALERT */
.custom-alert{
    border-radius:16px;
    border:none;
    padding:15px 20px;
}

/* MOBILE */
@media(max-width:768px){

    .main-content{
        padding:18px;
    }

}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<?php include 'sidebar.php'; ?>

<!-- MAIN -->
<main class="col-md-9 ms-sm-auto col-lg-10 main-content min-vh-100">

    <!-- HEADER -->
    <div class="settings-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h1 class="fw-bold mb-2">

                    <i class="fa-solid fa-gear"></i>
                    Settings

                </h1>

                <p class="mb-0 text-light">
                    Manage your profile and Sudoku preferences.
                </p>

            </div>

            <div class="profile-badge">

                <i class="fa-solid fa-user"></i>

                <?= htmlspecialchars($username) ?>

            </div>

        </div>

    </div>

    <!-- ALERT -->
    <?php if($message): ?>

        <div class="alert alert-<?= $message_type ?> custom-alert">

            <i class="fa-solid fa-circle-info"></i>

            <?= $message ?>

        </div>

    <?php endif; ?>

    <div class="row g-4">

        <!-- PROFILE -->
        <div class="col-lg-6">

            <div class="card settings-card h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="section-icon">

                            <i class="fa-solid fa-user"></i>

                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">
                                Profile Settings
                            </h4>

                            <p class="text-secondary mb-0">
                                Update your account information
                            </p>

                        </div>

                    </div>

                    <form method="POST">

                        <input type="hidden"
                        name="update_profile">

                        <!-- USERNAME -->
                        <div class="mb-4">

                            <label>Username</label>

                            <input type="text"
                            name="username"
                            class="form-control"
                            value="<?= htmlspecialchars($username) ?>"
                            required>

                        </div>

                        <!-- EMAIL -->
                        <div class="mb-4">

                            <label>Email Address</label>

                            <input type="email"
                            name="email"
                            class="form-control"
                            value="<?= htmlspecialchars($email) ?>"
                            required>

                        </div>

                        <button class="btn btn-primary w-100 custom-btn">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Changes

                        </button>

                    </form>

                </div>

            </div>

        </div>

        <!-- PASSWORD -->
        <div class="col-lg-6">

            <div class="card settings-card h-100">

                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="section-icon">

                            <i class="fa-solid fa-lock"></i>

                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">
                                Security
                            </h4>

                            <p class="text-secondary mb-0">
                                Change your account password
                            </p>

                        </div>

                    </div>

                    <form method="POST">

                        <input type="hidden"
                        name="change_password">

                        <!-- CURRENT -->
                        <div class="mb-4">

                            <label>Current Password</label>

                            <input type="password"
                            name="current"
                            class="form-control"
                            placeholder="Enter current password"
                            required>

                        </div>

                        <!-- NEW -->
                        <div class="mb-4">

                            <label>New Password</label>

                            <input type="password"
                            name="new"
                            class="form-control"
                            placeholder="Enter new password"
                            required>

                        </div>

                        <button class="btn btn-warning w-100 custom-btn">

                            <i class="fa-solid fa-key"></i>

                            Update Password

                        </button>

                    </form>

                </div>

            </div>

        </div>

        <!-- PREFERENCES -->
        <div class="col-12">

            <div class="card settings-card">

                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="section-icon">

                            <i class="fa-solid fa-sliders"></i>

                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">
                                Game Preferences
                            </h4>

                            <p class="text-secondary mb-0">
                                Customize your playing experience
                            </p>

                        </div>

                    </div>

                    <!-- DARK MODE -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <div>

                            <h6 class="mb-1">
                                Dark Mode
                            </h6>

                            <small class="text-secondary">
                                Enable dark appearance
                            </small>

                        </div>

                        <div class="form-check form-switch">

                            <input class="form-check-input"
                            type="checkbox"
                            checked>

                        </div>

                    </div>

                    <!-- SOUND -->
                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="mb-1">
                                Sound Effects
                            </h6>

                            <small class="text-secondary">
                                Play sounds during gameplay
                            </small>

                        </div>

                        <div class="form-check form-switch">

                            <input class="form-check-input"
                            type="checkbox"
                            checked>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- DANGER -->
        <div class="col-12">

            <div class="card settings-card danger-card">

                <div class="card-body">

                    <div class="d-flex align-items-center gap-3 mb-4">

                        <div class="section-icon">

                            <i class="fa-solid fa-triangle-exclamation text-danger"></i>

                        </div>

                        <div>

                            <h4 class="fw-bold mb-1">
                                Danger Zone
                            </h4>

                            <p class="text-secondary mb-0">
                                Permanently delete your account
                            </p>

                        </div>

                    </div>

                    <div class="alert alert-danger rounded-4">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        This action cannot be undone.

                    </div>

                    <form method="POST"
                    onsubmit="return confirm('Are you sure you want to delete your account?');">

                        <input type="hidden"
                        name="delete_account">

                        <button class="btn btn-danger custom-btn">

                            <i class="fa-solid fa-trash"></i>

                            Delete Account

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</main>

</div>
</div>

</body>
</html>