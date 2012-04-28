-- API table for Web Services to integrate data from --
CREATE TABLE `api` (
	`api_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`api_title` VARCHAR(20), 
	`api_image` VARCHAR(100), 
	`api_link` VARCHAR(100), 
	`api_authentication` VARCHAR(20), 
	`api_authorization` VARCHAR(20), 
	`api_authorization_key` VARCHAR(20)
);

-- API Call type (i.e. account_info, timeline, ) --
CREATE TABLE `api_call` (
	`api_call_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`api_call_url` VARCHAR(20),
	`api_call_url_end` VARCHAR(20),
	`api_request` VARCHAR(20), -- template for a request --
	`api_request_method_id` VARCHAR(20), -- GET,POST,PUT,DELETE --
	`api_request_mime_id` VARCHAR(20), -- FK to mime.id -- 
	`api_response_mime_id` INT(20) -- FK to mime.id -- 
);

 