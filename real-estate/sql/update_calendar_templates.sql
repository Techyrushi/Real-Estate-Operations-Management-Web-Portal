CREATE TABLE IF NOT EXISTS event_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    class_name VARCHAR(50) DEFAULT 'bg-primary',
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default templates
INSERT IGNORE INTO event_templates (title, class_name) VALUES 
('Lunch', 'bg-primary'),
('Go home', 'bg-warning'),
('Do homework', 'bg-info'),
('Work on UI design', 'bg-success'),
('Sleep tight', 'bg-danger');
