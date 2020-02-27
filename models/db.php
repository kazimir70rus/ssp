<?php


// CREATE DATABASE `msfm_tasker` /*!40100 COLLATE 'utf8_general_ci' */;


/*

CREATE TABLE `users` (
	`id_user` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`position` VARCHAR(50) NULL DEFAULT NULL,
	`pass` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`id_user`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `tasks` (
	`id_task` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_executor` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`id_author` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	`name` VARCHAR(50) NOT NULL DEFAULT '0',
	`data_begin` DATE NOT NULL,
	`data_end` DATE NOT NULL,
	PRIMARY KEY (`id_task`),
	INDEX `FK_tasks_users` (`id_executor`),
	INDEX `FK_tasks_users_2` (`id_author`),
	CONSTRAINT `FK_tasks_users` FOREIGN KEY (`id_executor`) REFERENCES `users` (`id_user`),
	CONSTRAINT `FK_tasks_users_2` FOREIGN KEY (`id_author`) REFERENCES `users` (`id_user`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


// update users set pass=password('123') where id_user=2;

// insert users (name, pass) values ('Сидоров', password('123'));

*/

