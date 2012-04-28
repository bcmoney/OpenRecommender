<?

/*
 * random_row
 *   Select random row from a database, using PHP random function insteaad of slower DBMS randomizer
 *   
 * USAGE:
 *
 * echo '<pre>';
 * print_r(random_row('YOUR_TABLE', 'YOUR_COLUMN'));
 * echo '</pre>';
 */
function random_row($table, $column) {

      $max_sql = "SELECT max(" . $column . ") 
					AS max_id FROM " . $table;
					
      $max_row = mysql_fetch_array(mysql_query($max_sql)); //get upper limit for Random number generator range
	  
      $random_number = mt_rand(1, $max_row['max_id']);
	  
      $random_sql = "SELECT * FROM " . $table . "
                     WHERE " . $column . " >= " . $random_number . " 
                     ORDER BY " . $column . " ASC LIMIT 1";
      $random_row = mysql_fetch_row(mysql_query($random_sql));
      if (!is_array($random_row)) {
          $random_sql = "SELECT * FROM " . $table . "
                         WHERE " . $column . " < " . $random_number . " 
                         ORDER BY " . $column . " DESC LIMIT 1";
          $random_row = mysql_fetch_row(mysql_query($random_sql));
      }
      return $random_row;
  }

/* 
 * Array itself should be randomly pulled from Database: 
 *   SELECT * FROM table WHERE <criteria> ORDER BY RAND() LIMIT 10;
 *
 * This is slow for large datasets, use pre-randomized INDEX:
 *   http://jan.kneschke.de/projects/mysql/order-by-rand/
 * OR
 *   random_row() - function (above) before building query
 */
$text[0]="<a href=link1.html>link 1</a>";
$text[1]="<a href=link2.html>link 2</a>";
$text[2]="<a href=link3.html>link 3</a>";
$text[3]="<a href=link4.html>link 4</a>";
$text[4]="<a href=link5.html>link 5</a>";
$text[5]="<a href=link6.html>link 6</a>";
$text[6]="<a href=link7.html>link 7</a>";
$text[7]="<a href=link8.html>link 8</a>";
$text[8]="<a href=link9.html>link 9</a>";
$text[9]="<a href=link10.html>link 10</a>";
$text[10]="<a href=link11.html>link 11</a>";
$text[11]="<a href=link12.html>link 12</a>";

$n=sizeof($text)-1;

$number = mt_rand (0, $n );
$random1 = $text[$number];
	echo "Option 1: " . $random1 . "<br/>";

shuffle ($text);
	echo  "Option 2: " . (($text[$number] != $random1) ? $text[$number]: $text[0]) . "<br/>";
	
?>