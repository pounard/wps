
CREATE DATABASE wps;
GRANT SELECT, INSERT, UPDATE, DELETE ON wps.* TO 'wps'@'localhost' IDENTIFIED BY 'wps';
USE wps;

CREATE TABLE `account` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `mail` VARCHAR(255) UNIQUE NOT NULL,
   `user_name` VARCHAR(255) NOT NULL,
   `user_database` VARCHAR(32) NOT NULL DEFAULT 'default',
   `password_hash` VARCHAR(255),
   `salt` VARCHAR(128),
   `key_public` BLOB,
   `key_private` BLOB,
   `key_type` VARCHAR(10),
   `is_active` INT(1) NOT NULL DEFAULT 0,
   `is_admin` INT(1) NOT NULL DEFAULT 0,
   `validate_token` VARCHAR(64),
   `ts_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY (`mail`)
);

-- Some test data
INSERT INTO account (id, mail, user_name, is_active, is_admin) VALUES (0, 'Anonymous', 'Anonymous', 0, 0);
INSERT INTO account (mail, user_name, is_active, is_admin) VALUES ('pounard@processus.org', 'Pierre', 1, 1);

CREATE TABLE `album` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `id_account` INT UNSIGNED NOT NULL,
   `id_media_preview` INT UNSIGNED,
   `access_level` INT UNSIGNED DEFAULT 0,
   `path` VARCHAR(1024) NOT NULL,
   `user_name` VARCHAR(255),
   `file_count` INT UNSIGNED NOT NULL DEFAULT 0,
   `ts_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `ts_updated` TIMESTAMP NOT NULL,
   `ts_user_date` TIMESTAMP NOT NULL,
   PRIMARY KEY (`id`),
   FOREIGN KEY (`id_account`) REFERENCES `account`(`id`)
);

CREATE TABLE `media` (
   `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
   `id_album` INT UNSIGNED NOT NULL,
   `id_account` INT UNSIGNED NOT NULL,
   `access_level` INT UNSIGNED DEFAULT 0,
   `name` VARCHAR(1024) NOT NULL,
   `path` VARCHAR(1024) NOT NULL,
   `physical_path` VARCHAR(1024) NOT NULL,
   `size` INT UNSIGNED NOT NULL DEFAULT 0,
   `width` INTEGER UNSIGNED,
   `height` INTEGER UNSIGNED,
   `user_name` VARCHAR(255),
   `md5_hash` VARCHAR(255),
   `mimetype` VARCHAR(255) NOT NULL DEFAULT 'application/octet-stream',
   `ts_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
   `ts_updated` TIMESTAMP NOT NULL,
   `ts_user_date` TIMESTAMP NOT NULL,
   PRIMARY KEY (`id`),
   FOREIGN KEY (`id_album`) REFERENCES `album`(`id`),
   FOREIGN KEY (`id_account`) REFERENCES `account`(`id`)
);

CREATE TABLE `media_metadata` (
    `id_media` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `data` BLOB,
    FOREIGN KEY (`id_media`) REFERENCES `media`(`id`) ON DELETE CASCADE
);


