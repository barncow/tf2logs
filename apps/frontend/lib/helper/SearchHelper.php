<?php

/**
This function takes in a url, and if a page param is found, its
value is replaced with pagenum. If it is not found, it is added.
*/
function replacePageParam($url, $pageNum, $pageParam = "page") {
  $matches;
  $url = preg_replace("/".$pageParam."=\d+/", $pageParam."=".$pageNum, $url, -1, $matches);
  if($matches === 0) {
    $sep = "&";
    if(strpos($url, "?") === false) $sep = "?";
    return $url.$sep.$pageParam."=".$pageNum;
  } else {
    return $url;
  }
}

?>
