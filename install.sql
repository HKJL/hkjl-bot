CREATE TABLE `karma` (
  `name` varchar(255) NOT NULL,
  `karma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `storage` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `storage` (`name`, `value`) VALUES ('discourse-last-reported-url', '');

ALTER TABLE `karma` ADD PRIMARY KEY (`name`);
ALTER TABLE `storage` ADD PRIMARY KEY (`name`);
