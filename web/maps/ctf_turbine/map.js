/////////////////////////////////////////////////////////////////////////////////////
// TurbineMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var TurbineMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.negY = true;
	  this.minX = -3232;
	  this.maxX = 3225;
	  this.minY = -1657;
	  this.maxY = 1638;
	  this.imgWidth = 750;
	  this.imgHeight = 388;
	  this.mapImageLocation = '/maps/ctf_turbine/map.jpg';
	  this.scoreBoardCorner = "br";
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("blue_cap", "Blue Intel", new Coordinate(17, 369), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("red_cap", "Red Intel", new Coordinate(732,16), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new TurbineMap(); //set the global map object to use our new TurbineMap object.
