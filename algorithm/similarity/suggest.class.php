<?php
	require_once('db.php');
	include('classes/stem.php');
	include('classes/cleaner.php');
	
	if( !empty ( $_POST['search'] ) ):
	
		$string = $_POST['search'];
		$main_url = 'http://www.roscripts.com/';
	
		$stemmer = new PorterStemmer;
		$stemmed_string = $stemmer->stem ( $string );
	
		$clean_string = new jSearchString();
		$stemmed_string = $clean_string->parseString ( $stemmed_string );		
		
		$new_string = '';
		foreach ( array_unique ( split ( " ",$stemmed_string ) ) as $array => $value )
		{
			if(strlen($value) >= 3)
			{
				$new_string .= ''.$value.' ';
			}
		}

		$new_string = substr ( $new_string,0, ( strLen ( $new_string ) -1 ) );
		
		if ( strlen ( $new_string ) > 3 ):
		
			$split_stemmed = split ( " ",$new_string );
		        
		        mysql_select_db($database); 
			$sql = "SELECT DISTINCT COUNT(*) as occurences, title, subtitle FROM articles WHERE (";
		             
			while ( list ( $key,$val ) = each ( $split_stemmed ) )
			{
		              if( $val!='' && strlen ( $val ) > 0 )
		              {
		              	$sql .= "((title LIKE '%".$val."%' OR subtitle LIKE '%".$val."%' OR content LIKE '%".$val."%')) OR";
		              }
			}
			
			$sql=substr ( $sql,0, ( strLen ( $sql )-3 ) );//this will eat the last OR
			$sql .= ") GROUP BY title ORDER BY occurences DESC LIMIT 10";
		
			$query = mysql_query($sql) or die ( mysql_error () );
			$row_sql = mysql_fetch_assoc ( $query );
			$total = mysql_num_rows ( $query );
			 
			if($total>0):
	
			        echo '	<div class="entry">'."\n";
				echo '		<ul>'."\n";
					while ( $row = mysql_fetch_assoc ( $query ) ) 
					{				
						echo '			<li>'."\n";
						echo '				<a href="'.$main_url.'articles/show/'.$row['id'].'">'.$row['title'].''."\n";
						echo '				<em>'.$row['subtitle'].'</em>'."\n";
						echo '				<span>Added on 2007-06-03 by roScripts</span></a>'."\n";
						echo '			</li>'."\n";				
					}
					
				echo '		</ul>'."\n";
				echo '	</div>'."\n";
			endif;
		endif;
	endif;	
?>