-- Database Creation
CREATE DATABASE openrecommender;
USE openrecommender;
-- Table Script Creation
CREATE TABLE recommendation(
   recommendation_id serial
   ,   recommendation_user_id Int
   ,   recommendation_item_id Int
   ,   recommendation_algorithm_id Int
   ,PRIMARY KEY ()
) TYPE = INNODB;

CREATE TABLE item(
   item_id serial
   ,   item_title varchar(100)
   ,   item_image varchar(100)
   ,   item_link varchar(100)
   ,   item_description varchar(500)
   ,   item_embed text
   ,PRIMARY KEY (item_id)
) TYPE = INNODB;

CREATE TABLE user(
   user_id serial
   ,   user_email varchar(100)
   ,   user_password varchar(50)
   ,PRIMARY KEY (user_id)
) TYPE = INNODB;

CREATE TABLE algorithm(
   algorithm_id serial
   ,   algorithm_name varchar(20)
   ,PRIMARY KEY (algorithm_id)
) TYPE = INNODB;

CREATE TABLE activity(
   activity_id serial
   ,   activity_timestamp DateTime
   ,PRIMARY KEY (activity_id)
) TYPE = INNODB;

CREATE TABLE audio(
   audio_id serial
   ,   audio_timestamp DateTime
   ,PRIMARY KEY (audio_id)
) TYPE = INNODB;

CREATE TABLE device(
   device_id varchar(20)
   ,   device_timestamp DateTime
   ,PRIMARY KEY (device_id)
) TYPE = INNODB;

CREATE TABLE discussion(
   discussion_id serial
   ,   discussion_timestamp DateTime
   ,PRIMARY KEY (discussion_id)
) TYPE = INNODB;

CREATE TABLE education(
   education_id serial
   ,   education_timestamp DateTime
   ,PRIMARY KEY (education_id)
) TYPE = INNODB;

CREATE TABLE environment(
   environment_id serial
   ,   environment_timestamp DateTime
   ,PRIMARY KEY (environment_id)
) TYPE = INNODB;

CREATE TABLE event(
   event_id serial
   ,   event_timestamp DateTime
   ,PRIMARY KEY (event_id)
) TYPE = INNODB;

CREATE TABLE video(
   video_id serial
   ,   video_timestamp DateTime
   ,PRIMARY KEY (video_id)
) TYPE = INNODB;

CREATE TABLE food(
   food_id serial
   ,   food_timestamp DateTime
   ,PRIMARY KEY (food_id)
) TYPE = INNODB;

CREATE TABLE image(
   image_id serial
   ,   image_timestamp DateTime
   ,PRIMARY KEY (image_id)
) TYPE = INNODB;

CREATE TABLE language(
   language_id serial
   ,   language_i18n Int
   ,   language_l10n Int
   ,PRIMARY KEY (language_id)
) TYPE = INNODB;

CREATE TABLE license(
   license_id serial
   ,   license_timestamp DateTime
   ,PRIMARY KEY (license_id)
) TYPE = INNODB;

CREATE TABLE money(
   money_id serial
   ,   money_timestamp DateTime
   ,PRIMARY KEY (money_id)
) TYPE = INNODB;

CREATE TABLE news(
   news_id Int
   ,   news_timestamp DateTime
   ,PRIMARY KEY (news_id)
) TYPE = INNODB;

CREATE TABLE organization(
   organization serial
   ,   organization_timestamp DateTime
   ,PRIMARY KEY (organization)
) TYPE = INNODB;

CREATE TABLE person(
   person_id serial
   ,   person_timestamp DateTime
   ,PRIMARY KEY (person_id)
) TYPE = INNODB;

CREATE TABLE place(
   place_id serial
   ,   place_timestamp DateTime
   ,PRIMARY KEY (place_id)
) TYPE = INNODB;

CREATE TABLE product(
   product_id serial
   ,   product_timestamp DateTime
   ,PRIMARY KEY (product_id)
) TYPE = INNODB;

CREATE TABLE profession(
   profession_id serial
   ,   profession_timestamp DateTime
   ,PRIMARY KEY (profession_id)
) TYPE = INNODB;

CREATE TABLE situation(
   situation_id serial
   ,   situation_timestamp DateTime
   ,PRIMARY KEY (situation_id)
) TYPE = INNODB;

CREATE TABLE species(
   species_id serial
   ,   species_timestamp DateTime
   ,PRIMARY KEY (species_id)
) TYPE = INNODB;

CREATE TABLE text(
   text_id serial
   ,   text_timestamp DateTime
   ,PRIMARY KEY (text_id)
) TYPE = INNODB;

CREATE TABLE unit(
   unit_id serial
   ,   unit_timestamp DateTime
   ,PRIMARY KEY (unit_id)
) TYPE = INNODB;

CREATE TABLE universe(
   universe_id serial
   ,   universe_timestamp DateTime
   ,PRIMARY KEY (universe_id)
) TYPE = INNODB;

CREATE TABLE conference(
   conference_id serial
   ,PRIMARY KEY (conference_id)
) TYPE = INNODB;

CREATE TABLE movie(
   movie_id serial
   ,PRIMARY KEY (movie_id)
) TYPE = INNODB;

CREATE TABLE series(
   series_id serial
   ,PRIMARY KEY (series_id)
) TYPE = INNODB;

CREATE TABLE tv(
   tv_id serial
   ,PRIMARY KEY (tv_id)
) TYPE = INNODB;

CREATE TABLE ugc(
   ugc_id serial
   ,PRIMARY KEY (ugc_id)
) TYPE = INNODB;
-- Relationships Creation
ALTER TABLE item ADD  CONSTRAINT recommendation_item_CON FOREIGN KEY(item_id) REFERENCES recommendation (recommendation_item_id);
ALTER TABLE user ADD  CONSTRAINT recommendation_user_CON FOREIGN KEY(user_id) REFERENCES recommendation (recommendation_user_id);
ALTER TABLE algorithm ADD  CONSTRAINT recommendation_algorithm_CON FOREIGN KEY(algorithm_id) REFERENCES recommendation (recommendation_algorithm_id);