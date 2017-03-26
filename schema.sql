CREATE TABLE IF NOT EXISTS `team` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `auid` varchar(8) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `name` varchar(35) DEFAULT NULL,
  `team` int(11) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `phone` int(11) DEFAULT NULL
);