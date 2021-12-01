CREATE TABLE IF NOT EXISTS `#__request_list` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`created_by` INT(10)  NOT NULL ,
`requested_by` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`product` VARCHAR(128)  NOT NULL ,
`product_id` INT(10)  NOT NULL ,
`status` TINYINT(3)  NOT NULL ,
`updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`plan` VARCHAR(128)  NOT NULL ,
`plan_id` INT(10)  NOT NULL ,
`org_id` INT(10)  NOT NULL ,
`user_note` TEXT NOT NULL ,
`admin_note` TEXT NOT NULL ,
`custom` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

