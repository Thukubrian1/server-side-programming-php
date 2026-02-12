-- ============================================
-- MASOMO EXCHANGE DATABASE SETUP
-- KyU Resource Sharing Platform
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS masomo_exchange_db;
USE masomo_exchange_db;

-- Create resources table
CREATE TABLE IF NOT EXISTS educational_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    resource_title VARCHAR(200) NOT NULL,
    resource_category ENUM('Notes', 'Video', 'Source Code') NOT NULL,
    resource_url VARCHAR(500) NOT NULL,
    contributor_email VARCHAR(100) NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    views INT DEFAULT 0,
    is_approved BOOLEAN DEFAULT TRUE,
    INDEX idx_category (resource_category),
    INDEX idx_email (contributor_email),
    INDEX idx_date (submission_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO educational_resources (resource_title, resource_category, resource_url, contributor_email) VALUES
('Introduction to PHP Arrays and Functions', 'Video', 'https://youtube.com/watch?v=php-arrays-101', 'jkamau@kyu.ac.ke'),
('Server-Side Programming Complete Notes', 'Notes', 'https://docs.kyu.ac.ke/sse2304/complete-notes.pdf', 'mwanjiku@kyu.ac.ke'),
('PHP MySQL CRUD Operations Source Code', 'Source Code', 'https://github.com/kyu-resources/php-crud-example', 'pmwangi@kyu.ac.ke'),
('Database Design Tutorial for Beginners', 'Video', 'https://youtube.com/watch?v=database-design-basics', 'swanjau@kyu.ac.ke'),
('PHP Security Best Practices Guide', 'Notes', 'https://docs.kyu.ac.ke/security/php-security.pdf', 'lnyambura@kyu.ac.ke');

-- Create view for category statistics
CREATE OR REPLACE VIEW resource_statistics AS
SELECT 
    resource_category,
    COUNT(*) as total_resources,
    SUM(views) as total_views,
    COUNT(DISTINCT contributor_email) as unique_contributors
FROM educational_resources
GROUP BY resource_category;

-- Create view for top contributors
CREATE OR REPLACE VIEW top_contributors AS
SELECT 
    contributor_email,
    COUNT(*) as resources_shared,
    SUM(views) as total_views
FROM educational_resources
GROUP BY contributor_email
ORDER BY resources_shared DESC
LIMIT 10;

-- Show success message
SELECT 'Masomo Exchange database created successfully!' AS status;
SELECT * FROM educational_resources;