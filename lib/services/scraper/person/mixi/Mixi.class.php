<?php
require_once 'mixi_graph_api.php';


/*
 * http://developer.mixi.co.jp/connect/mixi_graph_api/services/
 * FLX_CONSUMER_KEY -> set your consumer key
 * FLX_CONSUMER_SECRET -> set your consumer secret
 * FLX_REDIRECT_URL -> set your redirect url
 */

define('CONSUMER_KEY', '');
define('CONSUMER_SECRET', '');
define('SCOPE', 'r_profile w_voice r_voice w_photo r_message r_checkin r_updates');
define('DISPLAY', 'pc');
define('REDIRECT_URL', '');


class Mixi extends MixiGraphAPI{

    function __construct($config)
    {
        parent::__construct($config);
    }

    protected function setAppData($key, $value)
    {
        if(!$key) return;
        $name = $this->createKeyname($key);
        $_SESSION[$name] = $value;
    }

    protected function getAppData($key, $default = false)
    {
        $name = $this->createKeyname($key);
        return ($_SESSION && isset($_SESSION[$name])) ? $_SESSION[$name] : $default;
    }

    protected function clearAppData($key)
    {
        $name = $this->createKeyname($key);
        unset($_SESSION[$name]);
    }

    protected static $supportedKeys =
        array('access_token', 'refresh_token', 'user_id', 'scope');

    public function clearAllAppData() {
        foreach (self::$supportedKeys as $key) {
            $this->clearAppData($key);
        }
    }

    protected function createKeyname($key) {
    return implode('_', array('mixi',
                              $this->consumer_key,
                              $key));
    }

}

?>
