<?php
  /* Example usage of the Amazon Product Advertising API */
  include("AmazonProductAPI.class.php");
  $amazon = new AmazonProductAPI();
  
  error_reporting(0);
  $DEBUG = true;
  $query = (!empty($_REQUEST['q'])) ? $_REQUEST['q'] : 'X-Men Origins';
  $category = (!empty($_REQUEST['category'])) ? $_REQUEST['category'] : AmazonProductAPI::DVD;
  
  try {
    $result = $amazon->searchProducts($query, $category, "TITLE");
  }
  catch(Exception $e) {
    echo $e->getMessage();
  }
  
  $resultList = '<ul>';
  foreach($result->Items->Item as $item) {
    $resultList .= '<li><a href="http://www.amazon.com/gp/product/'.$item->ASIN.'/?ref='.$amazon->getAssociateTag().'&tag='.$amazon->getAssociateTag().'" title="'.$item->ItemAttributes->Title.'" target="_blank">';
    if($item->MediumImage->URL) { $resultList .= '<img src="'.$item->MediumImage->URL.'" /></a>'; } else { $resultList .= '<img src="http://farm2.static.flickr.com/1192/534117370_7eec8198e8.jpg" alt="Image Coming Soon..." width="120" /></a>'; }
    if($item->SalesRank) { $resultList .= '<br/>Rank: '.$item->SalesRank; }
    $resultList .= '</li>';
  }
  $resultList .= '</ul>';
?>
<html>
<head>
  <title>Amazon - Product Advertising API</title>
  <style type="text/css">
    select { width:105px }    
    option { background:goldenrod; font-weight:bold }    
    #results ul { list-style-type:none }
    #results li { float:left }
    .clear { clear:both }
  </style>
  <script type="text/javascript">
    window.onload = (function() {
      var categoryDropdown = document.getElementById('category');
      for(var i = 0; i < categoryDropdown.length; i++) {
        if (categoryDropdown.options[i].text === '<?php echo $category; ?>') {
          alert('matched: ' + categoryDropdown.options[i].text);
          categoryDropdown.options[i].setAttribute('selected','selected');
        }
      }
    });
  </script>
</head>
<body>
  <form>
    <select id="category" name="category">
      <option>All</option>
      <option>Apparel</option>
      <option>Appliances</option>
      <option>ArtsAndCrafts</option>
      <option>Automotive</option>
      <option>Baby</option>
      <option>Beauty</option>
      <option>Blended</option>
      <option>Books</option>
      <option>Classical</option>
      <option>DigitalMusic</option>
      <option selected="selected">DVD</option>
      <option>Electronics</option>
      <option>ForeignBooks</option>
      <option>Garden</option>
      <option>GourmetFood</option>
      <option>Grocery</option>
      <option>HealthPersonalCare</option>
      <option>Hobbies</option>
      <option>HomeGarden</option>
      <option>HomeImprovement</option>
      <option>Industrial</option>
      <option>Jewelry</option>
      <option>KindleStore</option>
      <option>Kitchen</option>
      <option>Lighting</option>
      <option>Magazines</option>
      <option>Marketplace</option>
      <option>Miscellaneous</option>
      <option>MobileApps</option>
      <option>MP3Downloads</option>
      <option>Music</option>
      <option>MusicalInstruments</option>
      <option>MusicTracks</option>
      <option>OfficeProducts</option>
      <option>OutdoorLiving</option>
      <option>Outlet</option>
      <option>PCHardware</option>
      <option>PetSupplies</option>
      <option>Photo</option>
      <option>Shoes</option>
      <option>Software</option>
      <option>SoftwareVideoGames</option>
      <option>SportingGoods</option>
      <option>Tools</option>
      <option>Toys</option>
      <option>UnboxVideo</option>
      <option>VHS</option>
      <option>Video</option>
      <option>VideoGames</option>
      <option>Watches</option>
      <option>Wireless</option>
      <option>WirelessAccessories</option>
    </select>
    <input type="text" id="q" name="q" value="<?php echo $query; ?>" />
  </form>  
  <div id="results">
    <?php echo $resultList; ?>
  </div>
  <div class="clear"> &nbsp; </div>
  <hr />
  <?php if($DEBUG) { echo "<pre>"; print_r($result); echo "</pre>"; } ?>
</body>
</html>