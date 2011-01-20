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
		  new CapturePointDrawable("Granary_cap_cp3", "Central Control Point", this.generateImageCoordinate(new Coordinate(-1532,0)), "", this.capturePointRadius) //midpt, neutral
		  ,new CapturePointDrawable("Granary_cap_blue_cp1", "BLU Base", this.generateImageCoordinate(new Coordinate(-1601, 5184)), "blue", this.capturePointRadius) //still slightly off.
		  ,new CapturePointDrawable("Granary_cap_blue_cp2", "BLU Warehouse", this.generateImageCoordinate(new Coordinate(-1537, 2963)), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("Granary_cap_red_cp1", "RED Base", this.generateImageCoordinate(new Coordinate(-1472, -5185)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("Granary_cap_red_cp2", "RED Warehouse", this.generateImageCoordinate(new Coordinate(-1535, -2949)), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new GranaryMap(); //set the global map object to use our new GranaryMap object.
