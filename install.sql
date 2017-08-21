CREATE TABLE `youtubelinks` (
  `url` varchar(100) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `karma` (
  `name` varchar(255) NOT NULL,
  `karma` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `storage` (
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `info` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `quote` (
  `id` int(16) NOT NULL,
  `quote` varchar(450) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `youtubelinks` ADD PRIMARY KEY (`url`);
ALTER TABLE `karma` ADD PRIMARY KEY (`name`);
ALTER TABLE `storage` ADD PRIMARY KEY (`name`);
ALTER TABLE `quote` ADD PRIMARY KEY (`id`);
ALTER TABLE `info` ADD PRIMARY KEY (`id`);

ALTER TABLE `quote` MODIFY `id` int(16) NOT NULL AUTO_INCREMENT;
ALTER TABLE `info` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

INSERT INTO `storage` (`name`, `value`) VALUES ('discourse-last-reported-url', '');
INSERT INTO `storage` (`name`, `value`) VALUES ('discourse-online', 'true');
