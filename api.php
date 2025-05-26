<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    switch ($method) {
        case 'GET':
            // Get all todos
            $stmt = $pdo->query("SELECT * FROM todos ORDER BY created_at DESC");
            $todos = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $todos]);
            break;

        case 'POST':
            // Add new todo
            if (!isset($input['task']) || empty(trim($input['task']))) {
                throw new Exception('Task is required');
            }
            
            $stmt = $pdo->prepare("INSERT INTO todos (task) VALUES (?)");
            $stmt->execute([trim($input['task'])]);
            
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = ?");
            $stmt->execute([$id]);
            $todo = $stmt->fetch();
            
            echo json_encode(['success' => true, 'data' => $todo]);
            break;

        case 'PUT':
            // Update todo (toggle completion or edit task)
            if (!isset($input['id'])) {
                throw new Exception('Todo ID is required');
            }
            
            if (isset($input['completed'])) {
                // Toggle completion status
                $stmt = $pdo->prepare("UPDATE todos SET completed = ? WHERE id = ?");
                $stmt->execute([$input['completed'], $input['id']]);
            }
            
            if (isset($input['task'])) {
                // Update task text
                $stmt = $pdo->prepare("UPDATE todos SET task = ? WHERE id = ?");
                $stmt->execute([trim($input['task']), $input['id']]);
            }
            
            $stmt = $pdo->prepare("SELECT * FROM todos WHERE id = ?");
            $stmt->execute([$input['id']]);
            $todo = $stmt->fetch();
            
            echo json_encode(['success' => true, 'data' => $todo]);
            break;

        case 'DELETE':
            // Delete todo
            if (!isset($input['id'])) {
                throw new Exception('Todo ID is required');
            }
            
            $stmt = $pdo->prepare("DELETE FROM todos WHERE id = ?");
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true, 'message' => 'Todo deleted successfully']);
            break;

        default:
            throw new Exception('Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error occurred']);
}
?>