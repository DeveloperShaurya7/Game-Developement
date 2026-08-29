let state = {
    user: null, mode: 'solo', diff: 'medium',
    seconds: 0, timer: null, mistakes: 0,
    p1Mistakes: 0, p2Mistakes: 0, p1Turn: true,
    isNoteMode: false, selectedIdx: null,
    solution: [], board: []
};

// --- CORE SYSTEM ---
function transitionTo(viewId) {
    document.querySelectorAll('.view-section').forEach(s => s.classList.remove('active'));
    document.getElementById(viewId).classList.add('active');
    if (viewId === 'view-game') initGame();
    if (viewId === 'view-dashboard') renderDashboard();
}

function handleAuth() {
    const name = document.getElementById('username-input').value.trim().toUpperCase();
    if (name.length < 2) return showToast("CODENAME TOO SHORT", "error");
    
    let profiles = JSON.parse(localStorage.getItem('elite_sudoku_users') || '{}');
    if (!profiles[name]) profiles[name] = { wins: 0, exp: 0 };
    
    state.user = { name, ...profiles[name] };
    localStorage.setItem('elite_sudoku_users', JSON.stringify(profiles));
    showToast(`ACCESS GRANTED: ${name}`, "success");
    transitionTo('view-dashboard');
}

// --- GAME ENGINE ---
function initGame() {
    clearInterval(state.timer);
    state.seconds = 0; state.mistakes = 0;
    state.p1Mistakes = 0; state.p2Mistakes = 0; state.p1Turn = true;
    state.selectedIdx = null;
    
    state.solution = generateBoard();
    const puzzle = pluckNumbers([...state.solution], state.diff);
    
    state.board = puzzle.map(v => ({ val: v === 0 ? null : v, fixed: v !== 0, notes: [] }));
    
    document.getElementById('pvp-stats').style.display = state.mode === 'pvp' ? 'flex' : 'none';
    updateUI();
    startTimer();
    renderGrid();
}

function renderGrid() {
    const container = document.getElementById('grid');
    container.innerHTML = '';
    const selectedVal = state.selectedIdx !== null ? state.board[state.selectedIdx].val : null;

    state.board.forEach((cell, i) => {
        const div = document.createElement('div');
        div.className = 'cell';
        if (cell.fixed) div.classList.add('fixed');
        if (state.selectedIdx === i) div.classList.add('selected');
        
        // Highlights
        if (state.selectedIdx !== null) {
            const r = Math.floor(i/9), c = i%9;
            const sr = Math.floor(state.selectedIdx/9), sc = state.selectedIdx%9;
            if (r === sr || c === sc) div.classList.add('highlight');
            if (selectedVal && cell.val === selectedVal) div.classList.add('same-num');
        }

        if (cell.val) {
            div.innerText = cell.val;
            if (!cell.fixed && cell.val !== state.solution[i]) div.classList.add('conflict');
        } else {
            const nGrid = document.createElement('div');
            nGrid.style.display = 'grid';
            nGrid.style.gridTemplateColumns = 'repeat(3, 1fr)';
            nGrid.style.fontSize = '0.5rem';
            for(let n=1; n<=9; n++) {
                const s = document.createElement('span');
                s.innerText = cell.notes.includes(n) ? n : '';
                nGrid.appendChild(s);
            }
            div.appendChild(nGrid);
        }

        div.onclick = () => { state.selectedIdx = i; renderGrid(); };
        container.appendChild(div);
    });
}

function handleInput(num) {
    if (state.selectedIdx === null || state.board[state.selectedIdx].fixed) return;
    const cell = state.board[state.selectedIdx];

    if (state.isNoteMode) {
        const nIdx = cell.notes.indexOf(num);
        if (nIdx > -1) cell.notes.splice(nIdx, 1);
        else cell.notes.push(num);
    } else {
        cell.val = num;
        cell.notes = [];
        if (num !== state.solution[state.selectedIdx]) {
            if (state.mode === 'pvp') {
                if (state.p1Turn) state.p1Mistakes++; else state.p2Mistakes++;
            } else {
                state.mistakes++;
            }
            if (state.mistakes >= 3 || state.p1Mistakes >= 3 || state.p2Mistakes >= 3) return gameOver(false);
        } else if (state.mode === 'pvp') {
            state.p1Turn = !state.p1Turn; // Swap turns on valid move
        }
    }
    updateUI();
    renderGrid();
    if (state.board.every((c, i) => c.val === state.solution[i])) gameOver(true);
}

// --- HELPERS ---
function startTimer() {
    state.timer = setInterval(() => {
        state.seconds++;
        const m = Math.floor(state.seconds/60).toString().padStart(2, '0');
        const s = (state.seconds%60).toString().padStart(2, '0');
        document.getElementById('timer').innerText = `${m}:${s}`;
    }, 1000);
}

