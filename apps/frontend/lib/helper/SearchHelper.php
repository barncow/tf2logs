<?php

/**
This function takes in a url, and if a page param is found, its
value is replaced with pagenum. If it is not found, it is added.
*/
function replacePageParam($url, $pageNum) {
  $matches;
  $url = preg_replace("/page=[\d*?]/", "page=".$pageNum, $url, -1, $matches);
  if($matches === 0) {
    return $url."&page=".$pageNum;
  } else {
    return $url;
  }
}

?>
