<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: authentication/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH REAL STATS FROM DB
========================= */

/*
CREATE TABLE game_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    result VARCHAR(20),
    completion_time VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/

/* TOTAL GAMES */
$totalQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
     FROM game_stats
     WHERE user_id='$user_id'"
);

$totalData = mysqli_fetch_assoc($totalQuery);

$total_games = $totalData['total'] ?? 0;

/* TOTAL WINS */
$winsQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) as wins
     FROM game_stats
     WHERE user_id='$user_id'
     AND result='win'"
);

$winsData = mysqli_fetch_assoc($winsQuery);

$wins = $winsData['wins'] ?? 0;

/* BEST TIME */
$bestQuery = mysqli_query(
    $conn,
    "SELECT completion_time
     FROM game_stats
     WHERE user_id='$user_id'
     AND result='win'
     ORDER BY completion_time ASC
     LIMIT 1"
);

$bestData = mysqli_fetch_assoc($bestQuery);

$best_time = $bestData['completion_time'] ?? '--:--';

/* WIN RATE */
$win_rate = 0;

if ($total_games > 0) {
    $win_rate = round(($wins / $total_games) * 100);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Dashboard | Sudoku Elite</title>

<!-- BOOTSTRAP -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    background:#0f172a;
    color:white;
    overflow-x:hidden;
    font-family:'Segoe UI',sans-serif;
}

/* MAIN */
.main-content{
    padding:30px;
}

/* HEADER */
.dashboard-header{

    background:
    linear-gradient(
    135deg,
    #2563eb,
    #1d4ed8
    );

    border-radius:24px;

    padding:30px;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.35);
}

/* PLAYER */
.player-badge{

    background:
    rgba(255,255,255,0.15);

    padding:12px 20px;

    border-radius:14px;

    font-weight:600;

    backdrop-filter:blur(10px);
}

/* STAT CARDS */
.stat-card{

    background:#1e293b;

    border:none;

    border-radius:24px;

    color:white;

    overflow:hidden;

    position:relative;

    transition:0.3s;

    box-shadow:
    0 10px 35px rgba(0,0,0,0.25);
}

.stat-card:hover{

    transform:translateY(-6px);
}

/* ICON */
.stat-card .icon{

    position:absolute;

    top:20px;
    right:20px;

    font-size:48px;

    opacity:0.12;
}

/* CONTENT */
.stat-card h6{
    color:#94a3b8;
}

.stat-card h2{
    font-weight:800;
    font-size:40px;
}

/* QUICK ACTION */
.quick-card{

    background:#1e293b;

    border:none;

    border-radius:24px;

    box-shadow:
    0 10px 35px rgba(0,0,0,0.25);
}

/* ACTION BUTTON */
.action-btn{

    padding:14px 22px;

    border-radius:16px;

    font-weight:700;
}

/* MOBILE */
@media(max-width:768px){

    .main-content{
        padding:20px;
    }

    .dashboard-header{
        padding:24px;
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
    <div class="dashboard-header mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h2 class="fw-bold mb-2">

                    <i class="fa-solid fa-grid-2"></i>

                    Dashboard

                </h2>

                <p class="mb-0 text-light">

                    Welcome back to Sudoku Elite

                </p>

            </div>

            <div class="player-badge">

                <i class="fa-solid fa-user"></i>

                <?= htmlspecialchars($_SESSION['username'] ?? 'Player') ?>

            </div>

        </div>

    </div>

    <!-- STATS -->
    <div class="row g-4">

        <!-- TOTAL GAMES -->
        <div class="col-md-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <i class="fa-solid fa-gamepad icon"></i>

                    <h6>Total Games</h6>

                    <h2><?= $total_games ?></h2>

                    <p class="text-secondary mb-0">

                        Games played

                    </p>

                </div>

            </div>

        </div>

        <!-- WINS -->
        <div class="col-md-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <i class="fa-solid fa-trophy icon"></i>

                    <h6>Total Wins</h6>

                    <h2><?= $wins ?></h2>

                    <p class="text-secondary mb-0">

                        Victories achieved

                    </p>

                </div>

            </div>

        </div>

        <!-- BEST TIME -->
        <div class="col-md-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <i class="fa-solid fa-stopwatch icon"></i>

                    <h6>Best Time</h6>

                    <h2><?= $best_time ?></h2>

                    <p class="text-secondary mb-0">

                        Fastest completion

                    </p>

                </div>

            </div>

        </div>

        <!-- WIN RATE -->
        <div class="col-md-3">

            <div class="card stat-card h-100">

                <div class="card-body">

                    <i class="fa-solid fa-chart-line icon"></i>

                    <h6>Win Rate</h6>

                    <h2><?= $win_rate ?>%</h2>

                    <p class="text-secondary mb-0">

                        Success percentage

                    </p>

                </div>

            </div>

        </div>

    </div>

    <!-- QUICK ACTION -->
    <div class="mt-5">

        <div class="card quick-card">

            <div class="card-body p-4">

                <h4 class="fw-bold mb-4 text-light">

                    <i class="fa-solid fa-bolt"></i>

                    Quick Actions

                </h4>

                <div class="d-flex flex-wrap gap-3">

                    <a href="board.php"
                    class="btn btn-success action-btn">

                        <i class="fa-solid fa-play"></i>

                        Play Game

                    </a>

                    <a href="leaderboard.php"
                    class="btn btn-outline-primary action-btn">

                        <i class="fa-solid fa-ranking-star"></i>

                        Leaderboard

                    </a>

                    <a href="profile.php"
                    class="btn btn-outline-light action-btn">

                        <i class="fa-solid fa-user"></i>

                        Profile

                    </a>

                    <a href="settings.php"
                    class="btn btn-outline-warning action-btn">

                        <i class="fa-solid fa-gear"></i>

                        Settings

                    </a>

                </div>

            </div>

        </div>

    </div>

</main>

</div>
</div>

</body>
</html>