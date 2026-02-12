-- ============================================
-- TICKET TRIAGE SYSTEM DATABASE SETUP
-- KyU IT Support Platform
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS ticket_system_db;
USE ticket_system_db;

-- Create support tickets table
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_number VARCHAR(20) UNIQUE NOT NULL,
    reporter_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    issue_category ENUM('Hardware', 'Software', 'Network') NOT NULL,
    detailed_description TEXT NOT NULL,
    priority ENUM('High', 'Medium', 'Low') NOT NULL,
    status ENUM('Open', 'In Progress', 'Resolved', 'Closed') DEFAULT 'Open',
    response_time VARCHAR(50) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_date TIMESTAMP NULL,
    INDEX idx_ticket_number (ticket_number),
    INDEX idx_category (issue_category),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO support_tickets (ticket_number, reporter_name, department, issue_category, detailed_description, priority, response_time, status) VALUES
('TKT-20240207-A1B2C3', 'John Kamau', 'Pure & Applied Sciences', 'Network', 'Campus Wi-Fi not working in Computer Lab 1. Students unable to access online resources for practical sessions.', 'High', 'Immediate', 'In Progress'),
('TKT-20240207-D4E5F6', 'Mary Wanjiku', 'Business Studies', 'Hardware', 'Projector in Lecture Hall B is not turning on. Tried multiple power sources but no response from the device.', 'High', '24 hours', 'Open'),
('TKT-20240207-G7H8I9', 'Peter Mwangi', 'Engineering', 'Software', 'Microsoft Office installation on Lab computers keeps crashing when opening Excel files larger than 5MB.', 'Medium', '24 hours', 'Open'),
('TKT-20240206-J1K2L3', 'Lucy Akinyi', 'Education', 'Network', 'Cannot connect to university email system from hostel area. Other websites work fine but email portal times out.', 'High', 'Immediate', 'Resolved'),
('TKT-20240206-M4N5O6', 'David Ochieng', 'Pure & Applied Sciences', 'Hardware', 'Keyboard in Lab 3 Computer Station 15 has several non-functional keys including Enter and Spacebar.', 'High', '24 hours', 'Resolved');

-- Create view for ticket statistics
CREATE OR REPLACE VIEW ticket_statistics AS
SELECT 
    COUNT(*) as total_tickets,
    SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) as open_tickets,
    SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_tickets,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_tickets,
    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as closed_tickets,
    SUM(CASE WHEN priority = 'High' THEN 1 ELSE 0 END) as high_priority,
    SUM(CASE WHEN priority = 'Medium' THEN 1 ELSE 0 END) as medium_priority,
    SUM(CASE WHEN priority = 'Low' THEN 1 ELSE 0 END) as low_priority
FROM support_tickets;

-- Create view for category breakdown
CREATE OR REPLACE VIEW category_breakdown AS
SELECT 
    issue_category,
    COUNT(*) as total_tickets,
    SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) as open_count,
    SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved_count,
    AVG(CASE WHEN priority = 'High' THEN 3 WHEN priority = 'Medium' THEN 2 ELSE 1 END) as avg_priority_score
FROM support_tickets
GROUP BY issue_category;

-- Show success message
SELECT 'Ticket System database created successfully!' AS status;
SELECT * FROM support_tickets;
SELECT * FROM ticket_statistics;