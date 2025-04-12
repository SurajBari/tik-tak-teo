<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tic Tac Toe vs CPU</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .cell {
      transition: background-color 0.3s ease, transform 0.2s ease;
    }
    .winner {
      animation: pulseWinner 1s infinite alternate;
    }
    @keyframes pulseWinner {
      from { background-color: #facc15; transform: scale(1.1); }
      to { background-color: #fde68a; transform: scale(1); }
    }
    .fade-in {
      animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: scale(0.9); }
      to { opacity: 1; transform: scale(1); }
    }
    .confetti-piece {
      position: absolute;
      width: 8px;
      height: 16px;
      background-color: hsl(var(--hue), 70%, 60%);
      opacity: 0.9;
      animation: fall linear forwards;
    }
    @keyframes fall {
      0% {
        transform: translateY(-100px) rotate(0deg);
        opacity: 1;
      }
      100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
      }
    }
  </style>
</head>

<body class="bg-gradient-to-br from-indigo-100 to-purple-200 min-h-screen flex items-center justify-center relative overflow-hidden">
<!-- 🔝 Header with Toggle -->
<header class="bg-white shadow-md w-full fixed top-0 left-0 z-30">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <!-- Logo/Title -->
    <div class="text-xl font-bold text-indigo-600">🎮 Tic Tac Toe</div>

    <!-- Toggle for mobile -->
    <button id="menuToggle" class="md:hidden text-gray-700 focus:outline-none">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <!-- Nav Links -->
    <nav id="navLinks" class="hidden md:flex space-x-4 items-center">
      <a href="#" class="text-gray-700 hover:text-indigo-600 transition">Home</a>
      <a href="#" class="text-gray-700 hover:text-indigo-600 transition">About</a>
      <a href="#" class="text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-1.5 rounded transition">Login</a>
    </nav>
  </div>

  <!-- Mobile Menu -->
  <div id="mobileMenu" class="md:hidden hidden flex-col px-4 pb-3">
    <a href="#" class="py-2 text-gray-700 hover:text-indigo-600">Home</a>
    <a href="#" class="py-2 text-gray-700 hover:text-indigo-600">About</a>
    <a href="#" class="py-2 text-white bg-indigo-600 hover:bg-indigo-700 rounded text-center">Login</a>
  </div>
