/////////////////////////////////////////////////////////////////////////////////////
// DustbowlMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var DustbowlMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.mirrorY = true;
	  this.minX = -3527;
	  this.maxX = 3080;
	  this.minY = -2912;
	  this.maxY = 3325;
	  this.imgWidth = 942;
	  this.imgHeight = 898;
	  this.mapImageLocation = '/maps/cp_dustbowl/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Dustbowl_cap_1_A", "First Cap, Stage One", new Coordinate(472,80), "red", this.capturePointRadius) 
		  ,new CapturePointDrawable("#Dustbowl_cap_1_B", "Second Cap, Stage One", new Coordinate(828,136), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Dustbowl_cap_2_A", "First Cap, Stage Two", new Coordinate(831,699), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Dustbowl_cap_2_B", "Second Cap, Stage Two", new Coordinate(279,759), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Dustbowl_cap_3_A", "First Cap, Stage Three", new Coordinate(232,384), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Dustbowl_cap_3_B", "Rocket, Final Cap", new Coordinate(578,375), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new DustbowlMap(); //set the global map object to use our new DustbowlMap object.
