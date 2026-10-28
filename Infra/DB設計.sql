#DB設計

#CREATE DATABASE online_forum;
#use online_forum;

CREATE TABLE users(
    id INT AUTO_INCREMENT, 
    name VARCHAR(60) NOT NULL, 
    sign_up_day DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    password_hash VARCHAR(255) NOT NULL,
    is_exists TINYINT NOT NULL,

    PRIMARY KEY(id)
);

CREATE TABLE posts(
    id INT AUTO_ENCREMENT, 
    user_id INT NOT NULL,
    post_date_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    content VARCHAR(500) NOT NULL,
    is_exists TINYINT NOT NULL,

    PRIMARY KEY(id),
    FOREIGN KEY(user_id) REFERENCES users(id)
);