</header>

  <!-- 🎊 Confetti Container -->
  <div id="confetti" class="absolute inset-0 pointer-events-none z-10"></div>

  <div class="text-center space-y-6 px-4 z-20 max-w-md w-full">
    <h1 class="text-4xl md:text-5xl font-bold text-gray-800">Tic Tac Toe 🤖</h1>

    <div id="game" class="grid grid-cols-3 gap-2 mx-auto max-w-xs sm:max-w-sm md:max-w-md">
      <!-- Cells rendered here -->
    </div>

    <div id="status" class="text-lg font-semibold text-gray-700 min-h-[1.5rem]"></div>

    <button id="resetBtn" class="mt-4 px-6 py-2 bg-indigo-500 text-white rounded-lg shadow hover:bg-indigo-600 transition w-full max-w-[200px] mx-auto">
      🔄 Reset Game
    </button>
  </div>

  <script>
    const game = document.getElementById('game');
    const status = document.getElementById('status');
    const resetBtn = document.getElementById('resetBtn');
    const confetti = document.getElementById('confetti');

    let board = Array(9).fill('');
    let playerTurn = true;
    let gameActive = true;

    const PLAYER_ICON = '❌';
    const CPU_ICON = '🤖';

    const winPatterns = [
      [0,1,2], [3,4,5], [6,7,8],
      [0,3,6], [1,4,7], [2,5,8],
      [0,4,8], [2,4,6]
    ];

    function renderBoard() {
      game.innerHTML = '';
      board.forEach((cell, i) => {
        const cellDiv = document.createElement('div');
        cellDiv.className = "cell fade-in aspect-square bg-white rounded-xl shadow-lg flex items-center justify-center text-4xl font-bold cursor-pointer hover:bg-indigo-100";
        cellDiv.textContent = cell;
        cellDiv.dataset.index = i;
        if (playerTurn && !cell && gameActive) {
          cellDiv.addEventListener('click', playerMove);
        }
        game.appendChild(cellDiv);
      });
      updateStatus();
    }

    function playerMove(e) {
      const idx = e.target.dataset.index;
      if (!board[idx] && playerTurn && gameActive) {
        board[idx] = PLAYER_ICON;
        playerTurn = false;
        renderBoard();
        checkResult();
        if (gameActive) {
          setTimeout(cpuMove, 500);
        }
      }
    }

    function cpuMove() {
      const emptyIndexes = board.map((v, i) => v === '' ? i : null).filter(i => i !== null);
      if (emptyIndexes.length === 0 || !gameActive) return;

      const randomIdx = emptyIndexes[Math.floor(Math.random() * emptyIndexes.length)];
      board[randomIdx] = CPU_ICON;
      playerTurn = true;
      renderBoard();
      checkResult();
    }

    function checkResult() {
      for (let pattern of winPatterns) {
        const [a, b, c] = pattern;
        if (board[a] && board[a] === board[b] && board[a] === board[c]) {
          gameActive = false;
          highlightWinner(pattern);
          status.textContent = `${board[a]} Wins! 🎉`;
          launchConfetti();
          return;
        }
      }

      if (!board.includes('')) {
        gameActive = false;
        status.textContent = "It's a Draw! 😅";
      }
    }

    function highlightWinner(pattern) {
      pattern.forEach(idx => {
        game.children[idx].classList.add('winner');
      });
    }

    function updateStatus() {
      if (gameActive) {
        status.textContent = playerTurn ? "Your Turn ❌" : "CPU Thinking 🤖...";
      }
    }

    function resetGame() {
      board = Array(9).fill('');
      playerTurn = true;
      gameActive = true;
      confetti.innerHTML = '';
      renderBoard();
    }

    function launchConfetti() {
      for (let i = 0; i < 80; i++) {
        const piece = document.createElement('div');
        const hue = Math.floor(Math.random() * 360);
        const left = Math.random() * 100;
        const duration = 2 + Math.random() * 2;

        piece.className = 'confetti-piece';
        piece.style.left = `${left}%`;
        piece.style.setProperty('--hue', hue);
        piece.style.animationDuration = `${duration}s`;
        piece.style.transform = `rotate(${Math.random() * 360}deg)`;
        confetti.appendChild(piece);

        setTimeout(() => piece.remove(), duration * 1000);
      }
    }

    resetBtn.addEventListener('click', resetGame);

    renderBoard();
  </script>
  <!-- Existing script and confetti ends above here -->

  <footer class="absolute bottom-0 w-full text-center py-4 bg-white bg-opacity-80 backdrop-blur-sm border-t border-gray-200 z-10">
  <div class="flex flex-col md:flex-row items-center justify-center gap-4 text-sm text-gray-700">
    <span>© 2025 | Made with ❤️ by <span class="font-semibold text-indigo-600">SURAJ BARI</span></span>
    
    <div class="flex space-x-4">
      <a href="https://github.com/SurajBari" target="_blank" class="text-gray-700 hover:text-black transition">
        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
          <path d="M12 0C5.37 0 0 5.38 0 12a12 12 0 008.205 11.385c.6.11.82-.26.82-.58v-2.01c-3.338.73-4.033-1.61-4.033-1.61-.546-1.39-1.333-1.76-1.333-1.76-1.09-.75.082-.735.082-.735 1.205.085 1.84 1.24 1.84 1.24 1.07 1.835 2.807 1.305 3.495.997.108-.776.42-1.305.76-1.605-2.665-.3-5.466-1.335-5.466-5.935 0-1.31.47-2.38 1.235-3.22-.125-.3-.535-1.515.115-3.16 0 0 1.005-.32 3.3 1.23a11.5 11.5 0 016 0c2.295-1.55 3.3-1.23 3.3-1.23.65 1.645.24 2.86.12 3.16.77.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.63-5.475 5.93.43.37.81 1.1.81 2.22v3.29c0 .32.21.7.825.58A12 12 0 0024 12c0-6.62-5.38-12-12-12z"/>
        </svg>
      </a>
      <a href="https://www.linkedin.com/in/suraj-bari-595873265" target="_blank" class="text-blue-700 hover:text-blue-800 transition">
        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
          <path d="M4.98 3.5C4.98 5.43 3.43 7 1.5 7S-1.98 5.43-1.98 3.5 0.57 0 2.5 0 4.98 1.57 4.98 3.5zM.01 8.67h4.95V24H.01V8.67zM8.34 8.67h4.74v2.09h.07c.66-1.26 2.27-2.59 4.68-2.59 5 0 5.92 3.29 5.92 7.58V24H18.7v-7.62c0-1.82-.03-4.17-2.54-4.17-2.55 0-2.94 1.99-2.94 4.04V24H8.34V8.67z"/>
        </svg>
      </a>
      <a href="https://www.instagram.com/surajbarise" target="_blank" class="text-pink-500 hover:text-pink-600 transition">
        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
          <path d="M7.5 2C5 2 2 5 2 7.5v9C2 19 5 22 7.5 22h9c2.5 0 5.5-3 5.5-5.5v-9C22 5 19 2 16.5 2h-9zM12 7a5 5 0 110 10 5 5 0 010-10zm6.5-.25a1.25 1.25 0 11-2.5 0 1.25 1.25 0 012.5 0zM12 9a3 3 0 100 6 3 3 0 000-6z"/>
        </svg>
      </a>
    </div>
  </div>
</footer>
<script>
  const menuToggle = document.getElementById('menuToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  menuToggle.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });
</script>


</body>

</body>
</html>
