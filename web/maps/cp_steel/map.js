/////////////////////////////////////////////////////////////////////////////////////
// SteelMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var SteelMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.mirrorY = true;
	  this.minX = -2155;
	  this.maxX = 2771;
	  this.minY = -3352;
	  this.maxY = 2542;
	  this.imgWidth = 830;
	  this.imgHeight = 992;
	  this.mapImageLocation = '/maps/cp_steel/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("Cap A, The front door dock", "Cap A, The front door dock", new Coordinate(172,666), "red", this.capturePointRadius) 
		  ,new CapturePointDrawable("Cap B, the Red Shed spawn", "Cap B, the Red Shed spawn", new Coordinate(691,584), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("Cap C, The bridge controls", "Cap C, The bridge controls", new Coordinate(606,150), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("Cap D, Red spawn door to E", "Cap D, Red spawn door to E", new Coordinate(175,241), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("Cap E, the main point", "Cap E, the main point", new Coordinate(366,404), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new SteelMap(); //set the global map object to use our new SteelMap object.
