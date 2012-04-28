<?php  
  include 'WolframAlphaEngine.php';
  
  $appID = '';

  // instantiate an engine object with your app id
  $engine = new WolframAlphaEngine( $appID );

  $query = (!empty($_REQUEST['q'])) ? $_REQUEST['q'] : 'Population of Canada';
  
  // we will construct a basic query to the api with the input 'pi' only the bare minimum will be used
  $response = $engine->getResults($query);
  // getResults will send back a WAResponse object, this object has a parsed version of the wolfram alpha response as well as the raw xml ($response->rawXML)  
?>
<html>
<body>
<form method='POST' action='#'>
Search: <input type="text" name="q" value="<?php echo $query; ?>">&nbsp;&nbsp; <input type="submit" name="Search" value="Search">
</form>
<br><br>
<hr>
<?php
  // we can check if there was an error from the response object  
  if ( $response->isError ) {
?>
  <h1>There was an error in the request</h1>
  </body>
  </html>
<?php
    die();
  }
?>

<h1>Results</h1>
<div>
<?php
  // if there are any assumptions, display them 
  if ( count($response->getAssumptions()) > 0 ) {
?>
    <h2>Assumptions:</h2>
    <ul>
<?php
      // assumptions come as a hash of type as key and array of assumptions as value
      foreach ( $response->getAssumptions() as $type => $assumptions ) {
?>
        <li><?php echo $type; ?>:<br>
          <ol>
<?php
          foreach ( $assumptions as $assumption ) {
?>
            <li><?php echo $assumption->name ." ". $assumption->description;?> to change search to this assumption <a href="simpleRequest.php?q=<?php echo urlencode($assumption->input);?>">click here</a></li>
<?php
          }
?>
          </ol>
        </li>
<?php
      }
?>     
    </ul>
<?php
  }
?>
</div>
<hr/>
<?php
  // if there are any pods, display them
  if ( count($response->getPods()) > 0 ) {
?>
    <h2>Pods</h2>
    <table border=1 width="80%" align="center">
<?php
    foreach ( $response->getPods() as $pod ) {
?>
      <tr>
        <td>
          <h3><?php echo $pod->attributes['title']; ?></h3>
<?php
        // each pod can contain multiple sub pods but must have at least one
        foreach ( $pod->getSubpods() as $subpod ) {
          // if format is an image, the subpod will contain a WAImage object
?>
          <img src="<?php echo $subpod->image->attributes['src']; ?>">
          <hr>
<?php
        }
?>         
        </td>
      </tr>
<?php
    }
?>
    </table>
<?php
  }
?>
</body>
</html>