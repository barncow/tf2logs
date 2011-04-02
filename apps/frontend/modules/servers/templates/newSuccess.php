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

<form action="$url" method="post" enctype="multipart/form-data">
  <table>
    {$form->renderGlobalErrors()}
    {$form->renderHiddenFields()}
    <tr>
      <th>{$form['name']->renderLabel()}</th>
      <td class="txtleft">
        {$form['name']->renderError()}
        {$form['name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <th>{$form['slug']->renderLabel()}</th>
      <td class="txtleft">
        {$form['slug']->renderError()}
        {$form['slug']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <th>{$form['ip']->renderLabel()}:{$form['port']->renderLabel()}</th>
      <td class="txtleft">
        {$form['ip']->renderError()}
        {$form['port']->renderError()}
        {$form['ip']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}:{$form['port']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2" class="center">
        <input type="submit" value="Save" class="ui-state-default"/>
      </td>
    </tr>
  </table>
</form>
EOD;
echo '<div id="infoBoxContainer">';
echo outputInfoBox("newServerForm", "Add a Server", $s);
echo '</div><br class="hardSeparator"/>';
?>
