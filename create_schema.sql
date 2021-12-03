CREATE DATABASE IF NOT EXISTS t11915jr;

CREATE TABLE IF NOT EXISTS  `t11915jr`.`User`(
    `user_ID` VARCHAR(45) NOT NULL,
#     Staff is 1; student is 0
    `is_staff` BOOLEAN,
    `user_name` VARCHAR(255) NOT NULL,
    `user_password` VARCHAR(255) NOT NULL,
    PRIMARY KEY (user_ID)
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Quiz`(
    `quiz_ID` VARCHAR(45) AUTO_INCREMENT,
    `quiz_name` VARCHAR(255),
    `quiz_owner` VARCHAR(255),
    PRIMARY KEY (quiz_ID),
    FOREIGN KEY (quiz_owner) REFERENCES User(user_ID)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Attempt` (
    `attempt_number` INT UNSIGNED AUTO_INCREMENT,
    `user_ID` VARCHAR(45) NOT NULL,
    `quiz_ID` VARCHAR(45) NOT NULL,
    `date_attempt` DATE,
    PRIMARY KEY (attempt_number, user_ID, quiz_ID),
    FOREIGN KEY (user_ID) REFERENCES User(user_ID)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Question`(
    `quiz_ID` VARCHAR(45) NOT NULL,
    `question_number` INT UNSIGNED AUTO_INCREMENT,
    `question` VARCHAR(255),
    PRIMARY KEY (quiz_ID, question_number),
    FOREIGN KEY (quiz_ID) REFERENCES Quiz(quiz_ID)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Answer`(
    `quiz_ID` VARCHAR(45),
    `question_number` INT UNSIGNED,
    `answer_number` INT UNSIGNED AUTO_INCREMENT,
    `answer` VARCHAR(255),
    `is_correct` BOOLEAN,
    PRIMARY KEY (quiz_ID, question_number),
    FOREIGN KEY (quiz_ID, question_number) REFERENCES Question(quiz_ID, question_number)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Attempt_Answers` (
    `attempt_number` INT UNSIGNED NOT NULL,
    `user_ID` VARCHAR(45) NOT NULL,
    `quiz_ID` VARCHAR(45) NOT NULL,
    `question_number` INT UNSIGNED NOT NULL,
    `answer_number` INT UNSIGNED NOT NULL,
    `user_answer` INT UNSIGNED NOT NULL,
    PRIMARY KEY (attempt_number, user_ID, quiz_ID, answer_number),
    FOREIGN KEY (attempt_number, user_ID, quiz_ID)
        REFERENCES Attempt(attempt_number, user_ID, quiz_ID)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`DeletedQuizzes`(
    `user_ID` VARCHAR(45) NOT NULL,
    `quiz_ID` VARCHAR(45) NOT NULL,
    `time_deleted` DATE
);

CREATE TRIGGER onQuizDelete
    BEFORE DELETE ON Quiz FOR EACH ROW
        INSERT INTO DeletedQuizzes SET `user_ID`= OLD.quiz_ID, `quiz_ID` = OLD.quiz_ID, `time_deleted` = NOW();

DELIMITER //
CREATE PROCEDURE sumAnswers(IN ID VARCHAR(45))
BEGIN
    SELECT user_ID, SUM(EQUALS(user_answer, answer)) FROM Attempt_Answers
        INNER JOIN Answer
            ON Attempt_Answers.quiz_ID = Answer.quiz_ID
                   AND Attempt_Answers.question_number = Answer.question_number
    WHERE SUM(EQUALS(user_answer, answer))/MAX(Attempt_Answers.question_number) <= 0.4;
END //

DELIMITER ;
