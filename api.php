<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
$db_host = 'localhost';
$db_name = 'daily_todo';
$db_user = 'root';
$db_pass = '';

// Database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// Initialize database tables
initializeDatabase($pdo);

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

// Authentication middleware
$user = null;
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? 
              getallheaders()['Authorization'] ?? 
              apache_request_headers()['Authorization'] ?? '';

// Also try to get from different header formats
if (empty($authHeader)) {
    $authHeader = $_SERVER['HTTP_X_AUTHORIZATION'] ?? '';
}

if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
    $token = substr($authHeader, 7);
    $user = validateToken($pdo, $token);
}

// Debug logging (remove in production)
error_log("Auth Header: " . $authHeader);
if ($user) {
    error_log("User authenticated: " . $user['username']);
} else {
    error_log("No user authenticated");
}

// Route handler
try {
    switch ($action) {
        case 'register':
            if ($method === 'POST') {
                handleRegister($pdo, $input);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;

        case 'login':
            if ($method === 'POST') {
                handleLogin($pdo, $input);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;

        case 'logout':
            if ($method === 'POST') {
                handleLogout($pdo, $user);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;

        case 'user':
            if ($method === 'GET') {
                handleGetUser($user);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;

        case 'todos':
            if (!$user) {
                http_response_code(401);
                echo json_encode([
                    'success' => false, 
                    'error' => 'Authentication required',
                    'debug' => [
                        'auth_header' => $authHeader,
                        'has_token' => !empty($token ?? ''),
                        'method' => $method
                    ]
                ]);
                break;
            }

            if ($method === 'GET') {
                $date = $_GET['date'] ?? date('Y-m-d');
                handleGetTodos($pdo, $user['id'], $date);
            } elseif ($method === 'POST') {
                handleCreateTodo($pdo, $user['id'], $input);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;

        case 'todo':
            if (!$user) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Authentication required']);
                break;
            }

            $todoId = $_GET['id'] ?? null;
            if (!$todoId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Todo ID required']);
                break;
            }

            if ($method === 'PUT') {
                handleUpdateTodo($pdo, $user['id'], $todoId, $input);
            } elseif ($method === 'DELETE') {
                handleDeleteTodo($pdo, $user['id'], $todoId);
            } else {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error']);
    error_log("API Error: " . $e->getMessage());
}

// Database initialization
function initializeDatabase($pdo) {
    try {
        // Users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Auth tokens table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS auth_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(255) UNIQUE NOT NULL,
                expires_at TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");

        // Todos table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS todos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                text TEXT NOT NULL,
                completed BOOLEAN DEFAULT FALSE,
                todo_date DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_date (user_id, todo_date)
            )
        ");

        // Clean expired tokens periodically
        $pdo->exec("DELETE FROM auth_tokens WHERE expires_at < NOW()");
        
    } catch (PDOException $e) {
        error_log("Database initialization error: " . $e->getMessage());
    }
}

// Authentication functions
function handleRegister($pdo, $input) {
    $username = trim($input['username'] ?? '');
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'All fields are required']);
        return;
    }

    if (strlen($username) < 3) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username must be at least 3 characters']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        return;
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']);
        return;
    }

    try {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(['success' => false, 'error' => 'Username or email already exists']);
            return;
        }

        // Create user
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $passwordHash]);
        
        $userId = $pdo->lastInsertId();
        
        // Generate auth token
        $token = generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $pdo->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $token, $expiresAt]);

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $userId,
                'username' => $username,
                'email' => $email
            ],
            'token' => $token
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Registration failed']);
        error_log("Registration error: " . $e->getMessage());
    }
}

function handleLogin($pdo, $input) {
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';

    if (empty($username) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Username and password are required']);
        return;
    }

    try {
        // Find user by username or email
        $stmt = $pdo->prepare("SELECT id, username, email, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
            return;
        }

        // Generate auth token
        $token = generateToken();
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $pdo->prepare("INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $token, $expiresAt]);

        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ],
            'token' => $token
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Login failed']);
        error_log("Login error: " . $e->getMessage());
    }
}

function handleLogout($pdo, $user) {
    if (!$user) {
        echo json_encode(['success' => true, 'message' => 'Already logged out']);
        return;
    }

    try {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if ($authHeader && strpos($authHeader, 'Bearer ') === 0) {
            $token = substr($authHeader, 7);
            $stmt = $pdo->prepare("DELETE FROM auth_tokens WHERE token = ?");
            $stmt->execute([$token]);
        }

        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Logout failed']);
        error_log("Logout error: " . $e->getMessage());
    }
}

function handleGetUser($user) {
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Authentication required']);
        return;
    }

    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ]
    ]);
}

function validateToken($pdo, $token) {
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.email 
            FROM users u 
            JOIN auth_tokens t ON u.id = t.user_id 
            WHERE t.token = ? AND t.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Token validation error: " . $e->getMessage());
        return null;
    }
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

// Todo functions
function handleGetTodos($pdo, $userId, $date) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, text, completed, todo_date, created_at, updated_at 
            FROM todos 
            WHERE user_id = ? AND todo_date = ? 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$userId, $date]);
        $todos = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'todos' => $todos
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to load todos']);
        error_log("Get todos error: " . $e->getMessage());
    }
}

function handleCreateTodo($pdo, $userId, $input) {
    $text = trim($input['text'] ?? '');
    $date = $input['date'] ?? date('Y-m-d');

    if (empty($text)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Todo text is required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO todos (user_id, text, todo_date) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $text, $date]);
        
        $todoId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'todo' => [
                'id' => $todoId,
                'text' => $text,
                'completed' => false,
                'todo_date' => $date
            ]
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create todo']);
        error_log("Create todo error: " . $e->getMessage());
    }
}

function handleUpdateTodo($pdo, $userId, $todoId, $input) {
    try {
        // First, verify the todo belongs to the user
        $stmt = $pdo->prepare("SELECT id, completed FROM todos WHERE id = ? AND user_id = ?");
        $stmt->execute([$todoId, $userId]);
        $todo = $stmt->fetch();

        if (!$todo) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Todo not found']);
            return;
        }

        $updates = [];
        $params = [];

        // Handle text update
        if (isset($input['text'])) {
            $text = trim($input['text']);
            if (empty($text)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Todo text cannot be empty']);
                return;
            }
            $updates[] = "text = ?";
            $params[] = $text;
        }

        // Handle completion toggle
        if (isset($input['completed'])) {
            if ($input['completed'] === 'toggle') {
                $newCompleted = $todo['completed'] ? 0 : 1;
            } else {
                $newCompleted = $input['completed'] ? 1 : 0;
            }
            $updates[] = "completed = ?";
            $params[] = $newCompleted;
        }

        if (empty($updates)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'No updates provided']);
            return;
        }

        $params[] = $todoId;
        $params[] = $userId;

        $sql = "UPDATE todos SET " . implode(', ', $updates) . " WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success' => true, 'message' => 'Todo updated successfully']);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update todo']);
        error_log("Update todo error: " . $e->getMessage());
    }
}

function handleDeleteTodo($pdo, $userId, $todoId) {
    try {
        $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
        $stmt->execute([$todoId, $userId]);

        if ($stmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Todo not found']);
            return;
        }

        echo json_encode(['success' => true, 'message' => 'Todo deleted successfully']);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to delete todo']);
        error_log("Delete todo error: " . $e->getMessage());
    }
}
?>