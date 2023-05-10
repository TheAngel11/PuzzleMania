SET NAMES utf8;
SET
time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `puzzlemania`;
USE `puzzlemania`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `email`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `password`  VARCHAR(255)                                            NOT NULL,
    `createdAt` DATETIME                                                NOT NULL,
    `updatedAt` DATETIME                                                NOT NULL,
    `uuid`       VARCHAR(255)                                           ,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `riddles`;
CREATE TABLE `riddles`
(
    `riddle_id`   INT          NOT NULL AUTO_INCREMENT,
    `user_id`    INT          NOT NULL,
    `riddle`      VARCHAR(255) NOT NULL,
    `answer`    VARCHAR(255) NOT NULL,
    PRIMARY KEY (`riddle_id`),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams`
(
    `team_id`   INT          NOT NULL AUTO_INCREMENT,
    `team_name`      VARCHAR(255) NOT NULL,
    `team_score`    INT          NOT NULL,
    PRIMARY KEY (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `team_members`;
CREATE TABLE `team_members`
(
    `team_id`   INT          NOT NULL,
    `user_id`   INT          NOT NULL,
    PRIMARY KEY (`team_id`, `user_id`),
    FOREIGN KEY (team_id) REFERENCES teams (team_id),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `riddle_answers`;
CREATE TABLE `riddle_answers`
(
    `riddle_id` INT NOT NULL AUTO_INCREMENT,
    `user_id`   INT NOT NULL,
    `question` VARCHAR(255) NOT NULL,
    `answer`    VARCHAR(255) NOT NULL,
    PRIMARY KEY (`riddle_id`),
    FOREIGN KEY (user_id) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
