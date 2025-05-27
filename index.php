<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Todo - Personal Task Manager</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .app-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 900px;
            width: 100%;
            color: white;
        }

        .auth-container {
            max-width: 400px;
            margin: 0 auto;
        }

        .app-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .app-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .app-subtitle {
            font-size: 1rem;
            opacity: 0.8;
            margin-bottom: 1rem;
        }

        .date-nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 15px;
        }

        .date-nav button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .date-nav button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .current-date {
            font-size: 1.2rem;
            font-weight: 600;
            min-width: 200px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
        }

        .form-input {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        }

        .btn {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 15px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6);
        }

        .btn-secondary {
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            box-shadow: 0 4px 15px rgba(78, 205, 196, 0.4);
        }

        .btn-secondary:hover {
            box-shadow: 0 6px 20px rgba(78, 205, 196, 0.6);
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .todo-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: end;
        }

        .todo-form .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .todo-list {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 2rem;
        }

        .todo-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
        }

        .todo-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
        }

        .todo-item.completed {
            opacity: 0.6;
        }

        .todo-item.completed .todo-text {
            text-decoration: line-through;
        }

        .todo-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .todo-checkbox.checked {
            background: linear-gradient(45deg, #4ecdc4, #44a08d);
            border-color: #4ecdc4;
        }

        .todo-text {
            flex: 1;
            font-size: 1rem;
        }

        .todo-actions {
            display: flex;
            gap: 0.5rem;
        }

        .todo-actions button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.5rem;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .todo-actions button:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 15px;
            text-align: center;
        }

        .summary-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .auth-toggle {
            text-align: center;
            margin-top: 1rem;
        }

        .auth-toggle a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            cursor: pointer;
        }

        .auth-toggle a:hover {
            color: white;
        }

        .user-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            opacity: 0.8;
        }

        .error {
            background: rgba(255, 107, 107, 0.2);
            border: 1px solid rgba(255, 107, 107, 0.5);
            color: #ff6b6b;
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .success {
            background: rgba(78, 205, 196, 0.2);
            border: 1px solid rgba(78, 205, 196, 0.5);
            color: #4ecdc4;
            padding: 1rem;
            border-radius: 15px;
            margin-bottom: 1rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .app-container {
                padding: 1rem;
            }
            
            .todo-form {
                flex-direction: column;
            }
            
            .date-nav {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .current-date {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Authentication Section -->
        <div id="authSection" class="auth-container">
            <div class="app-header">
                <h1 class="app-title"><i class="fas fa-calendar-check"></i> Daily Todo</h1>
                <p class="app-subtitle">Your personal task management companion</p>
            </div>

            <!-- Login Form -->
            <div id="loginForm">
                <h2 style="text-align: center; margin-bottom: 2rem;">Welcome Back!</h2>
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input type="text" id="loginUsername" class="form-input" placeholder="Enter your username or email">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="loginPassword" class="form-input" placeholder="Enter your password">
                </div>
                <button onclick="login()" class="btn" style="width: 100%;">Sign In</button>
                <div class="auth-toggle">
                    <p>Don't have an account? <a onclick="showRegister()">Sign up here</a></p>
                </div>
            </div>

            <!-- Register Form -->
            <div id="registerForm" style="display: none;">
                <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" id="regUsername" class="form-input" placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" id="regEmail" class="form-input" placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" id="regPassword" class="form-input" placeholder="Create a password">
                </div>
                <button onclick="register()" class="btn" style="width: 100%;">Create Account</button>
                <div class="auth-toggle">
                    <p>Already have an account? <a onclick="showLogin()">Sign in here</a></p>
                </div>
            </div>
        </div>

        <!-- Main App Section -->
        <div id="appSection" style="display: none;">
            <div class="app-header">
                <h1 class="app-title"><i class="fas fa-calendar-check"></i> Daily Todo</h1>
                <div class="user-info">
                    <span>Welcome, <strong id="username"></strong>!</span>
                    <button onclick="logout()" class="btn btn-small">Logout</button>
                </div>
            </div>

            <!-- Date Navigation -->
            <div class="date-nav">
                <button onclick="changeDate(-1)"><i class="fas fa-chevron-left"></i> Previous</button>
                <div class="current-date" id="currentDate"></div>
                <button onclick="changeDate(1)">Next <i class="fas fa-chevron-right"></i></button>
                <button onclick="goToToday()" class="btn-secondary">Today</button>
            </div>

            <!-- Summary Cards -->
            <div class="summary-section" id="summarySection">
                <div class="summary-card">
                    <div class="summary-number" id="totalTasks">0</div>
                    <div class="summary-label">Total Tasks</div>
                </div>
                <div class="summary-card">
                    <div class="summary-number" id="completedTasks">0</div>
                    <div class="summary-label">Completed</div>
                </div>
                <div class="summary-card">
                    <div class="summary-number" id="pendingTasks">0</div>
                    <div class="summary-label">Pending</div>
                </div>
            </div>

            <!-- Add Todo Form -->
            <div class="todo-form">
                <div class="form-group">
                    <label class="form-label">What do you need to do today?</label>
                    <input type="text" id="todoInput" class="form-input" placeholder="Enter your task...">
                </div>
                <button onclick="addTodo()" class="btn">
                    <i class="fas fa-plus"></i> Add Task
                </button>
            </div>

            <!-- Todo List -->
            <div class="todo-list" id="todoList"></div>
        </div>

        <!-- Loading and Messages -->
        <div id="loading" class="loading" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i> Loading...
        </div>
        <div id="message" style="display: none;"></div>
    </div>

    <script>
        let currentUser = null;
        let currentDate = new Date().toISOString().split('T')[0];
        let authToken = null;
        let isDemo = false;

        // Initialize app
        document.addEventListener('DOMContentLoaded', () => {
            // Check if localStorage is available (won't work in Claude artifacts)
            try {
                authToken = localStorage.getItem('authToken');
            } catch (e) {
                console.log('localStorage not available');
            }
            
            checkAuth();
            
            // Add enter key support
            document.getElementById('todoInput').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') addTodo();
            });
            
            document.getElementById('loginPassword').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') login();
            });
            
            document.getElementById('regPassword').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') register();
            });
        });

        // Authentication functions
        async function checkAuth() {
            if (!authToken) {
                showAuth();
                return;
            }

            try {
                const response = await apiRequest('api.php?action=user');
                if (response.success) {
                    currentUser = response.user;
                    showApp();
                } else {
                    try {
                        localStorage.removeItem('authToken');
                    } catch (e) {
                        console.log('localStorage not available');
                    }
                    authToken = null;
                    showAuth();
                }
            } catch (error) {
                console.error('Auth check failed:', error);
                showAuth();
            }
        }

        function showAuth() {
            document.getElementById('authSection').style.display = 'block';
            document.getElementById('appSection').style.display = 'none';
        }

        function showApp() {
            document.getElementById('authSection').style.display = 'none';
            document.getElementById('appSection').style.display = 'block';
            document.getElementById('username').textContent = currentUser ? currentUser.username : 'Demo User';
            updateDateDisplay();
            loadTodos();
        }

        function showLogin() {
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('registerForm').style.display = 'none';
        }

        function showRegister() {
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('registerForm').style.display = 'block';
        }

        function skipAuth() {
            isDemo = true;
            currentUser = { username: 'Demo User', id: 'demo' };
            showMessage('Welcome to Demo Mode! Your data will be stored temporarily.', 'success');
            setTimeout(() => showApp(), 1500);
        }

        async function login() {
            const username = document.getElementById('loginUsername').value;
            const password = document.getElementById('loginPassword').value;

            if (!username || !password) {
                showMessage('Please fill in all fields', 'error');
                return;
            }

            try {
                showLoading(true);
                const response = await apiRequest('api.php?action=login', {
                    method: 'POST',
                    body: JSON.stringify({ username, password })
                });

                if (response.success) {
                    currentUser = response.user;
                    authToken = response.token;
                    try {
                        localStorage.setItem('authToken', authToken);
                    } catch (e) {
                        console.log('localStorage not available');
                    }
                    showMessage('Login successful!', 'success');
                    setTimeout(() => showApp(), 1000);
                } else {
                    showMessage(response.error, 'error');
                }
            } catch (error) {
                showMessage('Network error occurred', 'error');
            } finally {
                showLoading(false);
            }
        }

        async function register() {
            const username = document.getElementById('regUsername').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;

            if (!username || !email || !password) {
                showMessage('Please fill in all fields', 'error');
                return;
            }

            try {
                showLoading(true);
                const response = await apiRequest('api.php?action=register', {
                    method: 'POST',
                    body: JSON.stringify({ username, email, password })
                });

                if (response.success) {
                    currentUser = response.user;
                    authToken = response.token;
                    try {
                        localStorage.setItem('authToken', authToken);
                    } catch (e) {
                        console.log('localStorage not available');
                    }
                    showMessage('Account created successfully!', 'success');
                    setTimeout(() => showApp(), 1000);
                } else {
                    showMessage(response.error, 'error');
                }
            } catch (error) {
                showMessage('Network error occurred', 'error');
            } finally {
                showLoading(false);
            }
        }

        async function logout() {
            try {
                await apiRequest('api.php?action=logout', { method: 'POST' });
            } catch (error) {
                console.error('Logout error:', error);
            }
            
            currentUser = null;
            authToken = null;
            try {
                localStorage.removeItem('authToken');
            } catch (e) {
                console.log('localStorage not available');
            }
            showAuth();
            showMessage('Logged out successfully!', 'success');
        }

        // Date functions
        function updateDateDisplay() {
            const date = new Date(currentDate);
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            document.getElementById('currentDate').textContent = date.toLocaleDateString('en-US', options);
        }

        function changeDate(direction) {
            const date = new Date(currentDate);
            date.setDate(date.getDate() + direction);
            currentDate = date.toISOString().split('T')[0];
            updateDateDisplay();
            loadTodos();
        }

        function goToToday() {
            currentDate = new Date().toISOString().split('T')[0];
            updateDateDisplay();
            loadTodos();
        }

        // Todo functions
        async function loadTodos() {
            try {
                const response = await apiRequest(`api.php?action=todos&date=${currentDate}`);
                if (response.success) {
                    renderTodos(response.todos);
                    updateSummary(response.todos);
                } else {
                    showMessage('Error loading todos', 'error');
                }
            } catch (error) {
                console.error('Load todos error:', error);
                showMessage('Error loading todos', 'error');
            }
        }

        function renderTodos(todos) {
            const todoList = document.getElementById('todoList');
            
            if (todos.length === 0) {
                todoList.innerHTML = `
                    <div style="text-align: center; padding: 2rem; opacity: 0.7;">
                        <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>No tasks for this day yet.</p>
                        <p>Add a task above to get started!</p>
                    </div>
                `;
                return;
            }

            todoList.innerHTML = todos.map((todo) => `
                <div class="todo-item ${todo.completed == 1 ? 'completed' : ''}">
                    <div class="todo-checkbox ${todo.completed == 1 ? 'checked' : ''}" onclick="toggleTodo(${todo.id})">
                        ${todo.completed == 1 ? '<i class="fas fa-check"></i>' : ''}
                    </div>
                    <div class="todo-text">${escapeHtml(todo.text)}</div>
                    <div class="todo-actions">
                        <button onclick="editTodo(${todo.id}, '${escapeHtml(todo.text).replace(/'/g, "\\'")}')" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteTodo(${todo.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        async function addTodo() {
            const input = document.getElementById('todoInput');
            const text = input.value.trim();

            if (!text) {
                showMessage('Please enter a task', 'error');
                return;
            }

            try {
                const response = await apiRequest('api.php?action=todos', {
                    method: 'POST',
                    body: JSON.stringify({
                        text: text,
                        date: currentDate
                    })
                });

                if (response.success) {
                    input.value = '';
                    loadTodos();
                    showMessage('Task added successfully!', 'success');
                } else {
                    showMessage(response.error || 'Error adding task', 'error');
                }
            } catch (error) {
                console.error('Add todo error:', error);
                showMessage('Error adding task', 'error');
            }
        }

        async function toggleTodo(todoId) {
            try {
                const response = await apiRequest(`api.php?action=todo&id=${todoId}`, {
                    method: 'PUT',
                    body: JSON.stringify({
                        completed: 'toggle'
                    })
                });

                if (response.success) {
                    loadTodos();
                } else {
                    showMessage('Error updating task', 'error');
                }
            } catch (error) {
                console.error('Toggle todo error:', error);
                showMessage('Error updating task', 'error');
            }
        }

        async function editTodo(todoId, currentText) {
            const newText = prompt('Edit task:', currentText);
            if (newText !== null && newText.trim()) {
                try {
                    const response = await apiRequest(`api.php?action=todo&id=${todoId}`, {
                        method: 'PUT',
                        body: JSON.stringify({
                            text: newText.trim()
                        })
                    });

                    if (response.success) {
                        loadTodos();
                        showMessage('Task updated!', 'success');
                    } else {
                        showMessage('Error updating task', 'error');
                    }
                } catch (error) {
                    showMessage('Error updating task', 'error');
                }
            }
        }

        async function deleteTodo(todoId) {
            if (confirm('Are you sure you want to delete this task?')) {
                try {
                    const response = await apiRequest(`api.php?action=todo&id=${todoId}`, {
                        method: 'DELETE'
                    });

                    if (response.success) {
                        loadTodos();
                        showMessage('Task deleted!', 'success');
                    } else {
                        showMessage('Error deleting task', 'error');
                    }
                } catch (error) {
                    showMessage('Error deleting task', 'error');
                }
            }
        }

        function updateSummary(todos) {
            const total = todos.length;
            const completed = todos.filter(todo => todo.completed == 1).length;
            const pending = total - completed;

            document.getElementById('totalTasks').textContent = total;
            document.getElementById('completedTasks').textContent = completed;
            document.getElementById('pendingTasks').textContent = pending;
        }

        // Utility functions
        function showMessage(message, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = message;
            messageEl.className = type;
            messageEl.style.display = 'block';
            
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 3000);
        }

        function showLoading(show) {
            document.getElementById('loading').style.display = show ? 'block' : 'none';
        }

        async function apiRequest(url, options = {}) {
            const config = {
                headers: {
                    'Content-Type': 'application/json'
                },
                ...options
            };

            if (authToken) {
                config.headers['Authorization'] = `Bearer ${authToken}`;
            }

            const response = await fetch(url, config);
            const data = await response.json();
            
            return data;
        }
    </script>
</body>
</html>