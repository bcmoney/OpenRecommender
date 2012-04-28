CREATE TABLE article (
	article_id BIGINT(20) NOT NULL PRIMARY KEY AUTO_INCREMENT, 
	article_title VARCHAR(50), 
	article_image VARCHAR(100), 
	article_link VARCHAR(100), 
	article_description VARCHAR(150), 	
	article_published DATETIME DEFAULT NULL,
	article_author_user_id BIGINT(20) DEFAULT NULL,
	article_news_id VARCHAR(255),
	article_last_viewed TIMESTAMP
);