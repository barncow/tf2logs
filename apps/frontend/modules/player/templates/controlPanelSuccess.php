<?php 
$sf_response->setTitle('My TF2Logs - TF2Logs.com'); 
use_helper("PageElements");
use_helper('Search');

$homepagelink = link_to('Upload one now!', '@homepage');
$s = <<<EOD
<div class="subInfo">
Use this area to modify information about the logs you have uploaded. Haven't uploaded a log? <strong>$homepagelink</strong>
</div>
EOD;
echo '<div class="infoBoxContainer">';
echo outputInfoBox("playerNameCP", $player->name, $s);
echo '</div><br class="hardSeparator"/>';

$pagination = "";
if ($slPager->haveToPaginate()) $pagination = '<div class="ui-table-content statTable">'.outputPaginationLinks($sf_request, $slPager, 'slPage', 'playerLogSubmitted').'</div>';
$data = "";
foreach($slPager->getResults() as $sl) {
  $link = link_to('Edit', '@log_edit?id='.$sl['id']);
  $date = getHumanReadableDate($sl['created_at']);
  $data .= <<<EOD
      <tr>
        <td class="ui-table-content">$link</td>
        <td class="ui-table-content">{$sl['name']}</td>
        <td class="ui-table-content">{$sl['map_name']}</td>
        <td class="ui-table-content txtnowrap">{$date}</td>
      </tr>
EOD;
}
    
$s = <<<EOD
$pagination
<table class="statTable" width="100%">
  <thead>
    <tr>
      <th colspan="2" class="ui-state-default">Log Name</th>
      <th class="ui-state-default">Map Name</th>
      <th class="ui-state-default">Date Submitted</th>
    </tr>
  </thead>
  <tbody>
    $data
    
  </tbody>
</table>
$pagination
EOD;
echo '<div class="infoBoxContainer">';
echo outputInfoBox("submittedCP", 'Logs Submitted - '.$numSubmittedLogs, $s);
echo '</div><br class="hardSeparator"/>';

?>
