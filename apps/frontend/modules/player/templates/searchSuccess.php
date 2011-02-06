<?php 
$sf_response->setTitle('Search for Player - TF2Logs.com');
use_helper('Search');
use_javascript('jquery-ui-1.8.9.custom.min.js'); 
use_javascript('search.js'); 
use_helper('PageElements'); 

$url = url_for('player/search');
$paramValue = (isset($param)) ? $param : "";
$s = <<<EOD
    Enter your search below. You can search by Steam ID, Friend ID, or by player name. You can enter a partial name.
    <form action="$url" method="get">
      <input type="hidden" name="page" id="pageValue" value="1"/>
      <table width="100%">
        <tr>
          <td style="text-align: center;"><input type="text" class="ui-widget-content ui-corner-all" name="param" value="$paramValue"/></td>
        </tr>
        <tr>
          <td style="text-align: center;">
            <button type="submit" id="searchButton">Search</button>
            <button id="clearButton">Clear</button>
          </td>
        </tr>
      </table>
    </form>
EOD;
echo outputInfoBox("searchForm", "Player Search", $s);

if(isset($pager) && count($pager->getResults()) > 0) {
  $pagination = "";
  if ($pager->haveToPaginate()) $pagination = outputPaginationLinks($sf_request, $pager);
  $s = <<<EOD
      $pagination
      <table width="100%">
        <tbody>
EOD;
  foreach($pager->getResults() as $r) {
    $stat = $r->getStats();
    $stat = $stat[0];
    $s .= '<tr><td>'.link_to($stat->getName(), '@player_by_numeric_steamid?id='.$r->getNumericSteamid()).'</td></tr>';
  }
  $s .= <<<EOD
        </tbody>
      </table>
      $pagination
EOD;
  echo outputInfoBox("searchResults", "Search Results", $s);
}
?>
