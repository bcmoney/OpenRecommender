SELECT 'VERBFRAMES' AS section;

SELECT 'get verb frames for "snow"' AS comment;
SELECT lemma,frame,SUBSTRING(definition FROM 1 FOR 32)
FROM vframemaps 
INNER JOIN vframes USING (frameid)
INNER JOIN words USING (wordid)
INNER JOIN synsets USING (synsetid)
WHERE lemma = 'snow';

SELECT 'get example sentence for "snow"' AS comment;
SELECT lemma,sentence,SUBSTRING(definition FROM 1 FOR 32)
FROM vframesentencemaps
LEFT JOIN vframesentences USING (sentenceid)
INNER JOIN words USING (wordid)
INNER JOIN synsets USING (synsetid)
WHERE lemma = 'snow';

SELECT 'get verb frames for "clean"' AS comment;
SELECT lemma,frame,SUBSTRING(definition FROM 1 FOR 32)
FROM vframemaps 
INNER JOIN vframes USING (frameid)
RIGHT JOIN words USING (wordid)
RIGHT JOIN synsets USING (synsetid)
WHERE lemma = 'clean'
ORDER BY synsetid;

SELECT 'get example sentence for "clean"' AS comment;
SELECT lemma,sentence,SUBSTRING(definition FROM 1 FOR 32)
FROM vframesentencemaps
LEFT JOIN vframesentences USING (sentenceid)
RIGHT JOIN words USING (wordid)
RIGHT JOIN synsets USING (synsetid)
WHERE lemma = 'clean'
ORDER BY synsetid;

SELECT 'get verb frames for "write"' AS comment;
SELECT lemma,frame,SUBSTRING(definition FROM 1 FOR 32)
FROM vframemaps 
INNER JOIN vframes USING (frameid)
RIGHT JOIN words USING (wordid)
RIGHT JOIN synsets USING (synsetid)
WHERE lemma = 'write'
ORDER BY synsetid;

SELECT 'get example sentence for "write"' AS comment;
SELECT lemma,sentence,SUBSTRING(definition FROM 1 FOR 32)
FROM vframesentencemaps
LEFT JOIN vframesentences USING (sentenceid)
RIGHT JOIN words USING (wordid)
RIGHT JOIN synsets USING (synsetid)
WHERE lemma = 'write'
ORDER BY synsetid;

SELECT 'get verb frames and example sentence grouped by synset for "write"' AS comment;
SELECT lemma,SUBSTRING(definition FROM 1 FOR 32),SUBSTRING(GROUP_CONCAT(frame) FROM 1 FOR 70),SUBSTRING(GROUP_CONCAT(sentence) FROM 1 FOR 32)
FROM vframemaps 
INNER JOIN vframes USING (frameid)
LEFT JOIN vframesentencemaps USING (synsetid,wordid)
LEFT JOIN vframesentences USING (sentenceid)
RIGHT JOIN words USING (wordid)
RIGHT JOIN synsets USING (synsetid)
WHERE lemma = 'write'
GROUP BY synsetid
ORDER BY synsetid;

SELECT 'verbframes matching n senses' AS comment;
SELECT frameid,frame,COUNT(*) AS N
FROM verbswithframes
GROUP BY frameid
ORDER BY N DESC;

SELECT 'sense matching n verbframes' AS comment;
SELECT sensekey,definition,COUNT(*) AS N
FROM verbswithframes
GROUP BY senseid
ORDER BY N DESC
LIMIT 20;

SELECT 'verbframes for "prepare"' AS comment;
SELECT lemma,synsetid,frame,SUBSTRING(definition FROM 1 FOR 32)
FROM verbswithframes
WHERE lemma = 'prepare' AND synsetid = 200406243;

