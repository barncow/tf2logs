/////////////////////////////////////////////////////////////////////////////////////
// SawmillMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var SawmillMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.negY = true;
	  this.minX = -1753;
	  this.maxX = 2743;
	  this.minY = -2497;
	  this.maxY = 2526;
	  this.imgWidth = 750;
	  this.imgHeight = 840;
	  this.mapImageLocation = '/maps/koth_sawmill/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Arena_cap", "Control Point", this.generateImageCoordinate(new Coordinate(508,0)), "", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new SawmillMap(); //set the global map object to use our new SawmillMap object.
