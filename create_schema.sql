CREATE DATABASE IF NOT EXISTS t11915jr;

CREATE TABLE IF NOT EXISTS  `t11915jr`.`User`(
    `user_ID` VARCHAR(45) NOT NULL,
    `user_name` VARCHAR(255) NOT NULL,
    `user_password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_ID)
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Quiz`(
    `quiz_ID` VARCHAR(45) NOT NULL,
    `quiz_name` VARCHAR(255),
    `quiz_owner` VARCHAR(255),
    PRIMARY KEY (quiz_ID),
    FOREIGN KEY (quiz_owner) REFERENCES User(user_ID)
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Attempt` (
    `attempt_number` INT UNSIGNED NOT NULL,
    `user_ID` VARCHAR(45) NOT NULL,
    `quiz_ID` VARCHAR(45) NOT NULL,
    `date_attempt` DATE,
    PRIMARY KEY (attempt_number, user_ID, quiz_ID),
    FOREIGN KEY (user_ID) REFERENCES User(user_ID)
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Question`(
    `quiz_ID` VARCHAR(45) NOT NULL,
    `question_number` INT UNSIGNED NOT NULL,
    `question` VARCHAR(255),
    PRIMARY KEY (quiz_ID, question_number),
    FOREIGN KEY (quiz_ID) REFERENCES Quiz(quiz_ID)
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Answer`(
    `quiz_ID` VARCHAR(45) NOT NULL,
    `question_number` INT UNSIGNED NOT NULL,
    `answer` VARCHAR(255),
    `is_correct` BOOLEAN,
    PRIMARY KEY (quiz_ID, question_number),
    FOREIGN KEY (quiz_ID, question_number) REFERENCES Question(quiz_ID, question_number)
);
