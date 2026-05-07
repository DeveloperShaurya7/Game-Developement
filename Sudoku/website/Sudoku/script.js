

/* ================= VARIABLES ================= */

let board = [];
let solution = [];

let selectedCell = null;
let history = [];
let notesMode = false;
let mistakes = 0;
let time = 0;
let timerInterval;
let currentLevel = "easy";

const cells = document.querySelectorAll(".cell");

/* ================= TIMER ================= */

function updateTimer(){

    time++;

    let m = Math.floor(time / 60);
    let s = time % 60;

    document.getElementById("timer").innerText =
    String(m).padStart(2,'0') + ":" +
    String(s).padStart(2,'0');
}

/* ================= RANDOM HELPERS ================= */

function shuffle(array){

    for(let i = array.length - 1; i > 0; i--){

        let j = Math.floor(Math.random() * (i + 1));

        [array[i], array[j]] =
        [array[j], array[i]];
    }

    return array;
}

/* ================= CREATE EMPTY BOARD ================= */

function createEmptyBoard(){

    return Array.from({length:9}, () => Array(9).fill(0));
}

/* ================= VALIDATE NUMBER ================= */

function isValid(board,row,col,num){

    for(let x=0;x<9;x++){

        if(board[row][x] === num) return false;
        if(board[x][col] === num) return false;
    }

    let startRow = row - row % 3;
    let startCol = col - col % 3;

    for(let r=0;r<3;r++){

        for(let c=0;c<3;c++){

            if(board[startRow+r][startCol+c] === num)
                return false;
        }
    }

    return true;
}

/* ================= SOLVE BOARD ================= */

function solveBoard(board){

    for(let row=0; row<9; row++){

        for(let col=0; col<9; col++){

            if(board[row][col] === 0){

                let numbers =
                shuffle([1,2,3,4,5,6,7,8,9]);

                for(let num of numbers){

                    if(isValid(board,row,col,num)){

                        board[row][col] = num;

                        if(solveBoard(board))
                            return true;

                        board[row][col] = 0;
                    }
                }

                return false;
            }
        }
    }

    return true;
}

/* ================= GENERATE FULL SOLUTION ================= */

function generateSolution(){

    let newBoard = createEmptyBoard();

    solveBoard(newBoard);

    return newBoard.flat();
}

/* ================= CREATE PUZZLE ================= */

function generatePuzzle(level){

    solution = generateSolution();

    board = [...solution];

    let removeCount = 35;

    if(level === "medium") removeCount = 45;
    if(level === "hard") removeCount = 55;

    let removed = 0;

    while(removed < removeCount){

        let index = Math.floor(Math.random() * 81);

        if(board[index] !== 0){

            board[index] = 0;
            removed++;
        }
    }
}

/* ================= LOAD GAME ================= */

function loadGame(level){

    currentLevel = level;

    generatePuzzle(level);

    mistakes = 0;
    time = 0;
    history = [];

    clearInterval(timerInterval);

    timerInterval = setInterval(updateTimer,1000);

    document.getElementById("mistakes").innerText =
    "0 / 3";

    document.getElementById("levelText").innerText =
    level.charAt(0).toUpperCase() +
    level.slice(1);

    document.getElementById("timer").innerText =
    "00:00";

    cells.forEach((cell,i)=>{

        cell.className = "cell";
        cell.innerHTML = "";

        if(board[i] !== 0){

            cell.innerHTML = board[i];
            cell.classList.add("fixed");
        }

        cell.onclick = () => selectCell(i);
    });
}

/* ================= SELECT ================= */

function selectCell(index){

    const cell = cells[index];

    if(cell.classList.contains("fixed")) return;

    cells.forEach(c=>{

        c.classList.remove("selected");
        c.classList.remove("highlight");
    });

    selectedCell = {cell,index};

    cell.classList.add("selected");

    if(cell.innerText){

        highlight(cell.innerText);
    }
}

/* ================= HIGHLIGHT ================= */

function highlight(num){

    cells.forEach(c=>{

        if(c.innerText === num){

            c.classList.add("highlight");
        }
    });
}

/* ================= INPUT ================= */

function inputNumber(num){

    if(!selectedCell) return;

    let {cell,index} = selectedCell;

    history.push({
        index:index,
        prev:cell.innerHTML
    });

    if(notesMode){

        cell.innerHTML =
        `<div class="notes"><span>${num}</span></div>`;

        return;
    }

    if(solution[index] == num){

        cell.innerHTML = num;

        checkWin();

    }else{

        mistakes++;

        document.getElementById("mistakes").innerText =
        mistakes + " / 3";

        cell.classList.add("error");

        setTimeout(()=>{
            cell.classList.remove("error");
        },400);

        if(mistakes >= 3){

            alert("💀 Game Over");

            newGame();
        }
    }
}

/* ================= ERASE ================= */

function eraseCell(){

    if(!selectedCell) return;

    selectedCell.cell.innerHTML = "";
}

/* ================= NOTES ================= */

function toggleNotes(){

    notesMode = !notesMode;

    alert(
        "Notes Mode " +
        (notesMode ? "Enabled" : "Disabled")
    );
}

/* ================= HINT ================= */

function getHint(){

    if(!selectedCell) return;

    let {cell,index} = selectedCell;

    cell.innerHTML = solution[index];

    checkWin();
}

/* ================= UNDO ================= */

function undoMove(){

    const last = history.pop();

    if(!last) return;

    cells[last.index].innerHTML =
    last.prev;
}

/* ================= RESET ================= */

function resetGame(){

    loadGame(currentLevel);
}

/* ================= NEW GAME ================= */

function newGame(){

    const level =
    document.getElementById("difficulty").value;

    loadGame(level);
}

/* ================= WIN ================= */

function checkWin(){

    let won = true;

    cells.forEach((cell,i)=>{

        if(parseInt(cell.innerText) !== solution[i]){

            won = false;
        }
    });

    if(won){

        clearInterval(timerInterval);

        setTimeout(()=>{

            alert(
`🎉 Congratulations!

You solved the puzzle!

Difficulty:
${currentLevel.toUpperCase()}

Time:
${document.getElementById("timer").innerText}`
            );

        },300);
    }
}

/* ================= KEYBOARD ================= */

document.addEventListener("keydown",(e)=>{

    if(!selectedCell) return;

    const index = selectedCell.index;

    if(e.key >= 1 && e.key <= 9){

        inputNumber(parseInt(e.key));
    }

    if(
        e.key === "Backspace" ||
        e.key === "Delete"
    ){

        eraseCell();
    }

    let next = null;

    if(e.key === "ArrowRight") next = index + 1;
    if(e.key === "ArrowLeft") next = index - 1;
    if(e.key === "ArrowUp") next = index - 9;
    if(e.key === "ArrowDown") next = index + 9;

    if(next !== null && next >= 0 && next < 81){

        selectCell(next);
    }
});

/* ================= START ================= */

loadGame("easy");

