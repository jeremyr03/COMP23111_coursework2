CREATE TABLE IF NOT EXISTS `t11915jr`.`Attempt` (
    `attempt_number` INT UNSIGNED NOT NULL ,
    `user_ID` VARCHAR(45) NOT NULL ,
    `quiz_ID` VARCHAR(45) NOT NULL ,
    `date_attempt` DATE,
     PRIMARY KEY (attempt_number, user_ID, quiz_ID)
);

CREATE TABLE IF NOT EXISTS `t11915jr`.`Quiz`(
    `quiz_ID` VARCHAR(45) NOT NULL,
    `quiz_name` VARCHAR(255),
    PRIMARY KEY (`quiz_ID`)
);