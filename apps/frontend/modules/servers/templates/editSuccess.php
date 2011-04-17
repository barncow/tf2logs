<?php 
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$title = 'Edit a Server';
if(isset($serverGroup) && $serverGroup->getGroupType() == ServerGroup::GROUP_TYPE_MULTI_SERVER) {
  $title .= ' Group';
}

$sf_response->setTitle($title.' - TF2Logs.com');
use_helper('PageElements');

$server_slug_url = "";
$action = "server_single_save";
if($server_slug) {
  $server_slug_url = '&server_slug='.$server_slug;
  $action = "server_multi_save";
}
$editsave = url_for('@'.$action.'?group_slug='.$group_slug.$server_slug_url);

$s = <<<EOD
You can edit your server using the form below. You are only allowed to edit the server's name and URL.<br/>

<br/>
<form action="$editsave" method="post" enctype="multipart/form-data">
  <table>
    {$form->renderGlobalErrors()}
    {$form->renderHiddenFields()}
    <tr>
      <th class="txtright">{$form['name']->renderLabel()}</th>
      <td class="txtleft">
        {$form['name']->renderError()}
        {$form['name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">This is the URL to get to your server logs. It can only contain letters, numbers, underscores (_), and dashes (-).</td>
    </tr>
    <tr>
      <th class="txtright">{$form['slug']->renderLabel()}</th>
      <td class="txtleft">
        {$form['slug']->renderError()}
        {$form['slug']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="center">
        <input type="submit" value="Save" class="ui-state-default ui-corner-all"/>
      </td>
    </tr>
  </table>
</form>
EOD;

echo '<div id="infoBoxContainer">';
echo outputInfoBox("editServerForm", $title, $s);
echo '</div><br class="hardSeparator"/>';
?>
