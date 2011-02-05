<?php use_helper('Search') ?>
<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
<div id="searchForm" class="infoBox">
  <div class="ui-widget ui-widget-header ui-corner-top header">Player Search</div>
  <div class="content">
    Enter your search below. You can search by Steam ID, Friend ID, or by player name. You can enter a partial name.
    <form action="<?php echo url_for('player/search') ?>" method="get">
      <input type="hidden" name="page" id="pageValue" value="1"/>
      <table width="100%">
        <tr>
          <td><input type="text" class="ui-widget-content ui-corner-all" name="param" value="<?php echo (isset($param)) ? $param : "" ?>"/></td>
        </tr>
        <tr>
          <td colspan="2">
            <button type="submit" id="searchButton">Search</button>
          </td>
        </tr>
      </table>
    </form>
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>

<?php if(isset($pager) && count($pager->getResults()) > 0): ?>
<div id="searchResults" class="infoBox">
  <div class="ui-widget ui-widget-header ui-corner-top header">Search Results</div>
  <div class="content">
    <?php if ($pager->haveToPaginate()): ?>
      <div class="pagination">
        <a href="<?php echo replacePageParam($sf_request->getUri(),1) ?>">First page</a>
     
        <a href="<?php echo replacePageParam($sf_request->getUri(),$pager->getPreviousPage()) ?>">Previous page</a>
     
        <?php foreach ($pager->getLinks() as $page): ?>
          <?php if ($page == $pager->getPage()): ?>
            <?php echo $page ?>
          <?php else: ?>
            <a href="<?php echo replacePageParam($sf_request->getUri(),$page) ?>"><?php echo $page ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
     
        <a href="<?php echo replacePageParam($sf_request->getUri(),$pager->getNextPage()) ?>">Next page</a>
     
        <a href="<?php echo replacePageParam($sf_request->getUri(),$pager->getLastPage()) ?>">Last page</a>
      </div>
    <?php endif; ?>
    <table width="100%">
      <tbody>
      <?php foreach($pager->getResults() as $r): ?>
        <?php $s = $r->getStats(); ?>
        <?php $s = $s[0]; ?>
        <tr><td><?php echo link_to($s->getName(), '@player_by_numeric_steamid?id='.$r->getNumericSteamid()) ?></td></tr>
      <?php endforeach ?>
      </tbody>
    </table>
    <?php if ($pager->haveToPaginate()): ?>
      <div class="pagination">
        <a href="<?php echo replacePageParam($sf_request->getUri(),1) ?>">First page</a>
     
        <a href="<?php echo replacePageParam($sf_request->getUri(),$pager->getPreviousPage()) ?>">Previous page</a>
     
        <?php foreach ($pager->getLinks() as $page): ?>
          <?php if ($page == $pager->getPage()): ?>
            <?php echo $page ?>
          <?php else: ?>
            <a href="<?php echo replacePageParam($sf_request->getUri(),$page) ?>"><?php echo $page ?></a>
          <?php endif; ?>
        <?php endforeach; ?>
     
        <a href="<?php echo replacePageParam($sf_request->getUri(),$pager->getNextPage()) ?>">Next page</a>
     
        <a href="<?php echo replacePageParam($sf_request->getUri(),$pager->getLastPage()) ?>">Last page</a>
      </div>
    <?php endif; ?>
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>
<?php endif ?>

<script type="text/javascript">
$(function (){ 
  $("#searchButton").button({
    icons: {
      primary: "ui-icon-search"
    }
  }); 
});
</script>
