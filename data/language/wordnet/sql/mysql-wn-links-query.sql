SELECT 'LINKS' AS section;

SELECT 'get words lexically-linked to "horse"' AS comment;
SELECT ssensenum,sw.lemma,link,dw.lemma AS linkedlemma,SUBSTRING(sdefinition FROM 1 FOR 60) FROM sensesXsemlinksXsenses AS l
LEFT JOIN words AS sw ON l.swordid = sw.wordid
LEFT JOIN words AS dw ON l.dwordid = dw.wordid
LEFT JOIN linktypes USING (linkid)
WHERE sw.lemma = 'horse' 
ORDER BY linkid,ssensenum;

SELECT 'get hypernyms for "horse"' AS comment;
SELECT ssensenum,sw.lemma,dw.lemma AS hypernym,SUBSTRING(sdefinition FROM 1 FOR 60) FROM sensesXsemlinksXsenses AS l
LEFT JOIN words AS sw ON l.swordid = sw.wordid
LEFT JOIN words AS dw ON l.dwordid = dw.wordid
WHERE sw.lemma = 'horse' AND linkid=1 AND spos='n'
ORDER BY ssensenum;

SELECT 'get hyponyms for "horse"' AS comment;
SELECT ssensenum,sw.lemma,dw.lemma AS hyponym,SUBSTRING(sdefinition FROM 1 FOR 60) FROM sensesXsemlinksXsenses AS l
LEFT JOIN words AS sw ON l.swordid = sw.wordid
LEFT JOIN words AS dw ON l.dwordid = dw.wordid
WHERE sw.lemma = 'horse' AND linkid=2 AND spos='n'
ORDER BY ssensenum;

SELECT 'get words lexically-linked to "black"' AS comment;
SELECT ssensenum,sw.lemma,link,dw.lemma AS linked,SUBSTRING(sdefinition FROM 1 FOR 60) FROM sensesXlexlinksXsenses AS l
LEFT JOIN words AS sw ON l.swordid = sw.wordid
LEFT JOIN words AS dw ON l.dwordid = dw.wordid
LEFT JOIN linktypes USING (linkid)
WHERE sw.lemma = 'black'
ORDER BY linkid,ssensenum;

SELECT 'get antonyms for "black"' AS comment;
SELECT ssensenum,sw.lemma,dw.lemma AS antonym,SUBSTRING(sdefinition FROM 1 FOR 60) FROM sensesXlexlinksXsenses AS l
LEFT JOIN words AS sw ON l.swordid = sw.wordid
LEFT JOIN words AS dw ON l.dwordid = dw.wordid
WHERE sw.lemma = 'black' AND linkid=30 AND spos='a'
ORDER BY ssensenum;

SELECT 'get synsetids "option" is member of' AS comment;
SELECT lemma,sensenum,synsetid
FROM wordsXsenses
WHERE lemma='option'
ORDER BY sensenum;

SELECT 'get synsets "option" is member of' AS comment;
SELECT lemma,sensenum,synsetid,pos,SUBSTRING(definition FROM 1 FOR 60)
FROM wordsXsensesXsynsets
WHERE lemma='option'
ORDER BY sensenum;

SELECT 'get synset members' AS comment;
SELECT synsetid,lemma
FROM wordsXsensesXsynsets
WHERE synsetid = 100161243;

SELECT 'get comma-separated list of synset members' AS comment;
SELECT synsetid,GROUP_CONCAT(lemma)
FROM wordsXsensesXsynsets
WHERE synsetid = 100161243
GROUP BY synsetid;

SELECT 'get synsets' AS comment;
SELECT synsetid,lemma
FROM wordsXsensesXsynsets
WHERE lemma = 'option';

SELECT 'synonyms' AS comment;
SELECT synsetid,dest.lemma,SUBSTRING(src.definition FROM 1 FOR 60)
FROM wordsXsensesXsynsets AS src 
INNER JOIN wordsXsensesXsynsets AS dest USING(synsetid)
WHERE src.lemma = 'option' AND dest.lemma <> 'option';

SELECT 'synonyms (alternative)' AS comment;
SELECT synsetid,lemma,SUBSTRING(definition FROM 1 FOR 60)
FROM wordsXsensesXsynsets
WHERE synsetid IN (
	SELECT synsetid
	FROM wordsXsensesXsynsets
	WHERE lemma = 'option') AND lemma <> 'option'
ORDER BY synsetid;

