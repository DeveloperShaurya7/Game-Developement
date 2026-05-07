<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: authentication/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Player';

$game_id = 0;

$checkGame = mysqli_query($conn,"
    SELECT id
    FROM game_stats
    WHERE user_id='$user_id'
    ORDER BY id DESC
    LIMIT 1
");

if(mysqli_num_rows($checkGame) > 0){

    $game = mysqli_fetch_assoc($checkGame);
    $game_id = $game['id'];

}else{

    mysqli_query($conn,"
        INSERT INTO game_stats
        (
            user_id,
            result,
            completion_time,
            time_taken,
            difficulty,
            mistakes,
            score,
            status
        )
        VALUES
        (
            '$user_id',
            'playing',
            '00:00',
            '00:00',
            'easy',
            0,
            0,
            'playing'
        )
    ");

    $game_id = mysqli_insert_id($conn);

}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Sudoku Elite Pro</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

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

/* HERO */

.hero{
    background:
    linear-gradient(
    135deg,
    #2563eb,
    #1d4ed8,
    #0f172a
    );

    border-radius:28px;
    padding:30px;
    margin-bottom:25px;

    box-shadow:
    0 20px 50px rgba(0,0,0,0.35);
}

/* GAME */

.game-layout{
    display:grid;
    grid-template-columns:1fr 360px;
    gap:25px;
}

.game-card,
.control-panel{
    background:#1e293b;
    border-radius:28px;
    padding:25px;

    box-shadow:
    0 10px 40px rgba(0,0,0,0.35);
}

/* BOARD */

.board-wrapper{
    display:flex;
    justify-content:center;
}

.sudoku-grid{
    width:100%;
    max-width:650px;
    aspect-ratio:1;

    display:grid;
    grid-template-columns:repeat(9,1fr);

    border:4px solid #2563eb;
    border-radius:20px;

    overflow:hidden;
}

.cell{
    border:1px solid #334155;
    display:flex;
    align-items:center;
    justify-content:center;

    background:#0f172a;

    font-size:24px;
    font-weight:700;

    cursor:pointer;
    transition:0.2s;

    user-select:none;
}

.cell:hover{
    background:#1e40af;
}

.cell.fixed{
    background:#e2e8f0;
    color:#111827;
}

.cell.selected{
    background:#2563eb;
}

.cell.highlight{
    background:#1d4ed8;
}

.cell.same{
    background:#0ea5e9;
}

.cell.error{
    background:#ef4444;
    animation:shake 0.2s linear 2;
}

.cell.notes{
    font-size:12px;
    color:#cbd5e1;
}

.cell:nth-child(3n){
    border-right:3px solid #2563eb;
}

.cell:nth-child(n+19):nth-child(-n+27),
.cell:nth-child(n+46):nth-child(-n+54){
    border-bottom:3px solid #2563eb;
}

/* STATS */

.stats{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:25px;
}

.stat-box{
    background:#0f172a;
    padding:15px;
    border-radius:18px;
    flex:1;
    min-width:110px;
    text-align:center;
}

.stat-box h6{
    color:#94a3b8;
    font-size:12px;
}

.stat-box span{
    font-size:22px;
    font-weight:700;
}

/* BUTTONS */

.control-btn{
    border:none;
    border-radius:16px;
    padding:14px;
    font-weight:700;
    transition:0.3s;
}

.control-btn:hover{
    transform:translateY(-3px);
}

.number-btn{
    height:60px;
    border-radius:14px;
    font-size:22px;
    font-weight:700;
}

/* MODAL */

.modal-content{
    background:#1e293b;
    color:white;
    border-radius:25px;
}

/* ANIMATION */

@keyframes shake{

    0%{transform:translateX(0);}
    25%{transform:translateX(-4px);}
    50%{transform:translateX(4px);}
    75%{transform:translateX(-4px);}
    100%{transform:translateX(0);}

}

@media(max-width:1200px){

    .game-layout{
        grid-template-columns:1fr;
    }

}

@media(max-width:768px){

    .main-content{
        padding:15px;
    }

    .cell{
        font-size:18px;
    }

}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<?php include 'sidebar.php'; ?>

<main class="col-md-9 ms-sm-auto col-lg-10 main-content min-vh-100">

    <!-- HERO -->

    <div class="hero">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <h1 class="fw-bold">

                    <i class="fa-solid fa-brain"></i>
                    Sudoku Elite Pro

                </h1>

                <p class="text-light mb-0">

                    Advanced Sudoku Experience

                </p>

            </div>

            <div class="bg-dark px-4 py-3 rounded-4">

                <i class="fa-solid fa-user"></i>

                <?= htmlspecialchars($username) ?>

            </div>

        </div>

    </div>

    <!-- GAME -->

    <div class="game-layout">

        <!-- LEFT -->

        <div class="game-card">

            <!-- STATS -->

            <div class="stats">

                <div class="stat-box">

                    <h6>TIME</h6>

                    <span id="timer">
                        00:00
                    </span>

                </div>

                <div class="stat-box">

                    <h6>MISTAKES</h6>

                    <span id="mistakes">
                        0 / 3
                    </span>

                </div>

                <div class="stat-box">

                    <h6>SCORE</h6>

                    <span id="score">
                        5000
                    </span>

                </div>

                <div class="stat-box">

                    <h6>HINTS</h6>

                    <span id="hints">
                        3
                    </span>

                </div>

            </div>

            <!-- BOARD -->

            <div class="board-wrapper">

                <div id="grid"
                class="sudoku-grid"></div>

            </div>

        </div>

        <!-- RIGHT -->

        <div class="control-panel">

            <h3 class="fw-bold mb-4">

                <i class="fa-solid fa-sliders"></i>
                Controls

            </h3>

            <!-- LEVEL -->

            <select id="difficulty"
            class="form-select bg-dark text-white border-0 rounded-4 mb-4 p-3">

                <option value="easy">
                    Easy
                </option>

                <option value="medium">
                    Medium
                </option>

                <option value="hard">
                    Hard
                </option>

            </select>

            <!-- NEW -->

            <button class="btn btn-success w-100 control-btn mb-4"
            onclick="newGame()">

                <i class="fa-solid fa-plus"></i>
                New Game

            </button>

            <!-- NUMBERS -->

            <div class="row g-2 mb-4">

                <?php for($i=1;$i<=9;$i++): ?>

                <div class="col-4">

                    <button
                    class="btn btn-outline-light w-100 number-btn"
                    onclick="inputNumber(<?= $i ?>)">

                        <?= $i ?>

                    </button>

                </div>

                <?php endfor; ?>

            </div>

            <!-- CONTROLS -->

            <div class="d-grid gap-3">

                <button class="btn btn-warning control-btn"
                onclick="eraseCell()">

                    <i class="fa-solid fa-eraser"></i>
                    Erase

                </button>

                <button class="btn btn-info control-btn"
                onclick="getHint()">

                    <i class="fa-solid fa-lightbulb"></i>
                    Hint

                </button>

                <button class="btn btn-primary control-btn"
                onclick="toggleNotes()">

                    <i class="fa-solid fa-pencil"></i>
                    Notes Mode

                </button>

                <button class="btn btn-dark control-btn"
                onclick="undoMove()">

                    <i class="fa-solid fa-rotate-left"></i>
                    Undo

                </button>

                <button class="btn btn-secondary control-btn"
                onclick="resetGame()">

                    <i class="fa-solid fa-arrows-rotate"></i>
                    Reset

                </button>

            </div>

        </div>

    </div>

</main>

</div>
</div>

<!-- MODAL -->

<div class="modal fade"
id="gameModal"
tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0">

            <div class="modal-body text-center p-5">

                <h2 id="modalTitle"
                class="fw-bold mb-3"></h2>

                <p id="modalMessage"
                class="text-secondary"></p>

                <button class="btn btn-primary px-4 rounded-4"
                data-bs-dismiss="modal">

                    Continue

                </button>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

let board = [];
let solution = [];
let initialBoard = [];

let selectedCell = null;

let mistakes = 0;
let seconds = 0;
let score = 5000;
let hints = 3;

let notesMode = false;

let timerInterval;

let moveHistory = [];

const grid =
document.getElementById("grid");

/* CREATE */

function createEmptyBoard(){

    return Array.from({length:9},
    () => Array(9).fill(0));

}

/* SHUFFLE */

function shuffle(arr){

    return arr.sort(() =>
    Math.random() - 0.5);

}

/* VALID */

function isValid(board,row,col,num){

    for(let x=0;x<9;x++){

        if(board[row][x] == num) return false;
        if(board[x][col] == num) return false;

    }

    const startRow =
    row - row % 3;

    const startCol =
    col - col % 3;

    for(let i=0;i<3;i++){

        for(let j=0;j<3;j++){

            if(board[startRow+i][startCol+j] == num){

                return false;

            }

        }

    }

    return true;

}

/* SOLVE */

function solve(board){

    for(let row=0;row<9;row++){

        for(let col=0;col<9;col++){

            if(board[row][col] == 0){

                let nums =
                shuffle([1,2,3,4,5,6,7,8,9]);

                for(let num of nums){

                    if(isValid(board,row,col,num)){

                        board[row][col] = num;

                        if(solve(board)){

                            return true;

                        }

                        board[row][col] = 0;

                    }

                }

                return false;

            }

        }

    }

    return true;

}

/* GENERATE */

function generatePuzzle(level){

    let newBoard =
    createEmptyBoard();

    solve(newBoard);

    solution =
    JSON.parse(JSON.stringify(newBoard));

    let removeCount = 35;

    if(level == "medium"){

        removeCount = 45;

    }

    if(level == "hard"){

        removeCount = 55;

    }

    while(removeCount > 0){

        let row =
        Math.floor(Math.random()*9);

        let col =
        Math.floor(Math.random()*9);

        if(newBoard[row][col] != 0){

            newBoard[row][col] = 0;

            removeCount--;

        }

    }

    board =
    JSON.parse(JSON.stringify(newBoard));

    initialBoard =
    JSON.parse(JSON.stringify(newBoard));

}

/* RENDER */

function renderBoard(){

    grid.innerHTML = "";

    for(let row=0;row<9;row++){

        for(let col=0;col<9;col++){

            const cell =
            document.createElement("div");

            cell.classList.add("cell");

            cell.dataset.row = row;
            cell.dataset.col = col;

            const value =
            board[row][col];

            if(value !== 0){

                cell.textContent = value;

                if(initialBoard[row][col] !== 0){

                    cell.classList.add("fixed");

                }

            }

            cell.addEventListener("click",() => {

                selectCell(cell,row,col);

            });

            grid.appendChild(cell);

        }

    }

}

/* SELECT */

function selectCell(cell,row,col){

    document.querySelectorAll(".cell")
    .forEach(c => {

        c.classList.remove(
            "selected",
            "highlight",
            "same"
        );

    });

    selectedCell = cell;

    cell.classList.add("selected");

    document.querySelectorAll(".cell")
    .forEach(c => {

        if(
            c.dataset.row == row ||
            c.dataset.col == col
        ){

            c.classList.add("highlight");

        }

        if(
            c.textContent ==
            cell.textContent &&
            cell.textContent != ""
        ){

            c.classList.add("same");

        }

    });

}

/* INPUT */

function inputNumber(num){

    if(!selectedCell) return;

    if(selectedCell.classList.contains("fixed")){

        return;

    }

    const row =
    selectedCell.dataset.row;

    const col =
    selectedCell.dataset.col;

    moveHistory.push({
        row,
        col,
        value:board[row][col]
    });

    if(solution[row][col] == num){

        board[row][col] = num;

        selectedCell.textContent = num;

        selectedCell.classList.remove("error");

        updateScore();

        animateCell(selectedCell);

        checkWin();

    }else{

        mistakes++;

        score -= 200;

        document.getElementById("mistakes")
        .innerText =
        mistakes + " / 3";

        selectedCell.classList.add("error");

        navigator.vibrate?.(200);

        setTimeout(() => {

            selectedCell.classList.remove("error");

        },500);

        if(mistakes >= 3){

            gameOver();

        }

    }

    updateScore();

    saveProgress();

}

/* SCORE */

function updateScore(){

    document.getElementById("score")
    .innerText = score;

}

/* HINT */

function getHint(){

    if(hints <= 0){

        showModal(
            "No Hints Left",
            "All hints used."
        );

        return;

    }

    for(let row=0;row<9;row++){

        for(let col=0;col<9;col++){

            if(board[row][col] == 0){

                board[row][col] =
                solution[row][col];

                hints--;

                document.getElementById("hints")
                .innerText = hints;

                renderBoard();

                return;

            }

        }

    }

}

/* NOTES */

function toggleNotes(){

    notesMode = !notesMode;

    showModal(
        "Notes Mode",
        notesMode
        ? "Notes enabled"
        : "Notes disabled"
    );

}

/* ERASE */

function eraseCell(){

    if(!selectedCell) return;

    if(selectedCell.classList.contains("fixed")){

        return;

    }

    const row =
    selectedCell.dataset.row;

    const col =
    selectedCell.dataset.col;

    board[row][col] = 0;

    selectedCell.textContent = "";

}

/* UNDO */

function undoMove(){

    if(moveHistory.length == 0) return;

    const move =
    moveHistory.pop();

    board[move.row][move.col] =
    move.value;

    renderBoard();

}

/* RESET */

function resetGame(){

    board =
    JSON.parse(JSON.stringify(initialBoard));

    mistakes = 0;

    renderBoard();

}

/* TIMER */

function startTimer(){

    clearInterval(timerInterval);

    seconds = 0;

    timerInterval = setInterval(() => {

        seconds++;

        score--;

        updateScore();

        const mins =
        String(Math.floor(seconds / 60))
        .padStart(2,'0');

        const secs =
        String(seconds % 60)
        .padStart(2,'0');

        document.getElementById("timer")
        .innerText =
        `${mins}:${secs}`;

    },1000);

}

/* CHECK */

function checkWin(){

    for(let row=0;row<9;row++){

        for(let col=0;col<9;col++){

            if(board[row][col] == 0){

                return;

            }

        }

    }

    clearInterval(timerInterval);

    showModal(
        "🎉 Victory!",
        `Amazing! Score: ${score}`
    );

    saveCompletedGame();

}

/* OVER */

function gameOver(){

    clearInterval(timerInterval);

    showModal(
        "❌ Game Over",
        "You reached 3 mistakes."
    );

}

/* MODAL */

function showModal(title,message){

    document.getElementById("modalTitle")
    .innerText = title;

    document.getElementById("modalMessage")
    .innerText = message;

    new bootstrap.Modal(
        document.getElementById("gameModal")
    ).show();

}

/* SAVE */

function saveProgress(){

    fetch("save_game.php",{

        method:"POST",

        headers:{
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        `game_id=<?= $game_id ?>`+
        `&difficulty=${document.getElementById("difficulty").value}`+
        `&time_taken=${document.getElementById("timer").innerText}`+
        `&mistakes=${mistakes}`+
        `&score=${score}`+
        `&status=playing`

    });

}

/* COMPLETE */

function saveCompletedGame(){

    fetch("save_game.php",{

        method:"POST",

        headers:{
            "Content-Type":
            "application/x-www-form-urlencoded"
        },

        body:
        `game_id=<?= $game_id ?>`+
        `&difficulty=${document.getElementById("difficulty").value}`+
        `&time_taken=${document.getElementById("timer").innerText}`+
        `&mistakes=${mistakes}`+
        `&score=${score}`+
        `&status=completed`

    });

}

/* ANIMATION */

function animateCell(cell){

    cell.style.transform =
    "scale(1.15)";

    setTimeout(() => {

        cell.style.transform =
        "scale(1)";

    },150);

}

/* NEW GAME */

function newGame(){

    mistakes = 0;
    score = 5000;
    hints = 3;

    document.getElementById("mistakes")
    .innerText = "0 / 3";

    document.getElementById("hints")
    .innerText = "3";

    updateScore();

    generatePuzzle(
        document.getElementById("difficulty").value
    );

    renderBoard();

    startTimer();

}

/* KEYBOARD */

document.addEventListener("keydown",(e)=>{

    if(e.key >= 1 && e.key <= 9){

        inputNumber(parseInt(e.key));

    }

    if(
        e.key === "Backspace" ||
        e.key === "Delete"
    ){

        eraseCell();

    }

});

/* START */

window.onload = () => {

    newGame();

};

</script>

</body>
</html>