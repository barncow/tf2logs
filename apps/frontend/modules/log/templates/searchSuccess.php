<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>
<?php use_helper('Search') ?>

<div id="searchForm" class="infoBox">
  <div class="ui-widget ui-widget-header ui-corner-top header">Player Search</div>
  <div class="content">
    Search for a log via the criteria below. All fields are optional. Entering no criteria below will return a list of all the logs that were uploaded.
    <form action="<?php echo url_for('log/search') ?>" method="get" id="searchForm">
      <input type="hidden" name="page" id="pageValue" value="1"/>
      <table>
        <?php echo $form ?>
      </table>
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
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div>
<?php endif; ?>

<script type="text/javascript">
$(function (){ 
  $("#searchButton").button({
    icons: {
      primary: "ui-icon-search"
    }
  }); 
});
</script>
