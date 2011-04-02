<?php 
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
$sf_response->setTitle('Add a Server - TF2Logs.com');
use_helper('PageElements');
?>

<?php 
$url = url_for('servers/add');
$s = <<<EOD
You can add a server using the form below.<br/>
*details on what is needed - server config access, rcon access*<br/>
*not required, you can always upload logs manually*<br/>

<br/>
<form action="$url" method="post" enctype="multipart/form-data">
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
      <td colspan="2">This is the URL to get to your server logs.</td>
    </tr>
    <tr>
      <th class="txtright">{$form['slug']->renderLabel()}</th>
      <td class="txtleft">
        {$form['slug']->renderError()}
        {$form['slug']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <th class="txtright">{$form['ip']->renderLabel()}:{$form['port']->renderLabel()}</th>
      <td class="txtleft">
        {$form['ip']->renderError()}
        {$form['port']->renderError()}
        {$form['ip']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))} <strong>:</strong> {$form['port']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
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
echo outputInfoBox("newServerForm", "Add a Server", $s);
echo '</div><br class="hardSeparator"/>';
?>
