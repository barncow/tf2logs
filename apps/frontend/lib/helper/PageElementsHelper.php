<?php
//helpers involved in rendering page elements should be placed here.
use_helper('Search');

function outputInfoBox($id, $title, $content, $styleLikeStatTables = false) {
  $titleHTML = $title;
  $header = ' header';
  if($styleLikeStatTables) {
    $titleHTML = '<div class="statTableCaption">'.$title.'</div>';
    $header = '';
  }
 return <<<EOD
<div id="$id" class="infoBox">
  <div class="ui-widget ui-toolbar ui-widget-header ui-corner-top$header">$titleHTML</div>
  <div class="content">
    $content
  </div>
  <div class="ui-widget-header ui-corner-bottom bottomSpacer"></div>
</div> 
EOD;
}

function outputPaginationLinks($request, $pager, $pageParam = 'page', $pageBookmark = "") {
  if(strlen($pageBookmark) > 0 && strpos("#", $pageBookmark) !== 0) $pageBookmark = '#'.$pageBookmark;
  $uri = $request->getUri();
  $s = '<div class="pagination">';
    $s .= '<a href="'.replacePageParam($uri,1, $pageParam).$pageBookmark.'">First page</a>&nbsp;';
 
    $s .= '<a href="'.replacePageParam($uri,$pager->getPreviousPage(), $pageParam).$pageBookmark.'">Previous page</a>&nbsp;';
 
    foreach ($pager->getLinks() as $page) {
      if($page == $pager->getPage()) {
        $s .= $page.'&nbsp;';
      } else {
        $s .= '<a href="'.replacePageParam($uri,$page, $pageParam).$pageBookmark.'">'.$page.'</a>&nbsp;';
      }
    }
 
    $s .= '<a href="'.replacePageParam($uri,$pager->getNextPage(), $pageParam).$pageBookmark.'">Next page</a>&nbsp;';
    $s .= '<a href="'.replacePageParam($uri,$pager->getLastPage(), $pageParam).$pageBookmark.'">Last page</a>';
  $s .= '</div>';
  return $s;
}
?>
