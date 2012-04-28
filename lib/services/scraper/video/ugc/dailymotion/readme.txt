In this example I retrieve 14 movies (maximum page 1) from dailymotion with the keywords Phil Collins.
The movies are sorted with the option relevance. You can also use HD quality.
Imgeflow will start focussed on the retrieved 7th movie.

INSTALLATION
Unrar and upload to your server
Open index.php and change the settings to your needs
//Default keywords
$videocode = "Phil Collins";
//Sorting - can be relevance (default), hd, visited, rated, commented
$sorteren = "relevance";
//Background for video player and div layer
$background = "#FFFFFF";
//Foreground dailymotion player
$foreground = "#000000";
//Reflection bgcolor
$reflection = "#000000";
//Directory where the images will be stored and cached (needs to be chmod to 775 or 777)
$imagesdir = "./img/";
//Previous and next button
$prevnext = true;
//Empty cache after one hour 60*60, one day 24*60*60 or 7*24*60*60 one week)
$cacheLife = 60*60;

There are two javascript settings you can change
1. Imageflow
/* Sets the numbers of images on each side of the focussed one */
conf_focus = 4;
/* 0 = default, 1 = small to big picture Change this to see the effect */
sizeAlgo = 0; //
/* Glide to a picture on startup. For example 10 is the 11th picture
Use 0 for the starting picture */
glidetopicture = 6;
/* Autostart slideshow */
slideshowauto = true;
/* Show slideshow button */
slideshowbutton = true;
/* Slideshow time setting in seconds */
slideshowtime = 3000;

2. Youtube player
//Settings for the widht and height of the youtube player
videowidht = '520';
videoheight = '411';
//Settings for the positsion of the youtube player
videotop = '-350px';
videoleft = '-80px';
/* Output video, highslide, empty for normal link */
output = "video";

The searchform can be removed easily ( just needed it for the demo )

Also Check out my demo with Imageflow, highslide viewer and sound effects
Or my demo with Youtube, highslide and Jeroen wijering flash player 4.0


TIP. If the results are less then 6 movies, imageflow will loose the focus because in this example I have set the starting image on 6.
Solution is to set the glidetopicture to 0
glidetopicture = 0
If you want to use some javascript to handle the slideshow you can use the following functions
One picture forwards onclick="handle(-1);"
One picture backwards onclick="handle(1);"
Stop slideshow onclick="stopslideshow();"
Play slideshow onclick="slideshow(1);"

Changing the reflections.
For the reflection options check http://reflection.corephp.co.uk

2008 cfconsultancy