function updateUI() {
    document.getElementById('error-val').innerText = `${state.mistakes}/3`;
    document.getElementById('p1-err').innerText = state.p1Mistakes;
    document.getElementById('p2-err').innerText = state.p2Mistakes;
    document.getElementById('p1-ui').className = state.p1Turn ? 'p1-score active' : 'p1-score';
    document.getElementById('p2-ui').className = !state.p1Turn ? 'p2-score active' : 'p2-score';
    document.getElementById('turn-label').innerText = state.mode === 'solo' ? 'OPERATIVE TURN' : (state.p1Turn ? 'P1 TURN' : 'P2 TURN');
}

function gameOver(win) {
    clearInterval(state.timer);
    if (win) {
        let profiles = JSON.parse(localStorage.getItem('elite_sudoku_users'));
        profiles[state.user.name].wins++;
        localStorage.setItem('elite_sudoku_users', JSON.stringify(profiles));
        showModal("VICTORY", "Grid Integrity Restored.", "fa-crown", "BACK TO HQ", () => transitionTo('view-dashboard'));
    } else {
        showModal("CRITICAL FAILURE", "System Compromised.", "fa-skull", "RETRY", () => initGame());
    }
}

// --- SUDOKU GEN ---
function generateBoard() {
    let b = Array(81).fill(0);
    const solve = (board) => {
        let i = board.indexOf(0);
        if (i === -1) return true;
        let nums = [1,2,3,4,5,6,7,8,9].sort(()=>Math.random()-0.5);
        for (let n of nums) {
            if (checkValid(board, i, n)) {
                board[i] = n;
                if (solve(board)) return true;
                board[i] = 0;
            }
        }
        return false;
    };
    solve(b); return b;
}

function checkValid(b, i, v) {
    let r = Math.floor(i/9), c = i%9;
    for(let j=0; j<9; j++) if(b[r*9+j]===v || b[j*9+c]===v) return false;
    let rr = Math.floor(r/3)*3, cc = Math.floor(c/3)*3;
    for(let j=0; j<3; j++) for(let k=0; k<3; k++) if(b[(rr+j)*9+(cc+k)]===v) return false;
    return true;
}

function pluckNumbers(b, diff) {
    let count = diff === 'easy' ? 30 : diff === 'medium' ? 45 : 55;
    while(count > 0) {
        let i = Math.floor(Math.random()*81);
        if (b[i] !== 0) { b[i] = 0; count--; }
    }
    return b;
}

// --- MODAL & UI ---
function showModal(title, body, icon, btnText, callback) {
    const modal = document.getElementById('global-modal');
    document.getElementById('modal-title').innerText = title;
    document.getElementById('modal-body').innerText = body;
    document.getElementById('modal-icon').innerHTML = `<i class="fas ${icon} fa-4x" style="color:var(--accent)"></i>`;
    const okBtn = document.getElementById('modal-confirm');
    okBtn.innerText = btnText;
    okBtn.onclick = () => { modal.style.display = 'none'; callback(); };
    document.getElementById('modal-cancel').style.display = 'none';
    modal.style.display = 'flex';
}

function showToast(msg, type) {
    const container = document.getElementById('toast-container');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerText = msg;
    container.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

function renderDashboard() {
    document.getElementById('profile-name').innerText = state.user.name;
    document.getElementById('profile-rank').innerText = `Wins: ${state.user.wins}`;
    const lb = document.getElementById('global-leaderboard');
    const users = JSON.parse(localStorage.getItem('elite_sudoku_users'));
    lb.innerHTML = Object.entries(users)
        .sort((a,b) => b[1].wins - a[1].wins)
        .map(([name, data]) => `<div style="display:flex; justify-content:space-between; padding:10px; border-bottom:1px solid var(--border)"><span>${name}</span><b>${data.wins} WINS</b></div>`)
        .join('');
}

function setMode(m) { state.mode = m; document.getElementById('mode-solo').classList.toggle('active', m==='solo'); document.getElementById('mode-pvp').classList.toggle('active', m==='pvp'); }
function setDiff(d, b) { state.diff = d; document.querySelectorAll('.diff-btn').forEach(btn=>btn.classList.remove('active')); b.classList.add('active'); }
function toggleNotes() { state.isNoteMode = !state.isNoteMode; document.getElementById('note-btn').classList.toggle('active'); }
function logout() { state.user = null; transitionTo('view-auth'); }
function confirmReset() { if(confirm("Reset entire board?")) initGame(); }

// Keyboard Support
document.addEventListener('keydown', (e) => {
    if (state.view !== 'view-game') return;
    if (e.key >= '1' && e.key <= '9') handleInput(parseInt(e.key));
    if (e.key === 'n') toggleNotes();
    if (e.key === 'h') getHint();
});

// Init Numpad
(function(){
    const pad = document.getElementById('numpad');
    for(let i=1; i<=9; i++) {
        const b = document.createElement('button');
        b.innerText = i;
        b.onclick = () => handleInput(i);
        pad.appendChild(b);
    }
})();