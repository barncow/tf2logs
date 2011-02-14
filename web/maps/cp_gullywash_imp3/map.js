/////////////////////////////////////////////////////////////////////////////////////
// GullywashMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var GullywashMap = GameMap.extend({
  init: function(){
		this._super();
		
	  this.minX = -3541;
	  this.maxX = 4899;
	  this.minY = -2842;
	  this.maxY = 2265;
	  this.imgWidth = 700;
	  this.imgHeight = 421;
	  this.mapImageLocation = '/maps/cp_gullywash_imp3/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Well_cap_center", "Central Control Point", this.generateImageCoordinate(new Coordinate(692,-296)), "", this.capturePointRadius) //midpt, neutral
		  ,new CapturePointDrawable("#Well_cap_blue_rocket", "BLU Base", this.generateImageCoordinate(new Coordinate(4458, -1035)), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("#Well_cap_blue_two", "BLU Warehouse", this.generateImageCoordinate(new Coordinate(1720, -1894)), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("#Well_cap_red_rocket", "RED Base", this.generateImageCoordinate(new Coordinate(-3081, 450)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Well_cap_red_two", "RED Warehouse", this.generateImageCoordinate(new Coordinate(-356, 1316)), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new GullywashMap(); //set the global map object to use our new GullywashMap object.
