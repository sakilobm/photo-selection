-- ============================================================
-- OBM Studio Framework Database Schema
-- Database: obm-new-version
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ─── packages ────────────────────────────────────────────────
DROP TABLE IF EXISTS `packages`;
CREATE TABLE `packages` (
  `id` VARCHAR(32) NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `price` INT NOT NULL,
  `badge` VARCHAR(128) DEFAULT NULL,
  `desc` TEXT DEFAULT NULL,
  `popular` TINYINT(1) NOT NULL DEFAULT 0,
  `features` TEXT NOT NULL, -- Stored as JSON string array
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── live_event ──────────────────────────────────────────────
DROP TABLE IF EXISTS `live_event`;
CREATE TABLE `live_event` (
  `id` INT AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL,
  `status` VARCHAR(32) NOT NULL DEFAULT 'OFFLINE', -- OFFLINE | PRE-SHOW | LIVE | ENDED
  `title` VARCHAR(255) DEFAULT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `stream_url` VARCHAR(512) DEFAULT NULL,
  `quality` VARCHAR(32) DEFAULT '1080p',
  `viewers` INT NOT NULL DEFAULT 0,
  `chat_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── client_portals ──────────────────────────────────────────
DROP TABLE IF EXISTS `client_portals`;
CREATE TABLE `client_portals` (
  `id` INT AUTO_INCREMENT,
  `code` VARCHAR(32) NOT NULL UNIQUE, -- Passcode used for client login
  `client_name` VARCHAR(128) NOT NULL,
  `email` VARCHAR(128) NOT NULL UNIQUE,
  `event_date` DATE DEFAULT NULL,
  `max_selection` INT NOT NULL DEFAULT 100,
  `total_photos` INT NOT NULL DEFAULT 0,
  `status` VARCHAR(32) NOT NULL DEFAULT 'Pending', -- Pending | In Progress | Completed | Blocked
  `blocked` TINYINT(1) NOT NULL DEFAULT 0,
  `flagged` TINYINT(1) NOT NULL DEFAULT 0,
  `added_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── client_photos ───────────────────────────────────────────
DROP TABLE IF EXISTS `client_photos`;
CREATE TABLE `client_photos` (
  `id` INT AUTO_INCREMENT,
  `portal_id` INT NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `category` VARCHAR(64) NOT NULL DEFAULT 'CANDID', -- CANDID | PORTRAIT | TRADITIONAL
  `size` VARCHAR(32) NOT NULL DEFAULT '3.5 MB',
  `thumb_url` VARCHAR(512) DEFAULT NULL,
  `selection_status` VARCHAR(32) NOT NULL DEFAULT 'PENDING', -- PENDING | APPROVED | REJECTED | DELETED
  `notes` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`portal_id`) REFERENCES `client_portals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── albums ──────────────────────────────────────────────────
DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
  `id` VARCHAR(64) NOT NULL,
  `chapter` VARCHAR(255) NOT NULL,
  `spreads` INT NOT NULL DEFAULT 10,
  `status` VARCHAR(64) NOT NULL DEFAULT 'In Review',
  `client_notes` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ─── Seeding Initial Data ────────────────────────────────────

-- Insert default packages
INSERT INTO `packages` (`id`, `name`, `price`, `badge`, `desc`, `popular`, `features`) VALUES
('silver', 'Silver Royal', 65000, 'Essential Coverage', 'Ideal for traditional rituals & intimate family ceremonies.', 0, '["1 Senior Traditional Photographer", "1 Traditional HD Videographer", "1 Artistic Candid Photographer", "40 Page Synthetic Flush Mount Album"]'),
('gold', 'Gold Elite', 145000, 'Cinematic & Drone', 'Complete candid, cinematic films & aerial drone shots.', 1, '["2 Candid Photographers & 1 Cinematographer", "4K Drone Operator (Aerial Venue Coverage)", "Outdoor Pre-Wedding Film Teaser", "50 Page Luxury Album + Acrylic Glass Box", "Digital Photo Selection Portal Access"]'),
('platinum', 'Platinum Plus', 285000, 'Grand Wedding Cinema', 'Full 4K cinema team, dual drones, and premium leather albums.', 0, '["Full Cinema Crew (3 Candid + 2 Traditional)", "2 Dual 4K Cinema Drones (Day & Night)", "Full Length Wedding Film + 3 min Trailer", "2 Luxury Leather Canvases + 2 Parent Albums", "Digital Flipbook Album Viewer Included", "Private Client Photo Selection Portal"]'),
('imperial', 'Imperial Stage', 450000, 'Stage & LED Production', 'Massive LED wall screens, live multi-camera broadcast & crane rigs.', 0, '["Massive High-Definition LED Video Wall Setup", "Multi-Cam Live Streaming & Stage Crane Jib", "Complete Photography & Cinematography Crew", "Unlimited Albums & Instant Photo Selection", "4K Drone Aerial Coverage Full Event", "VIP Priority Delivery (48hrs Digital)"]');

-- Insert default live event
INSERT INTO `live_event` (`code`, `status`, `title`, `subtitle`, `stream_url`, `quality`, `viewers`, `chat_enabled`) VALUES
('OBM026', 'LIVE', 'Vikram & Ananya Wedding', 'Live from Grand Mahal Convention Centre, Chennai', 'assets/wedding.jpg', '1080p', 142, 1);

-- Insert default client portals
INSERT INTO `client_portals` (`id`, `code`, `client_name`, `email`, `event_date`, `max_selection`, `total_photos`, `status`, `blocked`, `flagged`) VALUES
(1, 'DEMO2026', 'Vikram & Ananya', 'vikram@example.com', '2026-12-15', 100, 11, 'In Progress', 0, 1),
(2, 'KUMAR2026', 'Kumar & Priya', 'priya@example.com', '2026-11-20', 100, 5, 'Completed', 0, 1),
(3, 'SNEHA2026', 'Rahul & Sneha', 'arun@example.com', '2027-01-10', 120, 0, 'Pending', 0, 0),
(4, 'MEERA2026', 'Meera Nair', 'meera@example.com', '2026-10-05', 80, 0, 'Blocked', 1, 0);

-- Insert default photos for portals
INSERT INTO `client_photos` (`portal_id`, `filename`, `category`, `size`, `thumb_url`, `selection_status`) VALUES
-- DEMO2026 approved photos
(1, 'OBM_Candid_Ceremony_001.jpg', 'CANDID', '4.2 MB', 'https://images.unsplash.com/photo-1519741497674-611481863552?w=500&fit=crop', 'APPROVED'),
(1, 'OBM_Portrait_Couple_002.jpg', 'PORTRAIT', '3.8 MB', 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=500&fit=crop', 'APPROVED'),
(1, 'OBM_Traditional_Ritual_003.jpg', 'TRADITIONAL', '5.1 MB', 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=500&fit=crop', 'APPROVED'),
-- DEMO2026 rejected photos (available for selection)
(1, 'OBM_Candid_Wedding_001.jpg', 'CANDID', '4.5 MB', 'https://images.unsplash.com/photo-1519741497674-611481863552?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Portrait_Bridal_002.jpg', 'PORTRAIT', '3.2 MB', 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Traditional_Ritual_003.jpg', 'TRADITIONAL', '5.8 MB', 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Candid_Laughter_004.jpg', 'CANDID', '3.9 MB', 'https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Portrait_Studio_005.jpg', 'PORTRAIT', '4.1 MB', 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Traditional_Mandap_006.jpg', 'TRADITIONAL', '6.2 MB', 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Candid_Dance_007.jpg', 'CANDID', '3.7 MB', 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=500&fit=crop', 'PENDING'),
(1, 'OBM_Portrait_Family_008.jpg', 'PORTRAIT', '4.3 MB', 'https://images.unsplash.com/photo-1529634597503-139d3726fed5?w=500&fit=crop', 'PENDING'),

-- KUMAR2026 approved photos
(2, 'OBM_CandidKP_001.jpg', 'CANDID', '4.0 MB', 'https://images.unsplash.com/photo-1519741497674-611481863552?w=500&fit=crop', 'APPROVED'),
(2, 'OBM_PortraitKP_002.jpg', 'PORTRAIT', '3.5 MB', 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=500&fit=crop', 'APPROVED'),
(2, 'OBM_TraditionalKP_003.jpg', 'TRADITIONAL', '5.0 MB', 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=500&fit=crop', 'APPROVED'),
-- KUMAR2026 rejected photos (available for selection)
(2, 'OBM_CandidKP_Reject_001.jpg', 'CANDID', '3.8 MB', 'https://images.unsplash.com/photo-1519741497674-611481863552?w=500&fit=crop', 'PENDING'),
(2, 'OBM_PortraitKP_Reject_002.jpg', 'PORTRAIT', '4.1 MB', 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=500&fit=crop', 'PENDING');

-- Update total photo counts
UPDATE `client_portals` cp SET cp.total_photos = (SELECT COUNT(*) FROM `client_photos` WHERE portal_id = cp.id);

-- Insert default albums
INSERT INTO `albums` (`id`, `chapter`, `spreads`, `status`, `client_notes`) VALUES
('ch-wedding', 'Wedding Ceremony', 25, 'In Review', 2);

SET FOREIGN_KEY_CHECKS = 1;
