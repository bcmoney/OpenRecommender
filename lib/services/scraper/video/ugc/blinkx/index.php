<?php

//include "../../../Services.class.php"
error_reporting(0);

$q1 = $_REQUEST['q'];
$q = str_replace(" ", "+", $q1);

?>
<html>
<head>
  <title>Blinkx API integration</title>
</head>
<body>
  <div style="text-align:center; width:100%">
    <form action="" method="get">
      <input type="text" name="q" value="<?php echo $q1; ?>" />
      <input type="submit" value="Search" />
    </form>
    <embed src="http://www.blinkx.com/w?g_StageWidth=800&g_StageHeight=600&&g_ApiServer=www.blinkx.com&g_sImgServer=http://cdn-99.blinkx.com/store&g_sApiQuery=%2Fapiphp%2Fstart.php%3Faction%3Dquery%26databasematch%3Dmedia%26totalresults%3Dtrue%26AdultFilter%3Dtrue%26text%3D<?php echo $q; ?>%26start%3D1%26maxresults%3D25%26sortby%3Drelevance%26fieldtext%3DBIAS%7B1194613000%2C1000000%2C0%7D%3Aautn_date%2520AND%2520BIAS%7B1194613000%2C10000000%2C10%7D%3Aautn_date%2520AND%2520NOT%28MATCH%7BPodcast%7D%3ACHANNEL%29%26newsresults%3Dtrue&g_ApiTunnelPath=/f/" width="800" height="600" quality="high" bgcolor="#5892D2" name="newwall" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>
  </div>
</body>
</html>