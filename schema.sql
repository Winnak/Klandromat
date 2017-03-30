CREATE TABLE IF NOT EXISTS `student` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `auid` varchar(8) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `name` varchar(40) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `team` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(35) NOT NULL,
  `slug` varchar(35) NOT NULL,
  `creationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `value` int(11) NOT NULL DEFAULT 5
);

CREATE TABLE IF NOT EXISTS `role` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(35) NOT NULL
);

CREATE TABLE IF NOT EXISTS `teamstudent` (
  `teamid` int(11),
  `studentid` int(11),
  `roleid` int(11) DEFAULT 1,
   FOREIGN KEY (teamid) REFERENCES team(id),
   FOREIGN KEY (studentid) REFERENCES student(id),
   FOREIGN KEY (roleid) REFERENCES role(id)
);

CREATE TABLE IF NOT EXISTS `klandring` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `creationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `verdictdate` DATE,
  `title` varchar(120) NOT NULL,
  `description` text,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `verdict` int(3) NOT NULL DEFAULT 0,   -- 1 = from lost
                                         -- 2 = to   lost
                                         -- 3 = both lost
                                         -- 0 = not decided.
 `paid` BIT NOT NULL DEFAULT 0, -- 0 not paid, 1 paid.
  FOREIGN KEY (`from`) REFERENCES student(id),
  FOREIGN KEY (`to`) REFERENCES student(id)
);

INSERT INTO `role` (`id`, `name`) VALUES 
(1, 'member'),
(2, 'admin');