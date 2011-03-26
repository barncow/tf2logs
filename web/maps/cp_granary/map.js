/////////////////////////////////////////////////////////////////////////////////////
// GranaryMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var GranaryMap = GameMap.extend({
  init: function(){
    this._super();
    
	  this.minX = -6255;
	  this.maxX = 6264;
	  this.minY = -2907;
	  this.maxY = -192;
	  this.imgWidth = 1024;
	  this.imgHeight = 228;
	  this.flipXY = true;
	  this.negX = true;
	  this.mapImageLocation = '/maps/cp_granary/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("Granary_cap_cp3", "Central Control Point", new Coordinate(509,114), "", this.capturePointRadius) //midpt, neutral
		  ,new CapturePointDrawable("Granary_cap_blue_cp1", "BLU Base", new Coordinate(75,110), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("Granary_cap_blue_cp2", "BLU Warehouse", new Coordinate(261,115), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("Granary_cap_red_cp1", "RED Base", new Coordinate(940,119), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("Granary_cap_red_cp2", "RED Warehouse", new Coordinate(753,112), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new GranaryMap(); //set the global map object to use our new GranaryMap object.
