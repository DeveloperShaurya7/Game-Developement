<?php
session_start();
include 'db.php';

/* ================= SECURITY ================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: authentication/login.php");
    exit();
}

/* ================= USER ================= */

$user_id = $_SESSION['user_id'];

$userQuery = mysqli_query($conn, "
    SELECT username,email
    FROM users
    WHERE id = '$user_id'
");

$user = mysqli_fetch_assoc($userQuery);

$username = $user['username'] ?? 'Player';
$email = $user['email'] ?? 'player@sudokuelite.com';

/* ================= REAL GAME STATS ================= */

$statsQuery = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total_games,
        SUM(status='won') AS total_wins,
        MIN(time_taken) AS best_time,
        MAX(score) AS highest_score,
        SUM(score) AS total_xp
    FROM game_stats
    WHERE user_id = '$user_id'
");

$stats = mysqli_fetch_assoc($statsQuery);

$gamesPlayed = (int)($stats['total_games'] ?? 0);
$gamesWon = (int)($stats['total_wins'] ?? 0);

$bestTimeSeconds = $stats['best_time'];

$highestScore = (int)($stats['highest_score'] ?? 0);

$xp = (int)($stats['total_xp'] ?? 0);

$winRate = $gamesPlayed > 0
    ? round(($gamesWon / $gamesPlayed) * 100)
    : 0;

/* ================= FORMAT BEST TIME ================= */

if ($bestTimeSeconds && $bestTimeSeconds > 0) {

    $minutes = floor($bestTimeSeconds / 60);
    $seconds = $bestTimeSeconds % 60;

    $bestTime =
        str_pad($minutes, 2, '0', STR_PAD_LEFT)
        . ":" .
        str_pad($seconds, 2, '0', STR_PAD_LEFT);

} else {

    $bestTime = "00:00";

}

/* ================= XP + LEVEL ================= */

$nextXp = 10000;

$progress = $nextXp > 0
    ? min(($xp / $nextXp) * 100, 100)
    : 0;

/* LEVEL SYSTEM */

if ($xp >= 15000) {
    $level = "Legend";
} elseif ($xp >= 10000) {
    $level = "Master";
} elseif ($xp >= 7000) {
    $level = "Elite";
} elseif ($xp >= 4000) {
    $level = "Pro";
} elseif ($xp >= 1500) {
    $level = "Advanced";
} else {
    $level = "Beginner";
}

/* ================= GLOBAL RANK ================= */

$rankQuery = mysqli_query($conn, "
    SELECT rank_position FROM (
        SELECT
            user_id,
            RANK() OVER (ORDER BY SUM(score) DESC) AS rank_position
        FROM game_stats
        GROUP BY user_id
    ) ranked
    WHERE user_id = '$user_id'
");

$rankData = mysqli_fetch_assoc($rankQuery);

$rank = "#" . ($rankData['rank_position'] ?? '0');

/* ================= SKILL CALCULATIONS ================= */

$logicMastery = min(100, ($gamesWon * 2));

$speedRating = 0;

if ($bestTimeSeconds > 0) {

    if ($bestTimeSeconds <= 180) {
        $speedRating = 100;
    } elseif ($bestTimeSeconds <= 300) {
        $speedRating = 85;
    } elseif ($bestTimeSeconds <= 600) {
        $speedRating = 70;
    } else {
        $speedRating = 50;
    }

}

$accuracy = $winRate;

$consistency = $gamesPlayed > 0
    ? min(100, round(($gamesWon / $gamesPlayed) * 95))
    : 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Profile | Sudoku Elite</title>

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
    font-family:'Segoe UI',sans-serif;
    overflow-x:hidden;
}

.main-content{
    padding:30px;
}

.profile-hero{
    background:
    linear-gradient(
    135deg,
    #2563eb,
    #1d4ed8,
    #0f172a
    );

    border-radius:28px;
    padding:40px;
    position:relative;
    overflow:hidden;

    box-shadow:
    0 20px 50px rgba(0,0,0,0.35);
}

.profile-hero::before{
    content:'';
    position:absolute;
    width:300px;
    height:300px;
    background:rgba(255,255,255,0.05);
    border-radius:50%;
    top:-100px;
    right:-100px;
}

.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    background:white;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:50px;
    color:#2563eb;

    border:6px solid rgba(255,255,255,0.2);

    box-shadow:
    0 10px 30px rgba(0,0,0,0.3);
}

