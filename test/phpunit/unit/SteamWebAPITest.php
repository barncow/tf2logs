<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class unit_SteamWebAPITest extends sfPHPUnitBaseTestCase {

  //note - this does a lookup to an outside system. Therefore, this test could become false if data changes.
  public function testGetAvatarUrls() {
    $swapi = new SteamWebAPI();
    $a = $swapi->getAvatarUrlsFromSteamids(array("76561197993228277", "76561197973956286"));
    $aresult = array("76561197973956286" => "http://media.steampowered.com/steamcommunity/public/images/avatars/99/99680cfa3c8e98bba925a92556d8f15fc084df27.jpg");
    $this->assertTrue(in_array($aresult, $a), "check that a result was found");
  }
  
  public function testDownloadAvatar() {
    $swapi = new SteamWebAPI();
    $fullpath = sfConfig::get('sf_web_dir')."/avatars/1.jpg";
    if(file_exists($fullpath)){
        unlink($fullpath);
    }
    $swapi->downloadAvatar("http://media.steampowered.com/steamcommunity/public/images/avatars/84/8424071dd599cc63a9ca2f217cf70b7c943855f1.jpg", 1);
    $this->assertTrue(file_exists($fullpath), "check that file was downloaded");
    $this->assertTrue(filesize($fullpath) > 0, "jpg has bytes");
  }
  
  public function testGetName() {
    $swapi = new SteamWebAPI();
    $this->assertEquals("Barncow", $swapi->getPlayerName("76561197993228277"), "check that can get current name");
  }
  
  public function testChangeJSONIntsToStrings() {
  $swapi = new SteamWebAPI();
  $rawjson = <<<JSON
{
	"response": {
		"players": {
			"player": [
				{
					"steamid": 76561197960435530,
					"communityvisibilitystate": 3,
					"profilestate": 1,
					"personaname": "Robin",
					"lastlogoff": 1294278830,
					"profileurl": "http:\/\/steamcommunity.com\/id\/robinwalker\/",
					"avatar": "http:\/\/media.steampowered.com\/steamcommunity\/public\/images\/avatars\/f1\/f1dd60a188883caf82d0cbfccfe6aba0af1732d4.jpg",
					"avatarmedium": "http:\/\/media.steampowered.com\/steamcommunity\/public\/images\/avatars\/f1\/f1dd60a188883caf82d0cbfccfe6aba0af1732d4_medium.jpg",
					"avatarfull": "http:\/\/media.steampowered.com\/steamcommunity\/public\/images\/avatars\/f1\/f1dd60a188883caf82d0cbfccfe6aba0af1732d4_full.jpg",
					"personastate": 0,
					"realname": "Robin Walker",
					"primaryclanid": 103582791429521412,
					"timecreated": 1063407589,
					"loccountrycode": "US",
					"locstatecode": "WA",
					"loccityid": 3961
				}
			]
		}
	}
}
JSON;

  $cleanjson =  <<<JSON
{
	"response": {
		"players": {
			"player": [
				{
					"steamid": "76561197960435530",
					"communityvisibilitystate": "3",
					"profilestate": "1",
					"personaname": "Robin",
					"lastlogoff": "1294278830",
					"profileurl": "http:\/\/steamcommunity.com\/id\/robinwalker\/",
					"avatar": "http:\/\/media.steampowered.com\/steamcommunity\/public\/images\/avatars\/f1\/f1dd60a188883caf82d0cbfccfe6aba0af1732d4.jpg",
					"avatarmedium": "http:\/\/media.steampowered.com\/steamcommunity\/public\/images\/avatars\/f1\/f1dd60a188883caf82d0cbfccfe6aba0af1732d4_medium.jpg",
					"avatarfull": "http:\/\/media.steampowered.com\/steamcommunity\/public\/images\/avatars\/f1\/f1dd60a188883caf82d0cbfccfe6aba0af1732d4_full.jpg",
					"personastate": "0",
					"realname": "Robin Walker",
					"primaryclanid": "103582791429521412",
					"timecreated": "1063407589",
					"loccountrycode": "US",
					"locstatecode": "WA",
					"loccityid": "3961"
				}
			]
		}
	}
}
JSON;

  $this->assertEquals($cleanjson, $swapi->changeJSONIntsToStrings($rawjson), "check that bigints in json are converted to strings");
  }
}
