CREATE DATABASE game_db;

GRANT ALL PRIVILEGES ON game_db.* TO game_db_user@localhost IDENTIFIED BY 'Nihad1213!@';

USE game_db;

CREATE TABLE developers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    founded DATE,
    headquarters VARCHAR(255),
    ceo VARCHAR(100),
    website VARCHAR(255),
    contact_email VARCHAR(100),
    number_of_employees INT,
    notable_games TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE publishers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    founded DATE,
    headquarters VARCHAR(255),
    ceo VARCHAR(100),
    website VARCHAR(255),
    contact_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE games(
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(256) NOT NULL,
    `genre` VARCHAR(256),
    `platform` JSON,
    `developer_ID` INT,
    `publisher_ID` INT,
    `release_data` DATE,
    `status` BOOLEAN,
    `rating` VARCHAR(10),
    `price` DECIMAL(10, 2),
    `description` TEXT,
    `cover_image` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (developer_ID) REFERENCES developers(id),
    FOREIGN KEY (publisher_ID) REFERENCES publishers(id)
);

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT,
    name VARCHAR(127) NOT NULL,
    username VARCHAR(126) NOT NULL,
    password_hash VARCHAR(255) NOT NULL
    api_key VARCHAR(32) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (username),
    UNIQUE (api_key)
);