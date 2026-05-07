
<!-- FONT AWESOME -->
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<?php
// CURRENT PAGE
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- ================= SIDEBAR ================= -->

<nav class="col-md-3 col-lg-2 d-md-block sidebar position-fixed px-3 py-4">

    <!-- LOGO -->
    <div class="logo-section mb-4">

        <a href="index.php"
        class="logo d-flex align-items-center text-decoration-none">

            <div class="logo-icon">

                <i class="fa-solid fa-grip"></i>

            </div>

            <div>

                <h4 class="logo-text mb-0">

                    Sudoku

                </h4>

                <small class="text-secondary">

                    Elite Pro

                </small>

            </div>

        </a>

    </div>

    <hr>

    <!-- USER -->
    <div class="user-card mb-4">

        <div class="d-flex align-items-center">

            <div class="user-avatar">

                <i class="fa-solid fa-user"></i>

            </div>

            <div>

                <h6 class="mb-0 text-white">

                    <?= htmlspecialchars($_SESSION['username'] ?? 'Player') ?>

                </h6>

                <small class="text-secondary">

                    Elite Member

                </small>

            </div>

        </div>

    </div>

    <!-- MENU -->
    <ul class="nav flex-column sidebar-menu">

        <!-- DASHBOARD -->
        <li class="nav-item">

            <a href="index.php"
            class="nav-link sidebar-link
            <?= ($current_page == 'index.php') ? 'active' : '' ?>">

                <i class="fa-solid fa-chart-line"></i>

                <span>

                    Dashboard

                </span>

            </a>

        </li>

        <!-- PLAY -->
        <li class="nav-item">

            <a href="board.php"
            class="nav-link sidebar-link
            <?= ($current_page == 'board.php') ? 'active' : '' ?>">

                <i class="fa-solid fa-gamepad"></i>

                <span>

                    Play Game

                </span>

            </a>

        </li>

        <!-- LEADERBOARD -->
        <li class="nav-item">

            <a href="leaderboard.php"
            class="nav-link sidebar-link
            <?= ($current_page == 'leaderboard.php') ? 'active' : '' ?>">

                <i class="fa-solid fa-trophy"></i>

                <span>

                    Leaderboard

                </span>

            </a>

        </li>

        <!-- PROFILE -->
        <li class="nav-item">

            <a href="profile.php"
            class="nav-link sidebar-link
            <?= ($current_page == 'profile.php') ? 'active' : '' ?>">

                <i class="fa-solid fa-user"></i>

                <span>

                    Profile

                </span>

            </a>

        </li>

        <!-- SETTINGS -->
        <li class="nav-item">

            <a href="settings.php"
            class="nav-link sidebar-link
            <?= ($current_page == 'settings.php') ? 'active' : '' ?>">

                <i class="fa-solid fa-gear"></i>

                <span>

                    Settings

                </span>

            </a>

        </li>

    </ul>

    <!-- DIVIDER -->
    <div class="sidebar-divider"></div>

    <!-- EXTRA -->
    <div class="sidebar-extra">

        <div class="tip-card">

            <div class="mb-2">

                <i class="fa-solid fa-lightbulb text-warning"></i>

                <span class="fw-bold">

                    Daily Tip

                </span>

            </div>

            <small class="text-secondary">

                Focus on rows and columns before checking boxes.

            </small>

        </div>

    </div>

    <!-- LOGOUT -->
    <div class="logout-section mt-4">

        <a href="logout.php"
        class="logout-btn">

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</nav>

<!-- ================= SIDEBAR STYLE ================= -->

<style>

.sidebar{

    width:280px;
    min-height:100vh;

    background:
    linear-gradient(
    180deg,
    #111827,
    #0f172a
    );

    border-right:1px solid #1e293b;

    box-shadow:
    10px 0 40px rgba(0,0,0,0.35);

    overflow-y:auto;
    z-index:999;
}

/* LOGO */
.logo-icon{

    width:55px;
    height:55px;

    border-radius:18px;

    background:
    linear-gradient(
    135deg,
    #2563eb,
    #3b82f6
    );

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;
    font-size:22px;

    margin-right:14px;

    box-shadow:
    0 10px 25px rgba(37,99,235,0.4);
}

.logo-text{

    color:white;
    font-weight:800;
}

/* USER */
.user-card{

    background:#1e293b;

    border-radius:22px;

    padding:18px;

    border:1px solid #334155;
}

.user-avatar{

    width:50px;
    height:50px;

    border-radius:50%;

    background:
    linear-gradient(
    135deg,
    #2563eb,
    #60a5fa
    );

    display:flex;
    justify-content:center;
    align-items:center;

    color:white;
    font-size:18px;

    margin-right:14px;
}

/* MENU */
.sidebar-menu{
    gap:10px;
}

.sidebar-link{

    display:flex;
    align-items:center;

    gap:14px;

    padding:15px 18px;

    border-radius:18px;

    color:#cbd5e1;

    font-weight:600;

    transition:0.25s;

    text-decoration:none;
}

.sidebar-link i{

    width:22px;
    font-size:18px;
}

/* HOVER */
.sidebar-link:hover{

    background:#1e293b;

    color:white;

    transform:translateX(4px);
}

/* ACTIVE */
.sidebar-link.active{

    background:
    linear-gradient(
    135deg,
    #2563eb,
    #3b82f6
    );

    color:white;

    box-shadow:
    0 10px 25px rgba(37,99,235,0.35);
}

/* DIVIDER */
.sidebar-divider{

    height:1px;

    background:#334155;

    margin:28px 0;
}

/* TIP CARD */
.tip-card{

    background:#1e293b;

    border-radius:20px;

    padding:18px;

    border:1px solid #334155;
}

/* LOGOUT */
.logout-btn{

    width:100%;

    display:flex;
    align-items:center;
    justify-content:center;

    gap:10px;

    background:
    linear-gradient(
    135deg,
    #dc2626,
    #ef4444
    );

    color:white;

    padding:15px;

    border-radius:18px;

    text-decoration:none;

    font-weight:700;

    transition:0.3s;
}

.logout-btn:hover{

    transform:translateY(-2px);

    color:white;

    box-shadow:
    0 10px 25px rgba(239,68,68,0.35);
}

/* MOBILE */
@media(max-width:768px){

    .sidebar{

        width:100%;
        min-height:auto;
        position:relative !important;

        border-right:none;
        border-bottom:1px solid #1e293b;
    }

}

</style>