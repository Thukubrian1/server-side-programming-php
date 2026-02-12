-- Create database
CREATE DATABASE IF NOT EXISTS eco_track_db;
USE eco_track_db;

-- Create submissions table
CREATE TABLE IF NOT EXISTS sustainability_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    student_id VARCHAR(50) NOT NULL,
    action_category ENUM('Tree Planting', 'Waste Reduction', 'Energy Saving') NOT NULL,
    impact_description TEXT NOT NULL,
    submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_student_id (student_id),
    INDEX idx_category (action_category),
    INDEX idx_date (submission_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data
INSERT INTO sustainability_submissions (full_name, student_id, action_category, impact_description) VALUES
('John Kamau', 'SCT211-0123/2022', 'Tree Planting', 'Planted 10 indigenous trees in the main campus compound to improve air quality and provide natural shade for students during hot weather.'),
('Mary Wanjiku', 'SCT211-0456/2022', 'Waste Reduction', 'Organized a campus-wide recycling initiative that collected over 50kg of plastic waste and established three recycling points around campus.'),
('Peter Mwangi', 'SCT211-0789/2022', 'Energy Saving', 'Led a campaign to install motion-sensor lights in all classrooms, reducing energy consumption by approximately 35% in academic buildings.');

-- Create view for statistics
CREATE OR REPLACE VIEW category_statistics AS
SELECT 
    action_category,
    COUNT(*) as total_submissions,
    COUNT(DISTINCT student_id) as unique_students
FROM sustainability_submissions
GROUP BY action_category;

-- Show success message
SELECT 'Eco-Track database created successfully!' AS status;
SELECT * FROM sustainability_submissions;