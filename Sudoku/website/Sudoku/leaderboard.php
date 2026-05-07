<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: authentication/login.php");
    exit();
}

$current_user = $_SESSION['username'] ?? 'Player';

/* =========================
   FETCH REAL LEADERBOARD
========================= */

$leaders = [];

$query = mysqli_query($conn, "

    SELECT
        users.username,

        COALESCE(SUM(game_stats.score),0) AS score,

        COUNT(
            CASE
                WHEN game_stats.result='win'
                THEN 1
            END
        ) AS won,

        MIN(
            CASE
                WHEN game_stats.result='win'
                THEN game_stats.completion_time
            END
        ) AS best_time

    FROM users

    LEFT JOIN game_stats
    ON users.id = game_stats.user_id

    GROUP BY users.id

    ORDER BY score DESC

    LIMIT 20

");

while($row = mysqli_fetch_assoc($query)){

    $leaders[] = [

        'username' => $row['username'],

        'score' => $row['score'] ?? 0,

        'won' => $row['won'] ?? 0,

        'time' => $row['best_time'] ?? '--:--'

    ];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Leaderboard | Sudoku Elite</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<!-- FONT AWESOME -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    background:#0f172a;
    font-family:'Segoe UI',sans-serif;
    color:white;
    overflow-x:hidden;
}

/* MAIN */
.main-content{
    padding:30px;
}

/* HEADER */
.leaderboard-header{

    background:
    linear-gradient(
    135deg,
    #2563eb,
    #1d4ed8
    );

    border-radius:24px;

    padding:30px;

    margin-bottom:30px;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.35);
}

/* USER */
.user-badge{

    background:
    rgba(255,255,255,0.15);

    padding:10px 18px;

    border-radius:14px;

    font-weight:600;

    backdrop-filter:blur(10px);
}

/* TOP CARDS */
.top-card{

    background:#1e293b;

    border:none;

    border-radius:22px;

    overflow:hidden;

    position:relative;

    transition:0.3s;

    color:white;
}

.top-card:hover{
    transform:translateY(-5px);
}

.top-card .icon{

    position:absolute;

    right:20px;
    top:20px;

    font-size:55px;

    opacity:0.1;
}

.gold{
    border-top:4px solid #facc15;
}

.silver{
    border-top:4px solid #cbd5e1;
}

.bronze{
    border-top:4px solid #fb7185;
}

/* TABLE */
.leaderboard-card{

    background:#1e293b;

    border:none;

    border-radius:24px;

    overflow:hidden;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.35);
}

.table{
    margin:0;
    color:white;
}

.table thead{
    background:#2563eb;
}

.table thead th{

    border:none;

    padding:18px;

    font-size:14px;

    letter-spacing:0.5px;
}

.table tbody tr{

    transition:0.2s;

    border-color:#334155;
}

.table tbody tr:hover{
    background:#334155;
}

.table td{

    vertical-align:middle;

    padding:18px;

    border-color:#334155;
}

/* PLAYER */
.player-avatar{

    width:45px;
    height:45px;

    border-radius:50%;

    background:#0f172a;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:18px;
}

/* CURRENT USER */
.you-row{

    background:
    rgba(37,99,235,0.2);

    border-left:5px solid #2563eb;
}

/* RANK */
.rank-badge{

    width:38px;
    height:38px;

    border-radius:50%;

    display:flex;

    justify-content:center;

    align-items:center;

    font-weight:700;

    color:white;
}

