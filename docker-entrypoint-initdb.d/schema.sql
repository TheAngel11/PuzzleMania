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
    `user_id`    INT          ,
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


