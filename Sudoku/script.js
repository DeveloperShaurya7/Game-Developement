let selectedCell = null;
let mistakes = 0;
let solution = [];
let initialBoard = [];
let isNoteMode = false;
let timerInterval;
let seconds = 0;
let difficulty = 'medium';

// Initialize Game
function init() {
    const pad = document.getElementById('number-pad');
    for (let i = 1; i <= 9; i++) {
        const btn = document.createElement('button');
        btn.innerText = i;
        btn.onclick = () => handleInput(i);
        pad.appendChild(btn);
    }
    startGame();
}

function startGame(lvl = difficulty) {
    difficulty = lvl;
    clearInterval(timerInterval);
    seconds = 0;
    mistakes = 0;
    selectedCell = null;
    isNoteMode = false;
    
    document.getElementById('mistakes').innerText = `0/3`;
    document.getElementById('current-level').innerText = lvl.toUpperCase();
    document.getElementById('note-status').innerText = "OFF";
    
    solution = generateSolvedBoard();
    const puzzle = hideNumbers(solution, lvl);
    initialBoard = [...puzzle];
    
    window.gameState = puzzle.map(v => ({
        val: v === 0 ? null : v,
        notes: [],
        fixed: v !== 0
    }));

    renderGrid();
    startTimer();
}

function renderGrid() {
    const gridEl = document.getElementById('grid');
    gridEl.innerHTML = '';
    
    window.gameState.forEach((cell, i) => {
        const cellEl = document.createElement('div');
        cellEl.classList.add('cell');
        
        const row = Math.floor(i / 9), col = i % 9;
        if ((col + 1) % 3 === 0 && col < 8) cellEl.classList.add('thick-right');
        if ((row + 1) % 3 === 0 && row < 8) cellEl.classList.add('thick-bottom');

        if (cell.fixed) {
            cellEl.innerText = cell.val;
            cellEl.classList.add('fixed');
        } else if (cell.val) {
            cellEl.innerText = cell.val;
            if (cell.val !== solution[i]) cellEl.classList.add('error');
        } else {
            const notesGrid = document.createElement('div');
            notesGrid.classList.add('notes-grid');
            for (let n = 1; n <= 9; n++) {
                const s = document.createElement('span');
                s.innerText = cell.notes.includes(n) ? n : '';
                notesGrid.appendChild(s);
            }
            cellEl.appendChild(notesGrid);
        }

        cellEl.onclick = () => selectCell(cellEl, i);
        gridEl.appendChild(cellEl);
    });
}

function selectCell(el, idx) {
    document.querySelectorAll('.cell').forEach(c => c.classList.remove('selected', 'related', 'same-num'));
    selectedCell = { el, idx };
    el.classList.add('selected');

    const row = Math.floor(idx / 9), col = idx % 9;
    const val = window.gameState[idx].val;

    document.querySelectorAll('.cell').forEach((c, i) => {
        const r = Math.floor(i / 9), co = i % 9;
        if (r === row || co === col) c.classList.add('related');
        if (val && window.gameState[i].val === val) c.classList.add('same-num');
    });
}

function handleInput(num) {
    if (!selectedCell || window.gameState[selectedCell.idx].fixed) return;
    const idx = selectedCell.idx;

    if (isNoteMode) {
        const notes = window.gameState[idx].notes;
        window.gameState[idx].notes = notes.includes(num) ? notes.filter(n => n !== num) : [...notes, num];
    } else {
        window.gameState[idx].val = num;
        window.gameState[idx].notes = [];
        if (num !== solution[idx]) {
            mistakes++;
            document.getElementById('mistakes').innerText = `${mistakes}/3`;
            if (mistakes >= 3) showModal("Game Over", "You've reached the mistake limit.");
        }
    }
    renderGrid();
    checkWin();
}

function giveHint() {
    if (!selectedCell || window.gameState[selectedCell.idx].fixed) return;
    const idx = selectedCell.idx;
    window.gameState[idx].val = solution[idx];
    window.gameState[idx].notes = [];
    mistakes++; // Cost of a hint
    document.getElementById('mistakes').innerText = `${mistakes}/3`;
    renderGrid();
    checkWin();
}

document.getElementById('reset-btn').onclick = () => {
    window.gameState.forEach((c, i) => { if(!c.fixed) { c.val = null; c.notes = []; } });
    mistakes = 0;
    document.getElementById('mistakes').innerText = `0/3`;
    renderGrid();
};

function toggleNoteMode() {
    isNoteMode = !isNoteMode;
    document.getElementById('note-status').innerText = isNoteMode ? "ON" : "OFF";
}

document.addEventListener('keydown', (e) => {
    if (e.key >= '1' && e.key <= '9') handleInput(parseInt(e.key));
    if (e.key.toLowerCase() === 'n') toggleNoteMode();
});

function startTimer() {
    timerInterval = setInterval(() => {
        seconds++;
        const m = Math.floor(seconds/60).toString().padStart(2,'0');
        const s = (seconds%60).toString().padStart(2,'0');
        document.getElementById('timer').innerText = `${m}:${s}`;
    }, 1000);
}

function checkWin() {
    const win = window.gameState.every((c, i) => c.val === solution[i]);
    if (win) {
        clearInterval(timerInterval);
        showModal("Victory!", `Solved in ${document.getElementById('timer').innerText}`);
    }
}

function showModal(title, msg) {
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-msg').innerText = msg;
    document.getElementById('modal-overlay').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modal-overlay').style.display = 'none';
    startGame();
}

// Sudoku Generation Logic
function generateSolvedBoard() {
    const b = Array(81).fill(0);
    const isValid = (board, idx, val) => {
        const r = Math.floor(idx/9), c = idx%9;
        for(let i=0; i<9; i++) if(board[r*9+i]===val || board[i*9+c]===val) return false;
        const sr=Math.floor(r/3)*3, sc=Math.floor(c/3)*3;
        for(let i=0; i<3; i++) for(let j=0; j<3; j++) if(board[(sr+i)*9+(sc+j)]===val) return false;
        return true;
    };
    const solve = (board) => {
        const empty = board.indexOf(0);
        if (empty === -1) return true;
        const nums = [1,2,3,4,5,6,7,8,9].sort(() => Math.random() - 0.5);
        for (let n of nums) {
            if (isValid(board, empty, n)) {
                board[empty] = n;
                if (solve(board)) return true;
                board[empty] = 0;
            }
        }
        return false;
    };
    solve(b);
    return b;
}

function hideNumbers(board, lvl) {
    const counts = { easy: 30, medium: 45, hard: 55 };
    const b = [...board];
    let removed = 0;
    while(removed < counts[lvl]) {
        let i = Math.floor(Math.random()*81);
        if(b[i] !== 0) { b[i] = 0; removed++; }
    }
    return b;
}

init();