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

function replaceDatePickerButtons() {
  $(".ui-datepicker-trigger").html("") //clear default button text
    .button({
      icons: {
        primary: "ui-icon-calendar",
        text: false,
        label: ''
      }
    }) //create button
    .removeClass("ui-button-text-icon-primary").addClass("ui-button-icon-only"); //adjust styling
}
