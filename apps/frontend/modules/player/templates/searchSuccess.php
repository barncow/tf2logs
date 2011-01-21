<div id="searchForm">
  <form action="<?php echo url_for('player/search') ?>" method="get">
    <table>
      <tr>
        <td><input type="text" name="param" value="<?php echo $param ?>"/></td>
      </tr>
      <tr>
        <td colspan="2">
          <input type="submit" value="Search"/>
        </td>
      </tr>
    </table>
  </form>
</div>

<?php if(isset($results) && count($results) > 0): ?>
<table id="searchResults">
<caption>Search Results</caption>
<tbody>
<?php foreach($results as $r): ?>
  <?php $s = $r->getStats(); ?>
  <?php $s = $s[0]; ?>
  <tr><td><?php echo link_to($s->getName(), '@player_by_numeric_steamid?id='.$r->getNumericSteamid()) ?></td></tr>
<?php endforeach ?>
</tbody>
</table>
<?php endif ?>
