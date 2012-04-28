CREATE TABLE `user` (
      `user_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,                        
	  `username` VARCHAR(20) NOT NULL,
	  `password` VARCHAR(20) NOT NULL,
	  `openid` varchar(255) DEFAULT NULL, 
	  `membership_date` TIMESTAMP 
);

CREATE TABLE `account` (
      `account_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,                        
	  `account_name` VARCHAR(20) NOT NULL,
	  `account_service_homepage` VARCHAR(100) NOT NULL,
	  `account_url` VARCHAR(50) NOT NULL,
	  `account_end_url` VARCHAR(50) DEFAULT NULL	  
);

CREATE TABLE `user_account` (
      `user_account_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,                        
	  `user_account_credential` VARCHAR(20) NOT NULL,
	  `account_id` BIGINT(20) NOT NULL,
	  `user_id` BIGINT(20) NOT NULL
);