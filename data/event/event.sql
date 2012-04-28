CREATE TABLE event (event_id NOT NULL AUTONUM AS PRIMARY KEY, event_title VARCHAR(100), event_image VARCHAR(255), event_link VARCHAR(255), event_description VARCHAR(1000), event_start_date DATE, event_start_time TIME, event_end_date DATE, event_end_time TIME, event_repeats BOOLEAN);

INSERT INTO event (1,'U2 360 Tour - U2 with special guests Arcade Fire (Magnetic Hill Music Festival)','http://upload.wikimedia.org/wikipedia/en/3/3c/U2-360-tour-logo.png','http://www.u2.com/tour/index/#bottom','Live Nation Global Touring in association with Donald K Donald Events and The City of Moncton have today confirmed that U2 will come to Atlantic Canada for the first time ever! The final North American date of the U2 360° Tour will visit Magnetic Hill Music Festival in Moncton, New Brunswick on July 30th, 2011 with very special guest Arcade Fire. Tickets go on sale to the public on Tuesday, February 8th at 10am.',2011-07-30,20:00:00,2011-07-30,24:00:00,0);

-- U2 in Moncton --
INSERT INTO event_location (1,300);


-- EVENT SOURCES --
Facebook
MySpace
Eventful
Upcoming
Twitter
City Event Calendars