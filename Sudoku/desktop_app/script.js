class SudokuApp {
    constructor() {
        this.board = [];
        this.solution = [];
        this.selectedIdx = null;
        this.mistakes = 0;
        this.score = 0;
        this.timer = 0;
        this.timerInterval = null;
        this.difficulty = 'easy';
        this.leaderboard = JSON.parse(localStorage.getItem('sudoku_scores')) || [];

        this.init();
    }

    init() {
        this.setupBoardUI();
        this.bindEvents();
        this.newGame();
    }

    setupBoardUI() {
        const container = document.getElementById('grid-container');
        for (let i = 0; i < 81; i++) {
            const cell = document.createElement('div');
            cell.className = 'cell';
            cell.onclick = () => this.selectCell(i);
            container.appendChild(cell);
        }
    }

    newGame() {
        clearInterval(this.timerInterval);
        this.generatePuzzle();
        this.mistakes = 0;
        this.score = 0;
        this.timer = 0;
        this.updateUI();
        this.startTimer();
    }

    generatePuzzle() {
        // Core Logic: Generate a full valid board, then poke holes
        const fullGrid = this.solveSudoku(Array(81).fill(0));
        this.solution = [...fullGrid];
        
        const holes = { easy: 30, medium: 45, hard: 55 };
        this.board = [...fullGrid];
        
        for (let i = 0; i < holes[this.difficulty]; i++) {
            let idx = Math.floor(Math.random() * 81);
            while (this.board[idx] === 0) idx = Math.floor(Math.random() * 81);
            this.board[idx] = 0;
        }
        this.renderBoard();
    }

    // A simplified Sudoku generator/solver
    solveSudoku(grid) {
        const solve = (g) => {
            for (let i = 0; i < 81; i++) {
                if (g[i] === 0) {
                    const nums = [1,2,3,4,5,6,7,8,9].sort(() => Math.random() - 0.5);
                    for (let n of nums) {
                        if (this.isValid(g, i, n)) {
                            g[i] = n;
                            if (solve(g)) return true;
                            g[i] = 0;
                        }
                    }
                    return false;
                }
            }
            return true;
        };
        solve(grid);
        return grid;
    }

    isValid(grid, index, num) {
        const row = Math.floor(index / 9);
        const col = index % 9;
        for (let i = 0; i < 9; i++) {
            if (grid[row * 9 + i] === num) return false;
            if (grid[i * 9 + col] === num) return false;
            const boxRow = Math.floor(row / 3) * 3 + Math.floor(i / 3);
            const boxCol = Math.floor(col / 3) * 3 + (i % 3);
            if (grid[boxRow * 9 + boxCol] === num) return false;
        }
        return true;
    }

    renderBoard() {
        const cells = document.querySelectorAll('.cell');
        cells.forEach((cell, i) => {
            cell.innerText = this.board[i] || '';
            cell.className = 'cell' + (this.board[i] ? ' fixed' : '');
        });
    }

    selectCell(i) {
        if (this.board[i] !== 0 && document.querySelectorAll('.cell')[i].classList.contains('fixed')) {
            this.selectedIdx = null; // Can't edit fixed cells
            return;
        }
        this.selectedIdx = i;
        document.querySelectorAll('.cell').forEach(c => c.classList.remove('selected'));
        document.querySelectorAll('.cell')[i].classList.add('selected');
    }

    handleInput(num) {
        if (this.selectedIdx === null) return;
        const cell = document.querySelectorAll('.cell')[this.selectedIdx];

        if (this.solution[this.selectedIdx] === num) {
            this.board[this.selectedIdx] = num;
            cell.innerText = num;
            cell.className = 'cell';
            this.score += 100;
            this.updateUI();
            this.checkWin();
        } else {
            this.mistakes++;
            cell.classList.add('error');
            this.updateUI();
            if (this.mistakes >= 3) {
                alert("Game Over! 3 Mistakes made.");
                this.newGame();
            }
        }
    }

    updateUI() {
        document.getElementById('score').innerText = this.score.toString().padStart(4, '0');
        document.getElementById('mistakes').innerText = `${this.mistakes} / 3`;
    }

    startTimer() {
        this.timerInterval = setInterval(() => {
            this.timer++;
            const m = Math.floor(this.timer / 60).toString().padStart(2, '0');
            const s = (this.timer % 60).toString().padStart(2, '0');
            document.getElementById('timer').innerText = `${m}:${s}`;
        }, 1000);
    }

    checkWin() {
        if (!this.board.includes(0)) {
            clearInterval(this.timerInterval);
            const name = prompt("You Won! Enter your name for the leaderboard:");
            this.saveScore(name || "Anonymous", this.score);
            this.newGame();
        }
    }

    saveScore(name, score) {
        this.leaderboard.push({ name, score, date: new Date().toLocaleDateString() });
        this.leaderboard.sort((a, b) => b.score - a.score);
        this.leaderboard = this.leaderboard.slice(0, 5); // Keep top 5
        localStorage.setItem('sudoku_scores', JSON.stringify(this.leaderboard));
    }

    bindEvents() {
        document.querySelectorAll('.num').forEach(btn => {
            btn.onclick = () => this.handleInput(parseInt(btn.innerText));
        });

        document.getElementById('new-game-btn').onclick = () => this.newGame();

        document.querySelectorAll('.diff-btn').forEach(btn => {
            btn.onclick = (e) => {
                document.querySelectorAll('.diff-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.difficulty = btn.dataset.lvl;
                this.newGame();
            };
        });

        const modal = document.getElementById('leaderboard-modal');
        document.getElementById('show-leaderboard').onclick = () => {
            const list = document.getElementById('leaderboard-list');
            list.innerHTML = this.leaderboard.map(s => `<li><span>${s.name}</span><b>${s.score}</b></li>`).join('');
            modal.style.display = 'block';
        };

        document.querySelector('.close-modal').onclick = () => modal.style.display = 'none';
        
        document.getElementById('hint-btn').onclick = () => {
            if (this.selectedIdx !== null && this.board[this.selectedIdx] === 0) {
                this.handleInput(this.solution[this.selectedIdx]);
                this.score -= 50; // Penalty for hints
                this.updateUI();
            }
        };
    }
}

new SudokuApp();