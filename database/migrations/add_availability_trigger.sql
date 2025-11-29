-- ============================================================================
-- Database Trigger for Automatic Room Availability Updates
-- Automatically sets availability_status based on bed occupancy
-- ============================================================================

-- Drop trigger if exists
DROP TRIGGER IF EXISTS update_listing_availability_after_roommate_change;

-- Create trigger
DELIMITER $$

CREATE TRIGGER update_listing_availability_after_roommate_change
AFTER UPDATE ON listings
FOR EACH ROW
BEGIN
    -- When room becomes full (current_roommates >= bedrooms)
    IF NEW.current_roommates >= NEW.bedrooms THEN
        IF NEW.availability_status != 'occupied' THEN
            UPDATE listings 
            SET availability_status = 'occupied' 
            WHERE listing_id = NEW.listing_id;
        END IF;
    -- When room has available beds and was previously occupied
    ELSEIF NEW.current_roommates < NEW.bedrooms AND OLD.availability_status = 'occupied' THEN
        -- Only set to available if listing is approved
        IF NEW.approval_status = 'approved' THEN
            UPDATE listings 
            SET availability_status = 'available' 
            WHERE listing_id = NEW.listing_id;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Update existing listings to correct status based on current occupancy
UPDATE listings 
SET availability_status = 'occupied' 
WHERE current_roommates >= bedrooms 
  AND availability_status != 'occupied';

UPDATE listings 
SET availability_status = 'available' 
WHERE current_roommates < bedrooms 
  AND approval_status = 'approved' 
  AND availability_status = 'occupied';
