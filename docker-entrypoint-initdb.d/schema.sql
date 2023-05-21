-- Adminer 4.8.1 MySQL 8.0.32 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `riddles`;
CREATE TABLE `riddles` (
                           `riddle_id` int NOT NULL AUTO_INCREMENT,
                           `user_id` int DEFAULT NULL,
                           `riddle` varchar(255) NOT NULL,
                           `answer` varchar(255) NOT NULL,
                           PRIMARY KEY (`riddle_id`),
                           KEY `user_id` (`user_id`),
                           CONSTRAINT `riddles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `team_members`;
CREATE TABLE `team_members` (
                                `team_id` int NOT NULL,
                                `user_id` int NOT NULL,
                                PRIMARY KEY (`team_id`,`user_id`),
                                KEY `user_id` (`user_id`),
                                CONSTRAINT `team_members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`),
                                CONSTRAINT `team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `teams`;
CREATE TABLE `teams` (
                         `team_id` int NOT NULL AUTO_INCREMENT,
                         `team_name` varchar(255) NOT NULL,
                         `team_score` int NOT NULL,
                         PRIMARY KEY (`team_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` int NOT NULL AUTO_INCREMENT,
                         `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `createdAt` datetime NOT NULL,
                         `updatedAt` datetime NOT NULL,
                         `uuid` varchar(255) DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;


-- 2023-05-16 17:05:21

DROP TABLE IF EXISTS `games`;
CREATE TABLE `games`
(
    `game_id`   INT          NOT NULL AUTO_INCREMENT,
    `riddle1`   VARCHAR(255) NOT NULL,
    `riddle2`   VARCHAR(255) NOT NULL,
    `riddle3`   VARCHAR(255) NOT NULL,
    `user_id`   INT          NOT NULL,
    `score`     INT          NOT NULL,
    PRIMARY KEY (`game_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Now that all tables are created, let's populate the database with test entries
INSERT INTO riddles(riddle, answer)
VALUES ('It brings back the lost as though never gone, shines laughter and tears with light
long since shone; a moment to make, a lifetime to shed; valued then but lost when your dead.
What Is It?', 'Memory');

INSERT INTO riddles(riddle, answer)
VALUES ('What do you get when you cross a fish with an elephant?', 'Swimming trunks');

INSERT INTO riddles(riddle, answer)
VALUES ('I can be long, or I can be short. I can be grown, and I can be bought. I can be
painted, or left bare. I can be round, or I can be square. What am I?', 'Fingernails');

INSERT INTO riddles(riddle, answer)
VALUES ('I am lighter than a feather yet no man can hold me for long.', 'Breath');

INSERT INTO riddles(riddle, answer)
VALUES ('What occurs once in every minute, twice in every moment and yet never in a
thousand years?', 'The letter M');

INSERT INTO riddles(riddle, answer)
VALUES ('What nationality is Santa Claus?', 'North Polish');

INSERT INTO riddles(riddle, answer)
VALUES ('What animal is best at hitting a baseball?', 'A bat');

INSERT INTO riddles(riddle, answer)
VALUES ('What do you call a cow that twitches?', 'Beef Jerky');


