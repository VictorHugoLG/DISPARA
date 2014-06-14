<?php
	/*
	* Script para criacao de tabelas e views do Comunique.
	* 
	* @author	Lucas Rocha
	*/
	define('BASEPATH', 'application/config/database.php');
	require_once BASEPATH;
	$user = (!empty($_GET['user'])) ? $_GET['user'] : 'lucas';
	$password = (!empty($_GET['password'])) ? $_GET['password'] : $user;
	$sql_queries = array(
		//basic config
		'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";',
		'SET time_zone = "+00:00";',
		'USE `'.$db['default']['database'].'`;',
		//Estrutura da tabela `ci_sessions`
		'CREATE TABLE IF NOT EXISTS `ci_sessions` (
		  `session_id` varchar(40) NOT NULL DEFAULT \'0\',
		  `ip_address` varchar(45) NOT NULL DEFAULT \'0\',
		  `user_agent` varchar(120) NOT NULL,
		  `last_activity` int(10) unsigned NOT NULL DEFAULT \'0\',
		  `user_data` text NOT NULL,
		  PRIMARY KEY (`session_id`),
		  KEY `last_activity_idx` (`last_activity`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
		// Estrutura da tabela `mail_conf`
		'CREATE TABLE IF NOT EXISTS `mail_conf` (
		  `id` int(11) NOT NULL,
		  `description` varchar(200) DEFAULT NULL,
		  `smtp_auth` tinyint(1) NOT NULL,
		  `smtp_secure` varchar(20) DEFAULT NULL,
		  `host` varchar(50) NOT NULL,
		  `username` varchar(100) DEFAULT NULL,
		  `password` varchar(50) DEFAULT NULL,
		  `port` int(5) NOT NULL,
		  `from` varchar(100) NOT NULL,
		  `from_name` varchar(100) DEFAULT NULL,
		  `reply_to` varchar(100) DEFAULT NULL,
		  `reply_to_name` varchar(100) DEFAULT NULL,
		  `active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
		//Estrutura da tabela `mail_data`
		'CREATE TABLE IF NOT EXISTS `mail_data` (
		  `id` int(11) NOT NULL,
		  `html` varchar(10000) DEFAULT NULL,
		  `subject` varchar(100) DEFAULT NULL,
		  `dt_begin` date NOT NULL,
		  `dt_end` date NOT NULL,
		  `dttm_changed` datetime NOT NULL,
		  `changed_by` varchar(50) NOT NULL,
		  `name` varchar(100) NOT NULL,
		  `sms` varchar(1000) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
		//Estrutura da tabela `mail_list`
		'CREATE TABLE IF NOT EXISTS `mail_list` (
		  `mail_data_id` int(11) NOT NULL,
		  `domain` varchar(50) NOT NULL,
		  `prefix` varchar(50) NOT NULL,
		  `status` varchar(20) NOT NULL,
		  `dttm_changed` datetime NOT NULL,
		  `name` varchar(100) NOT NULL,
		  `legacy_id` varchar(50) NOT NULL,
		  `changed_by` varchar(50) NOT NULL,
		  PRIMARY KEY (`mail_data_id`,`domain`,`prefix`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
		//Estrutura da tabela `user`
		'CREATE TABLE IF NOT EXISTS `user` (
		  `name` varchar(50) NOT NULL,
		  `password` varchar(50) NOT NULL,
		  PRIMARY KEY (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;',
		//Extraindo dados da tabela `user`
		"INSERT INTO `user` (`name`, `password`) VALUES('$user', '$password');",
		//Estrutura stand-in para a view `vw_mail_schedule`
		'CREATE TABLE IF NOT EXISTS `vw_mail_schedule` (
		`mail_data_id` int(11)
		,`html` varchar(10000)
		,`subject` varchar(100)
		,`email` varchar(101)
		,`name` varchar(100)
		);',
		//Estrutura stand-in para a view `vw_sms_schedule`
		'CREATE TABLE IF NOT EXISTS `vw_sms_schedule` (
		`mail_data_id` int(11)
		,`sms` varchar(1000)
		,`phone` varchar(100)
		,`name` varchar(100)
		);',
		//Estrutura para a view `vw_mail_schedule`
		'DROP TABLE IF EXISTS `vw_mail_schedule`;',
		"CREATE VIEW `vw_mail_schedule` AS select `mail_data`.`id` AS `mail_data_id`,`mail_data`.`html` AS `html`,`mail_data`.`subject` AS `subject`,concat(`mail_list`.`prefix`,'@',`mail_list`.`domain`) AS `email`,`mail_list`.`name` AS `name` from (`mail_data` join `mail_list`) where ((`mail_data`.`id` = `mail_list`.`mail_data_id`) and (`mail_data`.`dt_begin` <= curdate()) and (`mail_data`.`dt_end` > curdate()) and (`mail_list`.`status` = 'AGENDADO') and (not(concat(`mail_list`.`prefix`,'@',`mail_list`.`domain`) in (select concat(`mail_list`.`prefix`,'@',`mail_list`.`domain`) from `mail_list` where (`mail_list`.`status` = 'REJEITADO')))) and (not((`mail_list`.`domain` regexp '[[:digit:]]')))) group by `mail_list`.`domain` limit 9;",
		//Estrutura para a view `vw_sms_schedule`
		'DROP TABLE IF EXISTS `vw_sms_schedule`;',
		"CREATE VIEW `vw_sms_schedule` AS select `mail_data`.`id` AS `mail_data_id`,`mail_data`.`sms` AS `sms`,concat(`mail_list`.`prefix`,`mail_list`.`domain`) AS `phone`,`mail_list`.`name` AS `name` from (`mail_data` join `mail_list`) where ((`mail_data`.`id` = `mail_list`.`mail_data_id`) and (`mail_data`.`dt_begin` <= curdate()) and (`mail_data`.`dt_end` > curdate()) and (`mail_list`.`status` = 'AGENDADO') and (`mail_list`.`domain` regexp '[[:digit:]]')) limit 9;");
	//connect
	$mysqli = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);
	//check connection 
	if ($mysqli->connect_errno) die("Connection failed: %s\n".$mysqli->connect_error);
	//run queries
	foreach ($sql_queries as $sql) {
		$query = $mysqli->query($sql);
		if (!empty($mysqli->error)) echo "Database error: ".$mysqli->error.'<br>';
	}
	echo utf8_decode("Se nenhuma mensagem de erro apareceu até aqui, a estrutura do banco de dados foi montada com o usuário: $user / senha: $password ");
?>