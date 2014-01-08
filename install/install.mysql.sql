
CREATE DATABASE wps;
GRANT SELECT, INSERT, UPDATE, DELETE ON wps.* TO 'wps'@'localhost' IDENTIFIED BY 'wps';
USE wps;

CREATE TABLE `account` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `mail` VARCHAR(255) UNIQUE NOT NULL,
   `user_name` VARCHAR(255) NOT NULL,
   `user_database` VARCHAR(32) NOT NULL DEFAULT 'default',
   `password_hash` VARCHAR(1024),
   `is_active` INT(1) NOT NULL DEFAULT 0,
   `is_admin` INT(1) NOT NULL DEFAULT 0,
   `validate_token` VARCHAR(64),
   `ts_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY (`mail`)
);

-- Some test data
INSERT INTO account (mail, user_name, is_active, is_admin) VALUES ('pounard@processus.org', 'Pierre', 1, 1);

CREATE TABLE `album` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `id_account` INT UNSIGNED NOT NULL,
   `path` VARCHAR(1024) NOT NULL,
   `user_name` VARCHAR(255),
   `ts_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `ts_updated` TIMESTAMP,
   `ts_user_date` TIMESTAMP,
   PRIMARY KEY (`id`),
   FOREIGN KEY (`id_account`) REFERENCES `account`(`id`)
);

CREATE TABLE `media` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `id_album` INT UNSIGNED NOT NULL,
   `id_account` INT UNSIGNED NOT NULL,
   `name` VARCHAR(1024) NOT NULL,
   `path` VARCHAR(1024) NOT NULL,
   `size` INT UNSIGNED NOT NULL DEFAULT 0,
   `width` INTEGER UNSIGNED,
   `height` INTEGER UNSIGNED,
   `user_name` VARCHAR(255),
   `md5_hash` VARCHAR(255),
   `mimetype` VARCHAR(255) NOT NULL DEFAULT 'application/octet-stream',
   `ts_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `ts_updated` TIMESTAMP,
   `ts_user_date` TIMESTAMP,
   PRIMARY KEY (`id`),
   FOREIGN KEY (`id_album`) REFERENCES `album`(`id`),
   FOREIGN KEY (`id_account`) REFERENCES `account`(`id`)
);
