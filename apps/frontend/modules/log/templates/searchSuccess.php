<?php $sf_response->setTitle('Search for Log - TF2Logs.com'); ?>
<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
<?php use_javascript('search.js'); ?>
<?php use_helper('Search') ?>
<?php use_helper('PageElements') ?>

<?php 
$url = url_for('log/search');
$s = <<<EOD
Search for a log via the criteria below. All fields are optional. Entering no criteria below will return a list of all the logs that were uploaded.
<form action="$url" method="get">
  <input type="hidden" name="page" id="pageValue" value="1"/>
  <table>
    <tbody>
      {$form->renderGlobalErrors()}
      <tr>
        <th>{$form['name']->renderLabel()}</th>
        <td>
          {$form['name']->renderError()}
          {$form['name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
        </td>
      </tr>
      <tr>
        <th>{$form['map_name']->renderLabel()}</th>
        <td>
          {$form['map_name']->renderError()}
          {$form['map_name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
        </td>
      </tr>
      <tr>
        <th>{$form['from_date']->renderLabel()}</th>
        <td>
          {$form['from_date']->renderError()}
          {$form['from_date']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
        </td>
      </tr>
      <tr>
        <th>{$form['to_date']->renderLabel()}</th>
        <td>
          {$form['to_date']->renderError()}
          {$form['to_date']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
        </td>
      </tr>
    </tbody>
  </table>
    <tr>
      <td colspan="2">
        <button type="submit" id="searchButton">Search</button>
        <button id="clearButton">Clear</button>
      </td>
    </tr>
  </table>
</form>
EOD;
echo outputInfoBox("searchForm", "Log Search", $s);
?>

<?php 
if(isset($pager) && count($pager->getResults()) > 0) {
  $pagination = "";
  if ($pager->haveToPaginate()) $pagination = outputPaginationLinks($sf_request, $pager);
  $s = <<<EOD
      $pagination
      <table width="100%">
        <thead>
          <tr><th>Log Name</th><th>Uploaded</th></tr>
        </thead>
        <tbody>
EOD;
        foreach($pager->getResults() as $r) {
          $s .= '<tr><td>'.link_to($r['name'], '@log_by_id?id='.$r['id']).'</td><td>'.$r["created_at"].'</td></tr>';
        }
  $s .= <<<EOD
        </tbody>
      </table>
      $pagination
EOD;
  echo outputInfoBox("searchResults", "Search Results", $s);
}
?>
