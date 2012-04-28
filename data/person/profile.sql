-- User Archetypes for overcoming the Cold-Start problem --
CREATE TABLE `archetype` (
	`archetype_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`archetype_name` VARCHAR(20) PRIMARY KEY NOT NULL AUTO_INCREMENT	
);

-- User data collected or gleaned from external accounts/sources (FB, Twitter, Last.FM, IMDB, Google, etc) ---
CREATE TABLE `profile` (
      `profile_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	  `profile_name` VARCHAR(20) NOT NULL,
	  -- scoring criteria --
	  `profile_archetype` VARCHAR(20) NOT NULL -- nearest archetype match (upon which algorithms will be constantly trained and improved) --
	  `profile_type` VARCHAR(20) NOT NULL -- introverted or extroverted --
	  `profile_iq` VARCHAR(20) NOT NULL,	  
	  `profile_eq` VARCHAR(20) NOT NULL,	  
	  `profile_labarrie` VARCHAR(20) NOT NULL,
	  `profile_nostalgia` VARCHAR(20) NOT NULL,
	  `profile_loneliness` VARCHAR(20) NOT NULL,	  
	  `profile_crowd` VARCHAR(20) NOT NULL,	  
	  `profile_interests` VARCHAR(20) NOT NULL,	  
	  `profile_budget` VARCHAR(20) NOT NULL,	  
	  `profile_wisdom` VARCHAR(20) NOT NULL,
      `profile_mood_id` VARCHAR(20) NOT NULL,	  
      `profile_education_category_id` VARCHAR(20) NOT NULL, -- highest level of education and schools (optional) --
	  `profile_audio_category_id` VARCHAR(20) NOT NULL,
	  `profile_video_category_id` VARCHAR(20) NOT NULL,
	  `profile_image_category_id` VARCHAR(20) NOT NULL,	  
	  `profile_news_category_id` VARCHAR(20) NOT NULL,	  
	  `profile_product_category_id` VARCHAR(20) NOT NULL,	  
	  `profile_device_id` VARCHAR(20) NOT NULL,	 -- any devices owned --	  
	  `profile_18n_id` VARCHAR(20) NOT NULL,
	  `profile_l10n_id` VARCHAR(20) NOT NULL,
	  `profile_taxonomy_id` VARCHAR(20) NOT NULL, -- interested in/most time spent on News, Video, Images or Audio (Music)? --
	  
);

-- User slotting --
CREATE TABLE `user_profile` (
      `user_profile_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	  `user_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	  `profile_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,	  
);


-- (M x N x Q) ranking matrix : Archetype(criteria) x Taxonomy(weight) x Algorithm(selection) --
CREATE TABLE `user_profile_matrix` (
      `user_profile_matrix_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	  `user_profile_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	  `taxonomy_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	  `algorithm_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT
);