.rank-badge{
    background:
    rgba(255,255,255,0.15);

    padding:10px 18px;
    border-radius:14px;

    backdrop-filter:blur(10px);

    font-weight:600;
}

.custom-card{
    background:#1e293b;
    border:none;
    border-radius:24px;
    color:white;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.35);

    overflow:hidden;
}

.custom-card .card-body{
    padding:28px;
}

.stat-box{
    background:#0f172a;
    border-radius:20px;
    padding:22px;
    text-align:center;

    transition:0.3s;

    border:1px solid #334155;
}

.stat-box:hover{
    transform:translateY(-5px);
    border-color:#2563eb;
}

.stat-icon{
    width:60px;
    height:60px;
    border-radius:18px;

    background:
    rgba(37,99,235,0.15);

    display:flex;
    justify-content:center;
    align-items:center;

    margin:auto;
    margin-bottom:15px;

    color:#60a5fa;
    font-size:24px;
}

.progress{
    height:12px;
    background:#0f172a;
    border-radius:20px;
}

.progress-bar{
    border-radius:20px;
}

.action-btn{
    background:#0f172a;
    border:1px solid #334155;
    color:white;

    border-radius:16px;

    padding:14px 18px;

    transition:0.3s;
    font-weight:600;
}

.action-btn:hover{
    background:#2563eb;
    border-color:#2563eb;
    color:white;

    transform:translateX(4px);
}

.achievement{
    background:#0f172a;
    border-radius:18px;
    padding:18px;

    border:1px solid #334155;
}

.achievement i{
    font-size:28px;
}

.footer{
    color:#94a3b8;
}

