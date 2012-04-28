SELECT 'CASED FORMS' AS section;

SELECT * FROM words
WHERE lemma='neolithic';

SELECT * FROM words
INNER JOIN casedwords USING (wordid)
WHERE lemma='neolithic';

SELECT * FROM words
INNER JOIN casedwords USING (wordid)
WHERE lemma='shakespeare';

SELECT * FROM words
INNER JOIN casedwords USING (wordid)
WHERE cased='shaKEspeare';

SELECT * FROM words
INNER JOIN casedwords USING (wordid)
WHERE cased='Shakespeare';

SELECT * FROM words
INNER JOIN casedwords USING (wordid)
WHERE lemma='am';

SELECT * FROM words
LEFT JOIN casedwords USING (wordid)
WHERE lemma='abolition';

SELECT cased,definition 
FROM senses
INNER JOIN casedwords USING (wordid,casedwordid)
INNER JOIN synsets USING (synsetid)
WHERE cased='Jackson';

SELECT cased,definition 
FROM senses
INNER JOIN casedwords USING (wordid,casedwordid)
INNER JOIN synsets USING (synsetid)
WHERE cased='C';

SELECT lemma,cased,SUBSTRING(definition FROM 1 FOR 64)
FROM senses
INNER JOIN words USING (wordid)
LEFT JOIN casedwords USING (wordid,casedwordid)
INNER JOIN synsets USING (synsetid)
WHERE lemma='C';

SELECT 'lemma having several cased forms' AS comment;
SELECT wordid,COUNT(casedwordid) AS N,GROUP_CONCAT(cased) 
FROM casedwords
GROUP BY wordid
HAVING N > 1
ORDER BY N DESC;

SELECT 'senses having a cased form' AS comment;
SELECT COUNT(*)
FROM senses
WHERE NOT ISNULL(casedwordid);

SELECT 'words having a cased form' AS comment;
SELECT COUNT(*)
FROM words
INNER JOIN casedwords USING (wordid);

