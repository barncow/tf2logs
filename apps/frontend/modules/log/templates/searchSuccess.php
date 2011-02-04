<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_stylesheet('jquery-ui-1.8.9.custom.css'); ?>
<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
<?php use_helper('Search') ?>

<form action="<?php echo url_for('log/search') ?>" method="get" id="searchForm">
  <input type="hidden" name="page" id="pageValue" value="1"/>
  <table>
    <?php echo $form ?>
  </table>
    <tr>
      <td colspan="2">
        <input type="submit" />
      </td>
    </tr>
  </table>
</form>

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
<thead>
  <tr><th>Log Name</th><th>Uploaded</th></tr>
</thead>
<tbody>
<?php foreach($pager->getResults() as $r): ?>
  <tr><td><?php echo link_to($r['name'], '@log_by_id?id='.$r['id']) ?></td><td><?php echo $r["created_at"] ?></td></tr>
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

<?php elseif(isset($pager) && count($pager->getResults()) == 0): ?>
No logs found.
<?php endif ?>
