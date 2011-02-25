<?php 
$sf_response->setTitle('Edit a Log - TF2Logs.com');
use_helper('PageElements');
use_helper('Log');
use_javascript('jquery-ui-1.8.9.custom.min.js');
use_javascript('autocompletehelper.js');

$url = url_for('@log_update?id='.$log->getId());
$loglink = link_to('View this Log', '@log_by_id?id='.$log->getId());
$cp = link_to('Back to Control Panel', '@controlpanel');
$s = <<<EOD
Update this log's information below. The name is required, but map name is optional. However, if you want to allow your log file to display events on screen through the Log Viewer, you should specify a map.<br/>
$loglink $cp
<form action="$url" method="post" enctype="multipart/form-data">
  <table>
    {$form->renderGlobalErrors()}
    {$form->renderHiddenFields()}
    <tr>
      <th class="txtright">{$form['name']->renderLabel()}<span class="error">*</span> {$form['name']->renderError()}</th>
      <td class="txtleft">
        {$form['name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all', 'size' => 50))}
      </td>
    </tr>
    <tr>
      <th class="txtright">{$form['map_name']->renderLabel()} {$form['map_name']->renderError()}</th>
      <td class="txtleft">
        {$form['map_name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="center">
        <input type="submit" value="Update" class="ui-state-default"/>
      </td>
    </tr>
  </table>
</form>
EOD;
echo '<div id="infoBoxContainer">';
echo outputInfoBox("updateForm", "Update Log", $s);
echo '</div><br class="hardSeparator"/>';
?>
<script type="text/javascript">
ACSource.source = <?php echo outputAsJSArray($mapNames); ?>;
$(function(){
  $("#log_map_name").autocomplete(ACSource);
});
</script>
