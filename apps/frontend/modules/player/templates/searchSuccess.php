<?php use_helper('Search') ?>
<div id="searchForm">
  <form action="<?php echo url_for('player/search') ?>" method="get">
    <input type="hidden" name="page" id="pageValue" value="1"/>
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

<?php if(isset($pager) && count($pager->getResults()) > 0): ?>
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

<table id="searchResults">
<caption>Search Results</caption>
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
<?php endif ?>
