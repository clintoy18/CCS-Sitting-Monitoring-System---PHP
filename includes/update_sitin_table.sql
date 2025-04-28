-- Add computer column to sit_in_records table
ALTER TABLE sit_in_records 
ADD COLUMN computer VARCHAR(50) NULL AFTER lab;

-- Add computer_id column to reservations table
ALTER TABLE reservations 
ADD COLUMN computer_id INT NULL AFTER room_id,
ADD CONSTRAINT fk_reservations_computer 
FOREIGN KEY (computer_id) REFERENCES computers(computer_id); 