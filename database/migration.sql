-- Users table
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullName` varchar(255) NOT NULL,
  `userName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `noOfFollowers` int(11) DEFAULT 0,
  `noOfFollowing` int(11) DEFAULT 0,
  `type` varchar(20) DEFAULT 'Usual',
  `image` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `bio` TEXT,
  `phone` varchar(15),
  `location` varchar(255),
  `website` varchar(255),
  `registration_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `last_login` DATETIME,
  `is_active` BOOLEAN DEFAULT TRUE,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userName` (`userName`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Category table
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Posts table
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255),
  `description` varchar(255) NOT NULL,
  `content` TEXT,
  `ingredients` varchar(255),
  `date` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  `likes_count` INT DEFAULT 0,
  `comments_count` INT DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- User relationships table
CREATE TABLE `user_relationships` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `follower_id` INT NOT NULL,
  `followed_id` INT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`),
  FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`),
  UNIQUE KEY `unique_relationship` (`follower_id`, `followed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Post reactions table
CREATE TABLE `post_reactions` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `reaction_type` ENUM('like', 'love', 'haha', 'wow', 'sad', 'angry') NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  UNIQUE KEY `unique_user_post_reaction` (`user_id`, `post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Comments table
CREATE TABLE `comments` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME ON UPDATE CURRENT_TIMESTAMP,
  parent_comment_id INT,
  FOREIGN KEY (parent_comment_id) REFERENCES comments(id),
  FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Notifications table
CREATE TABLE `notifications` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `content` TEXT NOT NULL,
  `is_read` BOOLEAN DEFAULT FALSE,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- API tokens table (keeping the original structure)
CREATE TABLE `api_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `api_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add indexes for improved query performance
CREATE INDEX idx_posts_user_id ON posts(user_id);
CREATE INDEX idx_posts_category_id ON posts(category_id);
CREATE INDEX idx_comments_post_id ON comments(post_id);
CREATE INDEX idx_comments_user_id ON comments(user_id);
CREATE INDEX idx_post_reactions_post_id ON post_reactions(post_id);
CREATE INDEX idx_post_reactions_user_id ON post_reactions(user_id);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);

DELIMITER //

-- User relationship triggers
CREATE TRIGGER after_user_relationship_insert
AFTER INSERT ON user_relationships
FOR EACH ROW
BEGIN
    UPDATE users SET noOfFollowers = noOfFollowers + 1 WHERE id = NEW.followed_id;
    UPDATE users SET noOfFollowing = noOfFollowing + 1 WHERE id = NEW.follower_id;
    
    INSERT INTO notifications (user_id, type, content)
    SELECT NEW.followed_id, 'new_follower', CONCAT(u.userName, ' started following you')
    FROM users u WHERE u.id = NEW.follower_id;
END//

CREATE TRIGGER after_user_relationship_delete
AFTER DELETE ON user_relationships
FOR EACH ROW
BEGIN
    UPDATE users SET noOfFollowers = noOfFollowers - 1 WHERE id = OLD.followed_id;
    UPDATE users SET noOfFollowing = noOfFollowing - 1 WHERE id = OLD.follower_id;
END//

-- Post reaction triggers
CREATE TRIGGER after_post_reaction_insert
AFTER INSERT ON post_reactions
FOR EACH ROW
BEGIN
    UPDATE posts SET likes_count = likes_count + 1 WHERE id = NEW.post_id;
    
    INSERT INTO notifications (user_id, type, content)
    SELECT p.user_id, 'post_liked', CONCAT(u.userName, ' liked your post')
    FROM posts p
    JOIN users u ON u.id = NEW.user_id
    WHERE p.id = NEW.post_id AND p.user_id != NEW.user_id;
END//

CREATE TRIGGER after_post_reaction_delete
AFTER DELETE ON post_reactions
FOR EACH ROW
BEGIN
    UPDATE posts SET likes_count = likes_count - 1 WHERE id = OLD.post_id;
END//

