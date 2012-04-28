<h2>NetFlix Movies - Currently at Home</h2>
<? netflix(3, "home", "image", "large", "&nbsp;&nbsp;&nbsp;", "&nbsp;&nbsp;&nbsp;", "PXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"); ?>

<h2>Netflix Movies - Currently in Queue</h2>
<? $movies = netflix_movies(20, "queue");
foreach ($movies as $movie) {
echo $movie->get_cover_image(large);
echo '&nbsp;&nbsp;&nbsp;&nbsp;';
} ?>

<h2>Netflix Movies - My Reviews</h2>
<? $movies = netflix_movies(10, "reviews");
foreach ($movies as $movie) {
echo '<p>';
echo '<strong>',$movie->get_title(),'</strong>';
echo '<br />';
echo $movie->get_description();
echo '</p><br />';
} ?>

<h2>Netflix Movies - My Recommendations</h2>
<? $movies = netflix_movies(5, "recommendations");
foreach ($movies as $movie) {
echo '<p>';
echo '<strong>',$movie->get_title(),'</strong>';
echo '<br />';
echo $movie->get_description();
echo '</p><br />';
} ?>