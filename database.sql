-- ------------------------------------------------------------
-- Database: hotel_management
-- ------------------------------------------------------------
USE hbwebsite;

-- ------------------------------------------------------------
-- Table: bookings
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    booking_id   VARCHAR(20) NOT NULL UNIQUE,   -- e.g. HB12345
    txn_id       VARCHAR(20) NOT NULL UNIQUE,   -- e.g. TXN123456
    room_id      INT NOT NULL,
    checkin      DATE NOT NULL,
    checkout     DATE NOT NULL,
    amount       DECIMAL(10,2) NOT NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_booking (booking_id, txn_id)
) ENGINE=InnoDB;

-- Payments table removed as per requirements
