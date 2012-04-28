-- RENAME AS PER V1 - mysql

-- ##################################################
--	word			words
--		wordid			wordid
--		lemma			lemma
ALTER TABLE words RENAME TO word;

-- ##################################################
--	casedword		casedwords
--		wordid			casedwordid
--		lemma			cased
--					wordid
ALTER TABLE casedwords RENAME TO casedword,
	DROP COLUMN wordid,
	CHANGE COLUMN casedwordid wordid MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	CHANGE COLUMN cased lemma VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT NULL,
	DROP INDEX k_casedwords_wordid,
	DROP INDEX unq_casedwords_cased,
	ADD UNIQUE INDEX unq_casedwords_lemma USING BTREE(lemma);

-- ##################################################
--	sense			senses
--		wordid			wordid
--		casedwordid		casedwordid
--		synsetid		synsetid
--		rank			sensenum
--		lexid			lexid
--		tagcount		tagcount
--					senseid
--					sensekey
ALTER TABLE senses RENAME TO sense,
	CHANGE COLUMN sensenum rank TINYINT(3) UNSIGNED NOT NULL DEFAULT '0';

-- ##################################################
--	synset			synsets
--		synsetid		synsetid
--		pos			pos
--		categoryid		lexdomainid
--		definition		definition
ALTER TABLE synsets RENAME TO synset,
	CHANGE COLUMN lexdomainid categoryid TINYINT(3) UNSIGNED NOT NULL DEFAULT 0;

-- ##################################################
--	linkdef			linktypes
--		linkid			linkid
--		name			link
--		recurses		recurses
ALTER TABLE linktypes RENAME TO linkdef
	CHANGE COLUMN link name VARCHAR(50) DEFAULT NULL;

-- ##################################################
--	lexlinkref		lexlinks
--		synset1id		synset1id
--		word1id			word1id
--		synset2id		synset2id
--		word2id			word2id
--		linkid			linkid
ALTER TABLE lexlinks RENAME TO lexlinkref;

-- ##################################################
--	semlinkref		semlinks
--		synset1id		synset1id
--		synset2id		synset2id
--		linkid			linkid
ALTER TABLE semlinks RENAME TO semlinkref;

-- ##################################################
--	sample			samples
--		synsetid		synsetid
--		sampleid		sampleid
--		sample			sample
ALTER TABLE samples RENAME TO sample;

-- ##################################################
--	categorydef		lexdomains
--		categoryid		lexdomainid
--		name			lexdomainname
--		pos			pos
ALTER TABLE lexdomains RENAME TO categorydef,
	CHANGE COLUMN lexdomainid categoryid TINYINT(3) UNSIGNED NOT NULL DEFAULT 0,
	DROP PRIMARY KEY,
	ADD PRIMARY KEY USING BTREE(lexdomainid);

-- ##################################################
--	morphdef		morphs
--		morphid			morphid
--		lemma			morph
ALTER TABLE morphs RENAME TO morphdef,
	CHANGE COLUMN morph lemma VARCHAR(70) NOT NULL,
	DROP INDEX unq_morphs_morph,
	ADD UNIQUE INDEX unq_morphs_lemma USING BTREE(lemma);

-- ##################################################
--	morphref		morphmaps
--		wordid			wordid
--		pos			pos
--		morphid			morphid
ALTER TABLE morphmaps RENAME TO morphref;

-- ##################################################
--	sentencedef		vframesentences
--		sentenceid		sentenceid
--		sentence		sentence
ALTER TABLE vframesentences RENAME TO sentencedef;

-- ##################################################
--	sentenceref		vframesentencemaps
--		synsetid		synsetid
--		wordid			wordid
--		sentenceid		sentenceid
ALTER TABLE vframesentencemaps RENAME TO sentenceref;

-- ##################################################
--	wordposition		adjpositions
--		synsetid		synsetid
--		wordid			wordid
--		positionid		position
ALTER TABLE adjpositions RENAME TO wordposition,
	CHANGE COLUMN position positionid ENUM('a','p','ip') NOT NULL DEFAULT 'a';

-- ##################################################
--	framedef		vframes
--		frameid			frameid
--		frame			frame
ALTER TABLE vframes RENAME TO framedef;

-- ##################################################
--	frameref		vframemaps
--		synsetid		synsetid
--		wordid			wordid
--		frameid			frameid
ALTER TABLE vframemaps RENAME TO frameref;

