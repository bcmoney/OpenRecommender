SELECT 'CARDINALITY OF ASSOCIATIONS' AS section;

SELECT 'GENERAL' AS subsection;

SELECT 'synset having N words (1..28)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(wordid) AS N FROM synsets
LEFT JOIN senses USING(synsetid)
GROUP BY synsetid) AS C;

SELECT 'word having N synset (1..75)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(synsetid) AS N FROM words
LEFT JOIN senses USING(wordid)
GROUP BY wordid) AS C;

SELECT 'MORPHOLOGY' AS subsection;

SELECT 'morph having N words (1..3)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(wordid) AS N FROM morphs
LEFT JOIN morphmaps USING(morphid)
GROUP BY morphid) AS C;

SELECT 'word having N morphs (0..7)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(morphid) AS N FROM words
LEFT JOIN morphmaps USING(wordid)
GROUP BY wordid) AS C;

SELECT 'VERBFRAMES' AS subsection;

SELECT 'sense having N vframes (0..8)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(frameid) AS N FROM senses 
LEFT JOIN vframemaps USING(wordid,synsetid)
GROUP BY synsetid,wordid) AS C;

SELECT 'vframe having N senses (1..801)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(CONCAT(wordid,synsetid)) AS N FROM vframes 
LEFT JOIN vframemaps USING(frameid)
GROUP BY frameid) AS C;

SELECT 'VERBFRAMES SENTENCES' AS comment;

SELECT 'sense having N vframesentences (0..8)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(sentenceid) AS N FROM senses 
LEFT JOIN vframesentencemaps USING(wordid,synsetid)
GROUP BY synsetid,wordid) AS C;

SELECT 'vframesentences having N senses (1..801)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(CONCAT(wordid,synsetid)) AS N FROM vframesentences 
LEFT JOIN vframesentencemaps USING(sentenceid)
GROUP BY sentenceid) AS C;

SELECT 'LEGACY SYNSETS' AS subsection;

SELECT 'synsets21' AS comment;
SELECT COUNT(*) FROM synsets21;

SELECT 'synsets20' AS comment;
SELECT COUNT(*) FROM synsets20;

SELECT 'LEGACY SENSEMAPS' AS subsection;

SELECT 'senses30 having N senses20 (1..5)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(*) AS N FROM senses
LEFT JOIN sensemaps2030 USING(wordid,synsetid)
GROUP BY wordid,synsetid) AS C;

SELECT 'senses20 having N senses30 ()' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(CONCAT(wordid,synsetid)) AS N FROM senses20
LEFT JOIN sensemaps2030 USING(wordid,synsetid)
GROUP BY wordid,synsetid) AS C;

SELECT 'LEGACY SYNSETMAPS' AS subsection;

SELECT 'synsets having N synsets21 (0..5)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(synsetid2) AS N FROM synsets
LEFT JOIN synsetmaps2130 USING(synsetid)
GROUP BY synsetid) AS C;

SELECT 'synsets having N synsets20 (0..7)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(synsetid2) AS N FROM synsets
LEFT JOIN synsetmaps2030 USING(synsetid)
GROUP BY synsetid) AS C;

SELECT 'synsets21 having N synsets30 (1..8)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(synsetid) N FROM synsetmaps2130 GROUP BY synsetid2) AS C;

SELECT 'synsets20 having N synsets30 (1..8)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(synsetid) N FROM synsetmaps2030 GROUP BY synsetid2) AS C;

SELECT 'VERBNET : CLASSES' AS subsection;

SELECT 'words having N vnclasses (0..10)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(classid) AS N FROM words 
LEFT JOIN vnclassmembers USING(wordid)
GROUP BY wordid) AS C;

SELECT 'vnclasses having N words (1..801)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(wordid) AS N FROM vnclasses 
LEFT JOIN vnclassmembers USING(classid)
GROUP BY classid) AS C;

SELECT 'VERBNET : FRAMES' AS comment;

SELECT 'sense having N vnframes (0..37)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(frameid) AS N FROM senses 
LEFT JOIN vnframemaps USING(wordid,synsetid)
GROUP BY synsetid,wordid) AS C;

SELECT 'frame having N senses (1..801)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(CONCAT(wordid,synsetid)) AS N FROM vnframes 
LEFT JOIN vnframemaps USING(frameid)
GROUP BY frameid) AS C;

SELECT 'VERBNET : ROLES' AS subsection;

SELECT 'sense having N vnroles (0..14)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(roleid) AS N FROM senses 
LEFT JOIN vnrolemaps USING(wordid,synsetid)
GROUP BY synsetid,wordid) AS C;

SELECT 'vnrole having N senses (2..1901)' AS comment;
SELECT MIN(N),MAX(N) FROM (
SELECT COUNT(CONCAT(wordid,synsetid)) AS N FROM vnroles 
LEFT JOIN vnrolemaps USING(roleid)
GROUP BY roleid) AS C;