.rank-1{
    background:linear-gradient(135deg,#facc15,#eab308);
}

.rank-2{
    background:linear-gradient(135deg,#cbd5e1,#94a3b8);
}

.rank-3{
    background:linear-gradient(135deg,#fb7185,#e11d48);
}

.rank-normal{
    background:#334155;
}

/* FOOTER */
.footer-note{

    color:#94a3b8;

    text-align:center;

    margin-top:20px;
}

/* MOBILE */
@media(max-width:768px){

    .main-content{
        padding:18px;
    }

    .table td,
    .table th{

        padding:12px;

        font-size:13px;
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
    <div class="leaderboard-header">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h1 class="fw-bold mb-2">

                    <i class="fa-solid fa-trophy"></i>

                    Global Leaderboard

                </h1>

                <p class="mb-0 text-light">

                    Compete with top Sudoku players.

                </p>

            </div>

            <div class="user-badge">

                <i class="fa-solid fa-user"></i>

                <?= htmlspecialchars($current_user) ?>

            </div>

        </div>

    </div>

    <!-- TOP 3 -->
    <div class="row g-4 mb-4">

        <?php
        $topClasses = ['gold','silver','bronze'];
        $topIcons = [
            'fa-crown',
            'fa-medal',
            'fa-award'
        ];

        for($i=0; $i<3; $i++):

            if(!isset($leaders[$i])) continue;

            $player = $leaders[$i];
        ?>

        <div class="col-md-4">

            <div class="card top-card <?= $topClasses[$i] ?> h-100">

                <div class="card-body p-4">

                    <i class="fa-solid <?= $topIcons[$i] ?> icon"></i>

                    <div class="mb-3">

                        <span class="badge bg-primary px-3 py-2">

                            #<?= $i + 1 ?>

                        </span>

                    </div>

                    <h3 class="fw-bold">

                        <?= htmlspecialchars($player['username']) ?>

                    </h3>

                    <h2 class="fw-bold text-warning">

                        <?= number_format($player['score']) ?>

                    </h2>

                    <p class="text-secondary mb-0">

                        Best Time:
                        <?= $player['time'] ?>

                    </p>

                </div>

            </div>

        </div>

        <?php endfor; ?>

    </div>

    <!-- LEADERBOARD TABLE -->
    <div class="card leaderboard-card">

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>

                    <tr>

                        <th>Rank</th>
                        <th>Player</th>
                        <th>Score</th>
                        <th>Wins</th>
                        <th>Best Time</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach($leaders as $index => $player):

                        $rank = $index + 1;

                        $is_me =
                        ($player['username'] === $current_user);

                        $rankClass = "rank-normal";

                        if($rank == 1) $rankClass = "rank-1";
                        if($rank == 2) $rankClass = "rank-2";
                        if($rank == 3) $rankClass = "rank-3";

                    ?>

                    <tr class="<?= $is_me ? 'you-row' : '' ?>">

                        <!-- RANK -->
                        <td>

                            <div class="rank-badge <?= $rankClass ?>">

                                <?php if($rank == 1): ?>

                                    <i class="fa-solid fa-crown"></i>

                                <?php elseif($rank == 2): ?>

                                    <i class="fa-solid fa-medal"></i>

                                <?php elseif($rank == 3): ?>

                                    <i class="fa-solid fa-award"></i>

                                <?php else: ?>

                                    <?= $rank ?>

                                <?php endif; ?>

                            </div>

                        </td>

                        <!-- PLAYER -->
                        <td>

                            <div class="d-flex align-items-center gap-3">

                                <div class="player-avatar">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                                <div>

                                    <div class="fw-bold">

                                        <?= htmlspecialchars($player['username']) ?>

                                        <?php if($is_me): ?>

                                            <span class="badge bg-primary ms-2">
                                                YOU
                                            </span>

                                        <?php endif; ?>

                                    </div>

                                    <small class="text-secondary">

                                        Sudoku Elite Player

                                    </small>

                                </div>

                            </div>

                        </td>

                        <!-- SCORE -->
                        <td class="fw-bold text-info">

                            <?= number_format($player['score']) ?>

                        </td>

                        <!-- WINS -->
                        <td>

                            <span class="badge bg-success px-3 py-2">

                                <?= $player['won'] ?> Wins

                            </span>

                        </td>

                        <!-- TIME -->
                        <td class="fw-bold">

                            <i class="fa-solid fa-clock text-warning"></i>

                            <?= $player['time'] ?>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer-note">

        <i class="fa-solid fa-rotate"></i>

        Live leaderboard powered by game stats

    </div>

</main>

</div>
</div>

</body>
</html>