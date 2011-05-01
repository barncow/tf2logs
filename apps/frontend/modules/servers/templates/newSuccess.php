<?php 
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
$sf_response->setTitle('Add a Server - TF2Logs.com');
use_helper('PageElements');

$add = url_for('servers/add').'?page='.$page;
$addSingleServer = url_for('@server_new')."?page=single";
$addGroupServer = url_for('@server_new')."?page=newgroup";
$addExistingGroupServer = url_for('@server_new')."?page=existinggroup";
$s = "";
$title = "Add a Server";

if($page == "single") {
$title = "Add a Single Server";
$s = <<<EOD
You can add a server using the form below.<br/>
*details on what is needed - server config access, rcon access*<br/>
*not required, you can always upload logs manually*<br/>

<br/>
<form action="$add" method="post" enctype="multipart/form-data">
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
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <th class="txtright">{$form['region']->renderLabel()}</th>
      <td class="txtleft">
        {$form['region']->renderError()}
        {$form['region']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
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

} else if($page == "newgroup") {

$title = "Add New Group and Server";
$s = <<<EOD
You can add a group and server using the form below.<br/>
*details on what is needed - server config access, rcon access*<br/>
*not required, you can always upload logs manually*<br/>

<br/>
<form action="$add" method="post" enctype="multipart/form-data">
  <table>
    {$form->renderGlobalErrors()}
    {$form->renderHiddenFields()}
    <tr>
      <td colspan="2"><h3>New Group</h3></td>
    </tr>
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
      <td colspan="2">This is the URL to get to view information about your group of servers. It can only contain letters, numbers, underscores (_), and dashes (-).</td>
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
      <td colspan="2"><h3>New Server</h3></td>
    </tr>
    <tr>
      <th class="txtright">{$form['server']['name']->renderLabel()}</th>
      <td class="txtleft">
        {$form['server']['name']->renderError()}
        {$form['server']['name']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2">This is the URL to get to your logs for this server. Note, since this is an address within your group, it only needs to be unique to the servers within the group, not every server and group in TF2Logs.com. It can only contain letters, numbers, underscores (_), and dashes (-).</td>
    </tr>
    <tr>
      <th class="txtright">{$form['server']['slug']->renderLabel()}my_group/</th>
      <td class="txtleft">
        {$form['server']['slug']->renderError()}
        {$form['server']['slug']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <th class="txtright">{$form['server']['ip']->renderLabel()}:{$form['server']['port']->renderLabel()}</th>
      <td class="txtleft">
        {$form['server']['ip']->renderError()}
        {$form['server']['port']->renderError()}
        {$form['server']['ip']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))} <strong>:</strong> {$form['server']['port']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
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

} else if($page == "existinggroup") {

//todo do checking to make sure that there is an existing group.
$title = "Add Server to Existing Group";
$s = <<<EOD
You can add a server to a group using the form below.<br/>
*details on what is needed - server config access, rcon access*<br/>
*not required, you can always upload logs manually*<br/>

<br/>
<form action="$add" method="post" enctype="multipart/form-data">
  <table>
    {$form->renderGlobalErrors()}
    {$form->renderHiddenFields()}
    <tr>
      <td colspan="2"><h3>Pick a Group</h3></td>
    </tr>
    <tr>
      <th class="txtright">{$form['server_group_id']->renderLabel()}</th>
      <td class="txtleft">
        {$form['server_group_id']->renderError()}
        {$form['server_group_id']->render(array('class' => 'ui-widget-content-nobg ui-corner-all'))}
      </td>
    </tr>
    <tr>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2"><h3>New Server</h3></td>
    </tr>
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
      <td colspan="2">This is the URL to get to your logs for this server. Note, since this is an address within your group, it only needs to be unique to the servers within the group, not every server and group in TF2Logs.com. It can only contain letters, numbers, underscores (_), and dashes (-).</td>
    </tr>
    <tr>
      <th class="txtright">{$form['slug']->renderLabel()}my_group/</th>
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

} else {

$title = "Add a Server";

$existing = "";
if($hasGroups) {
  $existing = <<<EOD
<a href="$addExistingGroupServer"><h3>Add a Server to an Existing Group</h3></a>
<p>This is not common. This is for communities that want to add another server to a group.</p>
EOD;
}

$s = <<<EOD
Choose what type of server you want to add.<br/>

<a href="$addSingleServer"><h3>Add a Single Server</h3></a>
<p>This is the most common option. This is for those that want to add just a server, without making it into a group of servers. Later on, you can make this a full group if you want.</p>

<a href="$addGroupServer"><h3>Add a Server and a Group</h3></a>
<p>This is not common. This is for communities that want to group their servers together for stat tracking within that group itself.</p>

$existing
EOD;

}

echo '<div id="infoBoxContainer">';
echo outputInfoBox("newServerForm", $title, $s);
echo '</div><br class="hardSeparator"/>';
?>
