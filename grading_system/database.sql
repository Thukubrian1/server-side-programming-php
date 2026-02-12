-- ============================================
-- GRADING SYSTEM DATABASE SETUP
-- SSE 2304 Lab Evaluation Tool
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS grading_system_db;
USE grading_system_db;

-- Create student grades table
CREATE TABLE IF NOT EXISTS student_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    cat1_score DECIMAL(5,2) NOT NULL,
    cat2_score DECIMAL(5,2) NOT NULL,
    assignment_score DECIMAL(5,2) NOT NULL,
    total_score DECIMAL(5,2) NOT NULL,
    status ENUM('Pass', 'Consult') NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student (student_name),
    INDEX idx_status (status),
    INDEX idx_total (total_score)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO student_grades (student_name, cat1_score, cat2_score, assignment_score, total_score, status) VALUES
('John Kamau', 12.5, 13.0, 8.0, 33.5, 'Pass'),
('Mary Wanjiru', 14.0, 12.5, 9.5, 36.0, 'Pass'),
('Peter Mwangi', 10.0, 9.5, 7.0, 26.5, 'Pass'),
('Lucy Akinyi', 8.0, 7.5, 3.0, 18.5, 'Consult'),
('David Ochieng', 11.5, 10.0, 8.5, 30.0, 'Pass');

-- Create view for class statistics
CREATE OR REPLACE VIEW class_statistics AS
SELECT 
    COUNT(*) as total_students,
    ROUND(AVG(total_score), 2) as average_score,
    MAX(total_score) as highest_score,
    MIN(total_score) as lowest_score,
    SUM(CASE WHEN status = 'Pass' THEN 1 ELSE 0 END) as passing_students,
    SUM(CASE WHEN status = 'Consult' THEN 1 ELSE 0 END) as consulting_students,
    ROUND(AVG(cat1_score), 2) as avg_cat1,
    ROUND(AVG(cat2_score), 2) as avg_cat2,
    ROUND(AVG(assignment_score), 2) as avg_assignment
FROM student_grades;

-- Create view for grade distribution
CREATE OR REPLACE VIEW grade_distribution AS
SELECT 
    CASE 
        WHEN total_score >= 32 THEN 'A (32-40)'
        WHEN total_score >= 24 THEN 'B (24-31)'
        WHEN total_score >= 20 THEN 'C (20-23)'
        ELSE 'Below Pass (0-19)'
    END as grade_range,
    COUNT(*) as student_count
FROM student_grades
GROUP BY grade_range
ORDER BY MIN(total_score) DESC;

-- Show success message
SELECT 'Grading System database created successfully!' AS status;
SELECT * FROM student_grades;
SELECT * FROM class_statistics;