CREATE TABLE IF NOT EXISTS `student` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `auid` varchar(8) NOT NULL,
  `year` INT(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `phone` INT(11) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `team` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(35) NOT NULL,
  `slug` varchar(35) NOT NULL,
  `creationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `welcome` TEXT NULL DEFAULT NULL,
  `value` INT(11) NOT NULL DEFAULT 5 -- Klandring price.
);

CREATE TABLE IF NOT EXISTS `role` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(35) NOT NULL
);

CREATE TABLE IF NOT EXISTS `teamstudent` (
  `teamid` INT(11),
  `studentid` INT(11),
  `roleid` INT(11) DEFAULT 1,
   FOREIGN KEY (teamid) REFERENCES team(id),
   FOREIGN KEY (studentid) REFERENCES student(id),
   FOREIGN KEY (roleid) REFERENCES role(id)
);

CREATE TABLE IF NOT EXISTS `klandring` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `creationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `verdictdate` DATE,
  `title` varchar(120) NOT NULL,
  `description` text,
  `team` INT(11) NOT NULL,
  `from` INT(11) NOT NULL,
  `to` INT(11) NOT NULL,
  `verdict` INT(3) NOT NULL DEFAULT 0,   -- 1 = from lost
                                         -- 2 = to   lost
                                         -- 3 = both lost
                                         -- 0 = not decided.
 `paid` BIT NOT NULL DEFAULT 0, -- 0 not paid, 1 paid.
  FOREIGN KEY (`team`) REFERENCES team(id),
  FOREIGN KEY (`from`) REFERENCES student(id),
  FOREIGN KEY (`to`) REFERENCES student(id)
);

CREATE TABLE IF NOT EXISTS `klandringmeta` (
  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `klandringid` INT NOT NULL,
  `uploadedby` INT(11) NOT NULL,
  `uploaddate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mime` VARCHAR(100) NOT NULL,
  `oldname` VARCHAR(260) NOT NULL,
  `newpath` CHAR(60) NOT NULL, -- 63^60 possibilities should be enough.
  FOREIGN KEY (`klandringid`) REFERENCES klandring(id),
  FOREIGN KEY (`uploadedby`) REFERENCES student(id)
);

INSERT INTO `role` (`id`, `name`) VALUES 
(0, 'applicant'),
(1, 'member'),
(2, 'treasurer'),
(3, 'team-admin');

CREATE TABLE IF NOT EXISTS `studentElo` (
  `id`	INT PRIMARY KEY,
  `elo`	FLOAT NOT NULL DEFAULT 2000
);
ALTER TABLE studentElo
ADD INDEX myIndex (id, elo);

ALTER TABLE studentElo
ADD FOREIGN KEY (id) REFERENCES student (`id`);

DELIMITER $$
DROP TRIGGER IF EXISTS addEloToStudent$$
CREATE TRIGGER addEloToStudent
AFTER INSERT ON student
FOR EACH ROW
BEGIN
    INSERT INTO studentElo VALUES(new.id, 2000);
END$$
DELIMITER ;

INSERT INTO studentElo (id)
SELECT id FROM student;

DELIMITER $$
DROP TRIGGER IF EXISTS updateEloOnUpdatedKlandring$$
CREATE TRIGGER updateEloOnUpdatedKlandring
AFTER UPDATE ON klandring
FOR EACH ROW
BEGIN
    DECLARE p1Elo FLOAT;
    DECLARE p2Elo FLOAT;
    DECLARE r1 FLOAT;
    DECLARE r2 FLOAT;
    DECLARE e1 FLOAT;
    DECLARE e2 FLOAT;
    DECLARE s1 FLOAT;
    DECLARE s2 FLOAT;
    DECLARE p1 FLOAT;
    DECLARE p2 FLOAT;
    IF (new.verdict != 0) THEN
		SET @p1Elo = (SELECT elo FROM studentElo WHERE id = new.from);
        SET @p2Elo = (SELECT elo FROM studentElo WHERE id = new.to);
        SET @r1 = (SELECT POW(10,(@p1Elo/400)));
        SET @r2 = (SELECT POW(10,(@p2Elo/400)));
        SET @e1 = (SELECT @r1/(@r1 + @r2));
        SET @e2 = (SELECT @r2/(@r1 + @r2));
        IF (new.verdict = 1) THEN
			SET @s1 = 1;
            SET @s2 = 0;
        END IF;
        IF(new.verdict = 2) THEN
			SET @s1 = 0;
            SET @s2 = 1;
		END IF;
		IF (new.verdict = 3) THEN
			SET @s1 = 0.5;
            SET @s2 = 0.5;
        END IF;
        SET @p1 = (SELECT @p1Elo + (32*(@s1-@e1)));
        SET @p2 = (SELECT @p2Elo + (32*(@s2-@e2)));
        UPDATE studentElo SET elo = @p1 WHERE id = new.from;
        UPDATE studentElo SET elo = @p2 WHERE id = new.to;
    END IF;
END$$
DELIMITER ;
