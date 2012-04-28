-- Algorithm for overcoming the Cold-Start problem --
CREATE TABLE `algorithm` (
	`algorith_id` BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	`algorithm_name` VARCHAR(20) 	
);
 
 
---
0. SEARCH	  	     (attract test Users w. widest offering of APIs and data: Video, Audio, Images, Text, Books, Movies, Products)
	CategoricalRandomization  
	
1. NLP 				 (rank TAXONOMY relevances for each ARCHETYPE)
	StringSimilarity
	ContextualAnnotation
	SOUNDEX (Transliteration)
	DIFF
	
2. Mahout		     (assign each ARCHETYPE an ALGORITHM confidence score)
	Classification 
		Bayes
		Neural Network	
	K-Means Clustering 
	Pattern Mining
	Dimension reduction 
	
3. 	LabarrieTheory    (collect USER PROFILE data...) 
	  PredictionAPI   (slot USER to an ARCHETYPE)	
	  
4. CF / LemireRACOFI  (works best when users are already interacting with system, so we use it last... thumbs Up/Down, "SkipIt" .vs. "WatchIt")
	Item-based
		NearestNeighbor
	User-based
		RatingsPopularityEvaluation

5. SKIP SEARCH (Taxonomy~~>Archetype~~>Algorithm~~>User~~>Recommendation)		
	Rule Engine
		Rete (could later try to speed up system w. Rule inference to streamline process)
		Top-Down
---