--REVIEWS/BUY--
--- Goodreads ---
--- Amazon ---
--- Barnes & Noble ---
--- Chapters-Indigo ---

--FULL--
--- Project Gutenberg ---
--- Bartleby ---
--- dailylit ---
--- WikiSource: http://en.wikisource.org ---
--- WikiSource: http://en.wikisource.org ---

--TRADE--
--- PaperbackSwap ---
--- BookRenter ---
--- Kindle Lending Library http://www.amazon.com/gp/feature.html/?docId=1000739811 ---
--- iSwap ---

--SEARCH--
--- Google Books ---
--- Google Scholar ---
--- Google PDF searches ---

-- AUDIO BOOKS --
--- Books Should Be Free ---
--- NewFiction ---
--- ThoughtAudio ---
--- LibriVox ---
--- Podiobooks ---
--- OpenCulture ---
--- LearnOutLoud ---
--- Librophile ---
--- StoryNory ---
--- Audible ---


-- BOOK (ISBN) --
CREATE TABLE book (book_id NOT NULL AUTONUM PRIMARY KEY, book_isbn CHAR(12), book_title VARCHAR(100), book_image VARCHAR(255), book_link VARCHAR(255), book_description VARCHAR(1000) );

INSERT INTO book (0968523501,'Bluebell in a Quarry','http://www.canadiangeographic.ca/atlas/Images/Glossary/Quarry.jpg','http://www.amazon.com/Bluebell-Quarry-Corpus-Christi-Millenium/dp/0968523501/','A novel challenging the Irish understanding of history and Christianity. - MEAGHER, John Mary. A Bluebell in a Quarry (Corpus Christi On the Eve of the Third Millenium). (Moncton, NB: Ahymsa Publishing, 1999). Pp 264. 8vo, ill. card covers. Vg. Signed. 15.00');