@media(max-width:768px){

    .main-content{
        padding:18px;
    }

    .profile-hero{
        padding:25px;
        text-align:center;
    }

    .avatar{
        margin:auto;
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

    <!-- HERO -->
    <div class="profile-hero mb-4">

        <div class="row align-items-center g-4">

            <div class="col-lg-8">

                <div class="d-flex align-items-center gap-4 flex-wrap">

                    <div class="avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div>

                        <h1 class="fw-bold mb-2">
                            <?= htmlspecialchars($username) ?>
                        </h1>

                        <p class="mb-3 text-light">
                            Sudoku Elite Strategist
                        </p>

                        <div class="d-flex flex-wrap gap-2">

                            <div class="rank-badge">
                                <i class="fa-solid fa-ranking-star"></i>
                                Rank <?= $rank ?>
                            </div>

                            <div class="rank-badge">
                                <i class="fa-solid fa-crown"></i>
                                <?= $level ?> Tier
                            </div>

                            <div class="rank-badge">
                                <i class="fa-solid fa-envelope"></i>
                                <?= htmlspecialchars($email) ?>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- XP -->
            <div class="col-lg-4">

                <div class="custom-card">

                    <div class="card-body">

                        <div class="d-flex justify-content-between mb-2">

                            <span>Experience</span>

                            <span><?= number_format($xp) ?> XP</span>

                        </div>

                        <div class="progress mb-3">

                            <div class="progress-bar bg-primary"
                            style="width:<?= $progress ?>%">

                            </div>

                        </div>

                        <small class="text-secondary">

                            Keep solving puzzles to level up.

                        </small>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- STATS -->
    <div class="row g-4 mb-4">

        <div class="col-md-3">

            <div class="stat-box">

                <div class="stat-icon">
                    <i class="fa-solid fa-gamepad"></i>
                </div>

                <h2 class="fw-bold">
                    <?= $gamesPlayed ?>
                </h2>

                <p class="text-secondary mb-0">
                    Games Played
                </p>

            </div>

        </div>

        <div class="col-md-3">

            <div class="stat-box">

                <div class="stat-icon">
                    <i class="fa-solid fa-trophy"></i>
                </div>

                <h2 class="fw-bold text-success">
                    <?= $gamesWon ?>
                </h2>

                <p class="text-secondary mb-0">
                    Victories
                </p>

            </div>

        </div>

        <div class="col-md-3">

            <div class="stat-box">

                <div class="stat-icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>

                <h2 class="fw-bold text-info">
                    <?= $winRate ?>%
                </h2>

                <p class="text-secondary mb-0">
                    Win Rate
                </p>

            </div>

        </div>

        <div class="col-md-3">

            <div class="stat-box">

                <div class="stat-icon">
                    <i class="fa-solid fa-stopwatch"></i>
                </div>

                <h2 class="fw-bold text-warning">
                    <?= $bestTime ?>
                </h2>

                <p class="text-secondary mb-0">
                    Best Time
                </p>

            </div>

        </div>

    </div>

    <!-- DETAILS -->
    <div class="row g-4">

        <!-- SKILLS -->
        <div class="col-lg-7">

            <div class="card custom-card h-100">

                <div class="card-body">

                    <h4 class="fw-bold mb-4">

                        <i class="fa-solid fa-brain text-primary"></i>

                        Skill Progress

                    </h4>

                    <!-- LOGIC -->
                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>Logic Mastery</span>
                            <span><?= $logicMastery ?>%</span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar bg-primary"
                            style="width:<?= $logicMastery ?>%"></div>

                        </div>

                    </div>

                    <!-- SPEED -->
                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>Speed Rating</span>
                            <span><?= $speedRating ?>%</span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar bg-info"
                            style="width:<?= $speedRating ?>%"></div>

                        </div>

                    </div>

                    <!-- ACCURACY -->
                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-2">

                            <span>Accuracy</span>
                            <span><?= $accuracy ?>%</span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar bg-success"
                            style="width:<?= $accuracy ?>%"></div>

                        </div>

                    </div>

                    <!-- CONSISTENCY -->
                    <div>

                        <div class="d-flex justify-content-between mb-2">

                            <span>Consistency</span>
                            <span><?= $consistency ?>%</span>

                        </div>

                        <div class="progress">

                            <div class="progress-bar bg-warning"
                            style="width:<?= $consistency ?>%"></div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- ACTIONS -->
        <div class="col-lg-5">

            <div class="card custom-card h-100">

                <div class="card-body">

                    <h4 class="fw-bold mb-4">

                        <i class="fa-solid fa-user-gear text-primary"></i>

                        Account Actions

                    </h4>

                    <div class="d-grid gap-3">

                        <a href="settings.php"
                        class="btn action-btn text-start">

                            <i class="fa-solid fa-pen-to-square me-2"></i>

                            Edit Profile

                        </a>

                        <a href="leaderboard.php"
                        class="btn action-btn text-start">

                            <i class="fa-solid fa-ranking-star me-2"></i>

                            View Leaderboard

                        </a>

                        <a href="board.php"
                        class="btn action-btn text-start">

                            <i class="fa-solid fa-play me-2"></i>

                            Start New Game

                        </a>

                        <a href="logout.php"
                        class="btn btn-outline-danger rounded-4 py-3 mt-2">

                            <i class="fa-solid fa-right-from-bracket me-2"></i>

                            Logout

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- ACHIEVEMENTS -->
    <div class="card custom-card mt-4">

        <div class="card-body">

            <h4 class="fw-bold mb-4">

                <i class="fa-solid fa-medal text-warning"></i>

                Achievements

            </h4>

            <div class="row g-4">

                <?php if($gamesWon >= 50): ?>

                <div class="col-md-4">

                    <div class="achievement">

                        <i class="fa-solid fa-crown text-warning mb-3"></i>

                        <h5 class="fw-bold">
                            Puzzle King
                        </h5>

                        <p class="text-secondary mb-0">
                            Won 50+ games.
                        </p>

                    </div>

                </div>

                <?php endif; ?>

                <?php if($bestTimeSeconds > 0 && $bestTimeSeconds <= 300): ?>

                <div class="col-md-4">

                    <div class="achievement">

                        <i class="fa-solid fa-bolt text-info mb-3"></i>

                        <h5 class="fw-bold">
                            Speed Demon
                        </h5>

                        <p class="text-secondary mb-0">
                            Finished under 5 minutes.
                        </p>

                    </div>

                </div>

                <?php endif; ?>

                <?php if($winRate >= 90): ?>

                <div class="col-md-4">

                    <div class="achievement">

                        <i class="fa-solid fa-brain text-success mb-3"></i>

                        <h5 class="fw-bold">
                            Mastermind
                        </h5>

                        <p class="text-secondary mb-0">
                            90%+ win accuracy.
                        </p>

                    </div>

                </div>

                <?php endif; ?>

            </div>

        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer text-center mt-4">

        © <?= date('Y') ?> Sudoku Elite • Train Your Mind Daily

    </div>

</main>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>