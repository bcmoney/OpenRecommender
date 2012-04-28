<?php
$config = parse_ini_file("config.ini");

if ($config['DEBUG']) {
  echo "<pre>";
    print_r($config);
  echo "</pre>";
}

$GLOBALS['api_config'] = array('');

foreach ($config['SERVICES'] as $service_parameter_name => $service_parameter_value) {
//AUDIO
  $GLOBALS['Audio']['lastfm'] = array('api_key' => $config['lastfm_api_key'], 'secret_key' => $config['lastfm_secret_key']);
//IMAGE
  $GLOBALS['Image']['flickr'] = array('api_key' => $config['flickr_api_key'], 'secret_key' => $config['flickr_secret_key']);
//TEXT (NLP)
  $GLOBALS['api_config']['AlchemyAPI'] = array('apikey' => $config['alchemy_api_key']);
  $GLOBALS['api_config']['Bing'] = array('AppId' => $config['bing_api_key'], 'Sources' => 'News');
  $GLOBALS['api_config']['DBpediaSpotlight'] = array($config['dbpedia_confidence'], $config['dbpedia_support']);
  $GLOBALS['api_config']['Daylife'] = array('access_key' => $config['daylife_api_key'], 'secret_key' => $config['daylife_secret_key']);
  $GLOBALS['api_config']['Evri'] = array($config['evri_api_key']);
  $GLOBALS['api_config']['OpenAmplify'] = array('apikey' => $config['openamplify_api_key']);
  $GLOBALS['api_config']['OpenCalais'] = array('licenseID' => $config['opencalais_api_key']);
  $GLOBALS['api_config']['SocialActions'] = array('');
  $GLOBALS['api_config']['Truveo'] = array('appid' => $config['truveo_api_key'], 'secret_key' => $config['truveo_secret_key']);
  $GLOBALS['api_config']['Vimeo'] = array('consumer_key' => $config['vimeo_api_key'], 'consumer_secret' => $config['vimeo_secret_key']);
  $GLOBALS['api_config']['Yahoo'] = array('appid' => $config['yahoo_api_key']);
  $GLOBALS['api_config']['YahooBOSS'] = array('appid' => $config['yahooboss_api_key']);
  $GLOBALS['api_config']['YouTube'] = array('category' => 'Nonprofit');
  $GLOBALS['api_config']['Zemanta'] = array('api_key' => $config['zemanta_api_key']);
  $GLOBALS['api_config']['ZemantaSocialActions'] = array('api_key' => $config['zemantasocialactions_api_key']);
//WEATHER

//VIDEO

}




?>