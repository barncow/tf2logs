<?php

/**
 * This retrieves information using the Steam Web API.
 *
 * @package    tf2logs
 * @subpackage lib/logParser
 * @author     Brian Barnekow
 * @version    1.0
 */
class SteamWebAPI {  
  public function getAvatarUrlsFromSteamids($numericalSteamids) {
    $a = $this->getPlayerSummaries($numericalSteamids);
    $ret = array();
    foreach($a['response']['players']['player'] as $p) {
      $ret[] = array($p['steamid'] => $p['avatar']);
    }
    return $ret;
  }
  
  public function getPlayerName($numericalSteamid) {
    $a = $this->getPlayerSummaries(array($numericalSteamid));
    if($a && count($a) > 0) {
      return $a['response']['players']['player'][0]['personaname'];
    }
    return "User Not Found";
  }
  
  public function getPlayerSummaries($numericalSteamids) {
    $steamids = implode(",", $numericalSteamids);
    return $this->getDataFromUrl("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0001/?key=".sfConfig::get('app_steam_api_key')."&steamids=".$steamids);
  }
  
  /**
  * PHP does not yet have an option to change JSON integers to strings.
  * This will do it instead.
  */
  public function changeJSONIntsToStrings($json) {
    return preg_replace("/: (\d+)/", ': "${1}"', $json);
  }
  
  //customized from version on http://www.edmondscommerce.co.uk/php/php-save-images-using-curl/
  public function downloadAvatar($url, $playerId) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
    $rawdata=curl_exec($ch);
    curl_close ($ch);
    $fullpath = sfConfig::get('sf_web_dir')."/avatars/".$playerId.".jpg";
    if(file_exists($fullpath)){
        unlink($fullpath);
    }
    $fp = fopen($fullpath,'x');
    fwrite($fp, $rawdata);
    fclose($fp);
  }
  
  /**
  * Will retrieve data from the URL, specifying the format as JSON.
  * This will then return the data as an associative array.
  */
  protected function getDataFromUrl($url) {
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $url."&format=json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $output = curl_exec($ch);
    curl_close($ch);
    $output = $this->changeJSONIntsToStrings($output);
    
    return json_decode($output, true);
  }
}
