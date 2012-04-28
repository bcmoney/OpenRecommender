<?php

include_once "IMDB.class.php";

$imdb = new IMDB();

/*
$ratings = $imdb->getRatings(); //DEBUG: 
echo"<pre>";print_r($ratings);echo"</pre>";
 */
$i = 0;
echo '<ul>';
foreach ($imdb->getRatings() as $rating) {
  if ($i > 0) {
    $title = $imdb->getRatingsTitle($rating);
    $link = str_replace("/title/","",substr($imdb->getRatingsTitleLink($rating),0,-1));
    $year = $imdb->getRatingsTitleYear($rating);
    $your_rating = $imdb->getRatingsYourRating($rating);
    $user_rating = $imdb->getRatingsAverageRating($rating);
    $votes = $imdb->getRatingsNumberOfVotes($rating);
    $date = $imdb->getRatingsDate($rating);  
    echo '<li><a href="movie.php?m='.$link.'" target="_blank">'.$title.'</a> '.$your_rating.' | '.$user_rating.' ('.$votes.') - '.$date.'</li>'; //HTML output
  //  "INSERT INTO movie (title, image, link, description, year, date, user_rating, average_rating) VALUES ('{$title}', '{$image}', '{$link}', '{$desc}', {$year}, {$date}, {$user_rating}, {$your_rating});" //write to database
  }
  $i++;
}
echo '</ul>';

?>