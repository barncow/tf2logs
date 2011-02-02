<?php use_javascript('jquery-1.4.4.min.js'); ?>
<?php use_stylesheet('./mint-choc/jquery-ui-1.8.9.custom.css'); ?>
<?php use_javascript('jquery-ui-1.8.9.custom.min.js'); ?>

<script type="text/javascript">
$(function(){
  $(".pageNav").click(function(e){
    e.preventDefault();
    $("#pageValue").val($(this).attr('ref')).parent().submit();
  });
});
</script>

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
    <a href="#" ref="1" class="pageNav">First page</a>
 
    <a href="#" ref="<?php echo $pager->getPreviousPage() ?>" class="pageNav">Previous page</a>
 
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="#" ref="<?php echo $page ?>" class="pageNav"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
 
    <a href="#" ref="<?php echo $pager->getNextPage() ?>" class="pageNav">Next page</a>
 
    <a href="#" ref="<?php echo $pager->getLastPage() ?>" class="pageNav">Last page</a>
  </div>
<?php endif; ?>

<table id="searchResults">
<caption>Search Results</caption>
<tbody>
<?php foreach($pager->getResults() as $r): ?>
  <tr><td><?php echo link_to($r['name'], '@log_by_id?id='.$r['id']) ?> Uploaded <?php echo $r["created_at"] ?></td></tr>
<?php endforeach ?>
</tbody>
</table>

<?php if ($pager->haveToPaginate()): ?>
  <div class="pagination">
    <a href="#" ref="1" class="pageNav">First page</a>
 
    <a href="#" ref="<?php echo $pager->getPreviousPage() ?>" class="pageNav">Previous page</a>
 
    <?php foreach ($pager->getLinks() as $page): ?>
      <?php if ($page == $pager->getPage()): ?>
        <?php echo $page ?>
      <?php else: ?>
        <a href="#" ref="<?php echo $page ?>" class="pageNav"><?php echo $page ?></a>
      <?php endif; ?>
    <?php endforeach; ?>
 
    <a href="#" ref="<?php echo $pager->getNextPage() ?>" class="pageNav">Next page</a>
 
    <a href="#" ref="<?php echo $pager->getLastPage() ?>" class="pageNav">Last page</a>
  </div>
<?php endif; ?>

<?php elseif(isset($pager) && count($pager->getResults()) == 0): ?>
No logs found.
<?php endif ?>
