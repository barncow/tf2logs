$(function (){ 
  $("#searchButton").button({
    icons: {
      primary: "ui-icon-search"
    }
  }); 
  
  $("#clearButton").button({
    icons: {
      primary: "ui-icon-trash"
    }
  }).click(function(e){
    $("#searchForm").find("input[type=text], select").val("");
    e.preventDefault();
  }); 
});
