SELECT 'MORPHOLOGY' AS section;

SELECT 'lemma from morph' AS comment;
SELECT * 
FROM morphology
WHERE morph = 'better';

SELECT 'morph from lemma' AS comment;
SELECT * 
FROM morphology
WHERE lemma = 'good';

SELECT 'chivy-chevied,chevies,chevying,chivied,chivvied,chivvies,chivvying' AS comment;
SELECT lemma,pos,morph
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
WHERE lemma = 'chivy';

SELECT 'quiz(n,v)-quizzes' AS comment;
SELECT lemma,pos,morph
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
WHERE lemma = 'quiz';

SELECT 'gas(n,v)-gasses' AS comment;
SELECT lemma,pos,morph
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
WHERE lemma = 'gas';

SELECT 'good,well-better' AS comment;
SELECT lemma,pos,morph
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
WHERE morph = 'better';

SELECT 'well(a,r)-better' AS comment;
SELECT lemma,pos,morph
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
WHERE lemma = 'well';

SELECT 'be-am,are,been,is,was,were' AS comment;
SELECT lemma,pos,morph
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
WHERE lemma = 'be';

SELECT 'morphs per lemma' AS comment;
SELECT lemma,COUNT(morph) AS N,GROUP_CONCAT(morph) AS morphs,GROUP_CONCAT(pos)
FROM morphology
GROUP BY lemma HAVING N > 1
LIMIT 20;

SELECT 'lemma per morph' AS comment;
SELECT morph,COUNT(lemma) AS N,GROUP_CONCAT(lemma) AS lemmas,GROUP_CONCAT(pos)
FROM morphology
GROUP BY morph HAVING N > 1
LIMIT 20;

SELECT 'lemma - 3 (or more) morphs' AS comment;
SELECT lemma,COUNT(wordid) AS N,GROUP_CONCAT(morph),GROUP_CONCAT(pos)
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
GROUP BY wordid
HAVING N > 2
ORDER BY N DESC;

SELECT 'morph - 2 (or more) lemmas' AS comment;
SELECT morph,COUNT(morphid) AS N ,GROUP_CONCAT(lemma),GROUP_CONCAT(pos)
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
GROUP BY morphid
HAVING N > 1
ORDER BY N DESC;

SELECT 'morph matching several pos' AS comment;
SELECT lemma,morph,GROUP_CONCAT(pos),COUNT(*) AS N
FROM morphmaps
INNER JOIN words USING (wordid)
INNER JOIN morphs USING (morphid)
GROUP BY wordid,morphid
HAVING N > 1
ORDER BY N DESC;

