-- Create the database
CREATE DATABASE IF NOT EXISTS daily_todo 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Use the database
USE daily_todo;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Create auth_tokens table
CREATE TABLE IF NOT EXISTS auth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- Create todos table
CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    text TEXT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    todo_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_user_date (user_id, todo_date),
    INDEX idx_user_id (user_id),
    INDEX idx_todo_date (todo_date),
    INDEX idx_completed (completed)
);

-- Create a view for easy todo statistics
CREATE OR REPLACE VIEW todo_stats AS
SELECT 
    u.id as user_id,
    u.username,
    t.todo_date,
    COUNT(*) as total_todos,
    SUM(CASE WHEN t.completed = 1 THEN 1 ELSE 0 END) as completed_todos,
    SUM(CASE WHEN t.completed = 0 THEN 1 ELSE 0 END) as pending_todos,
    ROUND((SUM(CASE WHEN t.completed = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as completion_percentage
FROM users u
LEFT JOIN todos t ON u.id = t.user_id
WHERE t.id IS NOT NULL
GROUP BY u.id, u.username, t.todo_date;

-- Insert sample data (optional - remove if not needed)
-- Sample user (password is 'password123' hashed)
INSERT INTO users (username, email, password_hash) VALUES 
('demo_user', 'demo@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Get the user ID for sample todos
SET @demo_user_id = LAST_INSERT_ID();

-- Sample todos for today
INSERT INTO todos (user_id, text, completed, todo_date) VALUES 
(@demo_user_id, 'Review project requirements', FALSE, CURDATE()),
(@demo_user_id, 'Attend team meeting at 2 PM', FALSE, CURDATE()),
(@demo_user_id, 'Complete daily standup notes', TRUE, CURDATE()),
(@demo_user_id, 'Update documentation', FALSE, CURDATE());

-- Sample todos for yesterday
INSERT INTO todos (user_id, text, completed, todo_date) VALUES 
(@demo_user_id, 'Code review for feature X', TRUE, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(@demo_user_id, 'Send weekly report', TRUE, DATE_SUB(CURDATE(), INTERVAL 1 DAY)),
(@demo_user_id, 'Plan tomorrow tasks', FALSE, DATE_SUB(CURDATE(), INTERVAL 1 DAY));

-- Sample todos for tomorrow
INSERT INTO todos (user_id, text, completed, todo_date) VALUES 
(@demo_user_id, 'Prepare presentation slides', FALSE, DATE_ADD(CURDATE(), INTERVAL 1 DAY)),
(@demo_user_id, 'Client call at 10 AM', FALSE, DATE_ADD(CURDATE(), INTERVAL 1 DAY));

-- Create a stored procedure for cleaning up expired tokens (optional)
DELIMITER //
CREATE PROCEDURE CleanupExpiredTokens()
BEGIN
    DELETE FROM auth_tokens WHERE expires_at < NOW();
    SELECT ROW_COUNT() as deleted_tokens;
END //
DELIMITER ;

-- Create an event to automatically cleanup expired tokens daily (optional)
-- Note: You need to enable the event scheduler first with: SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS cleanup_expired_tokens
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_TIMESTAMP
DO
    CALL CleanupExpiredTokens();

-- Show table information
SHOW TABLES;

-- Display table structures
DESCRIBE users;
DESCRIBE auth_tokens;
DESCRIBE todos;

-- Show sample data counts
SELECT 'Users' as table_name, COUNT(*) as record_count FROM users
UNION ALL
SELECT 'Auth Tokens' as table_name, COUNT(*) as record_count FROM auth_tokens
UNION ALL
SELECT 'Todos' as table_name, COUNT(*) as record_count FROM todos;


