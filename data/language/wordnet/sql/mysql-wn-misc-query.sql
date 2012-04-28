SELECT 'ADJ POSITIONS' AS section;

SELECT 'adj having multiple positions' AS comment;
SELECT lemma,COUNT(DISTINCT position) AS N,GROUP_CONCAT(position),GROUP_CONCAT(senseid)
FROM adjectiveswithpositions
GROUP BY wordid HAVING N >1
ORDER BY N DESC;

SELECT 'uses of "out"' AS comment;
SELECT lemma,position,positionname,definition
FROM adjectiveswithpositions
INNER JOIN adjpositiontypes USING (position)
WHERE lemma = 'out';

SELECT 'uses of "big"' AS comment;
SELECT lemma,position,positionname,definition
FROM adjectiveswithpositions
INNER JOIN adjpositiontypes USING (position)
WHERE lemma = 'big';

