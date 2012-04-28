<?php

/**
 * 
 * Dependency: MongoDB Driver - http://www.mongodb.org/display/DOCS/PHP+Language+Center
 */
$mongo = new Mongo();
$db = $mongo->openrecommender; //connect to DB
$movies = $db->movie; //connect to a particular Collection (i.e. table)
 
$movie_title = (!empty($_REQUEST['title'])) ? $_REQUEST['title'] : 'Turtles';

 
/* CREATE (inserting) */
$movie = array(
    'title'=>'Teenage Mutant Ninja Turtles',
    'image' =>'http://upload.wikimedia.org/wikipedia/en/1/12/TMNT1987Series.png',
    'link'=>'http://www.tmnt.com'
);
$movie_id = $movies->insert($movie); //succesfull INSERT will output new Movie ID


/* READ (selecting) */
$filter = array(
    'title'=>$movie_title
);
$my_movies = $movies->find($filter);
foreach($my_movies as $movie) {
    echo $movie['title'] . "<br />";
    echo $movie['image'] . "<br />";
    echo $movie['link'] . "<br />";
    echo $movie['description'] . "<br />";
}


/* UPDATE (updating) */
$filter = array('_id'=>$movie_id));
$movie_update = array('$set'=>array('link'=>'http://www.ninjaturtles.com/'));
$movies->update($filter,$movie_update);


/* DELETE (deleting) */
$filter = array('_id'=>$movie_id);
$movies->remove($filter,true);


/******************************************************************/
/* Mongo GridFS - divide a large file among multiple documents */ 
  array("files_id"=>123456789, "n"=>0, "data"=>new MongoBinData("abc"));
  array("files_id"=>123456789, "n"=>1, "data"=>new MongoBinData("def"));
  array("files_id"=>123456789, "n"=>2, "data"=>new MongoBinData("ghi"));
  array("files_id"=>123456789, "n"=>3, "data"=>new MongoBinData("jkl"));
  
?>