SELECT 'DICTIONARY-LIKE' AS section;

SELECT '"want" entry' AS comment;

SELECT lemma,pos,sensenum,synsetid,SUBSTRING(definition FROM 1 FOR 64),SUBSTRING(sampleset FROM 1 FOR 50)
FROM dict
WHERE lemma = 'want'
ORDER BY pos,sensenum;

SELECT '"like" verb entry' AS comment;

SELECT lemma,pos,sensenum,synsetid,SUBSTRING(definition FROM 1 FOR 64),SUBSTRING(sampleset FROM 1 FOR 50)
FROM dict
WHERE lemma = 'like' AND pos = 'v'
ORDER BY pos,sensenum;

SELECT '"want" as verb entry' AS comment;

SELECT lemma,pos,sensenum,SUBSTRING(definition FROM 1 FOR 64),SUBSTRING(sampleset FROM 1 FOR 50)
FROM dict
WHERE lemma = 'want' AND pos = 'v'
ORDER BY pos,sensenum;

SELECT '"want" as noun with tag information' AS comment;

SELECT lemma,pos,sensenum,tagcount,SUBSTRING(definition FROM 1 FOR 64)
FROM dict
WHERE lemma = 'want'
ORDER BY pos,sensenum;

SELECT 'starting with "fear"' AS comment;
SELECT lemma,pos,sensenum,SUBSTRING(definition FROM 1 FOR 64)
FROM dict
WHERE lemma LIKE 'fear%'
ORDER BY lemma,pos,sensenum;

SELECT 'ending with "wards"' AS comment;
SELECT lemma,pos,sensenum,SUBSTRING(definition FROM 1 FOR 64)
FROM dict
WHERE lemma LIKE '%wards'
ORDER BY lemma,pos,sensenum;

SELECT 'containing "ipod"' AS comment;
SELECT lemma,pos,sensenum,SUBSTRING(definition FROM 1 FOR 64)
FROM dict
WHERE lemma LIKE '%ipod%'
ORDER BY lemma,pos,sensenum;

SELECT 'staring with "rhino", ending with "s"' AS comment;
SELECT lemma,pos,sensenum,SUBSTRING(definition FROM 1 FOR 64)
FROM dict
WHERE lemma LIKE 'rhino%s'
ORDER BY lemma,pos,sensenum;

SELECT 'with lexical domain' AS comment;
SELECT lexdomainname,lemma,pos,sensenum,SUBSTRING(definition FROM 1 FOR 64)
FROM dict
INNER JOIN lexdomains USING(lexdomainid,pos)
WHERE lemma LIKE '%ipod%'
ORDER BY lexdomainid,lemma,pos,sensenum;

SELECT 'matching definition' AS comment;
SELECT SUBSTRING(definition FROM 1 FOR 64)
FROM synsets
WHERE pos= 'n' AND definition LIKE '(trademark)%'
LIMIT 20;

SELECT 'synset members' AS comment;
SELECT synsetid,pos,GROUP_CONCAT(lemma),SUBSTRING(definition FROM 1 FOR 60)
FROM dict
WHERE synsetid IN (201824736,201777210,201776952,200691665,201825962)
GROUP BY synsetid;

SELECT 'synset members' AS comment;
SELECT synsetid,pos,GROUP_CONCAT(lemma),SUBSTRING(definition FROM 1 FOR 60)
FROM dict
WHERE synsetid IN (300019731,300604897,300062626)
GROUP BY synsetid;

