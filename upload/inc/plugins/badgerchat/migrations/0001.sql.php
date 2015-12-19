<?php
if(!defined("IN_MYBB")) {
  // Not going to give a message (people could easily tell if a site is running this)
  die();
}
echo "
CREATE TABLE IF NOT EXISTS `mybb_badgerchat_messages` (
  `Id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `SentAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uid` INT(10) UNSIGNED NOT NULL,
  `Ip` VARCHAR(15) NOT NULL,
  `Message` VARCHAR(200) NOT NULL,
  CONSTRAINT pk_badgerchat_messageId PRIMARY KEY(`Id`)
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `mybb_badgerchat_version` (
  `Version` INT UNSIGNED NOT NULL,
  `InstalledAt` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO mybb_badgerchat_version(`Version`) VALUES (1);
";