-- Comment triggers
CREATE TRIGGER after_comment_insert
AFTER INSERT ON comments
FOR EACH ROW
BEGIN
    UPDATE posts SET comments_count = comments_count + 1 WHERE id = NEW.post_id;
    
    -- Notification for post owner
    INSERT INTO notifications (user_id, type, content)
    SELECT p.user_id, 'post_commented', CONCAT(u.userName, ' commented on your post')
    FROM posts p
    JOIN users u ON u.id = NEW.user_id
    WHERE p.id = NEW.post_id AND p.user_id != NEW.user_id;
    
    -- Notification for parent comment owner (if it's a reply)
    IF NEW.parent_comment_id IS NOT NULL THEN
        INSERT INTO notifications (user_id, type, content)
        SELECT c.user_id, 'comment_reply', CONCAT(u.userName, ' replied to your comment')
        FROM comments c
        JOIN users u ON u.id = NEW.user_id
        WHERE c.id = NEW.parent_comment_id AND c.user_id != NEW.user_id;
    END IF;
END//

CREATE TRIGGER after_comment_delete
AFTER DELETE ON comments
FOR EACH ROW
BEGIN
    UPDATE posts SET comments_count = comments_count - 1 WHERE id = OLD.post_id;
END//

-- User last login update
CREATE TRIGGER before_api_token_insert
BEFORE INSERT ON api_tokens
FOR EACH ROW
BEGIN
    UPDATE users SET last_login = NOW() WHERE id = NEW.user_id;
END//

DELIMITER ;



ALTER TABLE users
ADD COLUMN is_premium BOOLEAN DEFAULT FALSE,
ADD COLUMN premium_expiry DATE NULL;
CREATE TABLE premium_subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  plan_name VARCHAR(50) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
DELIMITER //

CREATE TRIGGER after_premium_subscription_insert_update
AFTER INSERT ON premium_subscriptions
FOR EACH ROW
BEGIN
    UPDATE users 
    SET is_premium = TRUE, 
        premium_expiry = NEW.end_date 
    WHERE id = NEW.user_id;
END//

DELIMITER ;
DELIMITER //

CREATE TRIGGER after_premium_subscription_update
AFTER UPDATE ON premium_subscriptions
FOR EACH ROW
BEGIN
    IF NEW.status = 'expired' OR NEW.status = 'cancelled' THEN
        UPDATE users 
        SET is_premium = FALSE, 
            premium_expiry = NULL 
        WHERE id = NEW.user_id;
    END IF;
END//

DELIMITER ;
DELIMITER //

CREATE PROCEDURE update_expired_subscriptions()
BEGIN
    UPDATE premium_subscriptions
    SET status = 'expired'
    WHERE end_date < CURDATE() AND status = 'active';
    
    UPDATE users u
    JOIN premium_subscriptions ps ON u.id = ps.user_id
    SET u.is_premium = FALSE, u.premium_expiry = NULL
    WHERE ps.status = 'expired' AND u.is_premium = TRUE;
END//

DELIMITER ;
CREATE EVENT update_expired_subscriptions_event
ON SCHEDULE EVERY 1 DAY
STARTS CURRENT_DATE + INTERVAL 1 DAY
DO CALL update_expired_subscriptions();

CREATE TABLE reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reporter_id INT NOT NULL,
  reported_user_id INT,
  reported_post_id INT,
  reported_comment_id INT,
  report_type ENUM('user', 'post', 'comment') NOT NULL,
  reason VARCHAR(255) NOT NULL,
  description TEXT,
  status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (reporter_id) REFERENCES users(id),
  FOREIGN KEY (reported_user_id) REFERENCES users(id),
  FOREIGN KEY (reported_post_id) REFERENCES posts(id),
  FOREIGN KEY (reported_comment_id) REFERENCES comments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
CREATE INDEX idx_report_status ON reports(status);
DELIMITER //

CREATE TRIGGER after_report_insert
AFTER INSERT ON reports
FOR EACH ROW
BEGIN
    INSERT INTO notifications (user_id, type, content)
    SELECT id, 'new_report', CONCAT('New ', NEW.report_type, ' report filed. ID: ', NEW.id)
    FROM users
    WHERE type = 'admin';
END//

DELIMITER ;DELIMITER //

CREATE PROCEDURE get_pending_reports(IN limit_count INT)
BEGIN
    SELECT r.*, 
           u1.userName AS reporter_name,
           u2.userName AS reported_user_name,
           p.title AS reported_post_title,
           c.content AS reported_comment_content
    FROM reports r
    LEFT JOIN users u1 ON r.reporter_id = u1.id
    LEFT JOIN users u2 ON r.reported_user_id = u2.id
    LEFT JOIN posts p ON r.reported_post_id = p.id
    LEFT JOIN comments c ON r.reported_comment_id = c.id
    WHERE r.status = 'pending'
    ORDER BY r.created_at ASC
    LIMIT limit_count;
END//

DELIMITER ;
DELIMITER //

CREATE PROCEDURE update_report_status(IN report_id INT, IN new_status VARCHAR(20))
BEGIN
    UPDATE reports
    SET status = new_status,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = report_id;
    
    -- Optionally, we can add more logic here, such as:
    -- - Notifying the reporter of the status change
    -- - Taking automatic actions based on the new status (e.g., hiding reported content)
END//

DELIMITER ;
ALTER TABLE users
ADD COLUMN report_count INT DEFAULT 0;
DELIMITER //

CREATE TRIGGER after_user_report
AFTER INSERT ON reports
FOR EACH ROW
BEGIN
    IF NEW.report_type = 'user' THEN
        UPDATE users
        SET report_count = report_count + 1
        WHERE id = NEW.reported_user_id;
    END IF;
END//

DELIMITER ;
CREATE VIEW report_statistics AS
SELECT 
    report_type,
    status,
    COUNT(*) as count,
    DATE(created_at) as report_date
FROM reports
GROUP BY report_type, status, report_date
ORDER BY report_date DESC, count DESC;