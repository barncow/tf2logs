/////////////////////////////////////////////////////////////////////////////////////
// GoldrushMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var GoldrushMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.mirrorX = true;
	  this.minX = -8573;
	  this.maxX = -2184;
	  this.minY = -2687;
	  this.maxY = 3244;
	  this.imgWidth = 688;
	  this.imgHeight = 707;
	  this.mapImageLocation = '/maps/pl_goldrush/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Goldrush_cap_1_A", "First Cap, Stage One", new Coordinate(7,276), "red", this.capturePointRadius) 
		  ,new CapturePointDrawable("#Goldrush_cap_1_B", "Second Cap, Stage One", new Coordinate(162,66), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Goldrush_cap_2_A", "First Cap, Stage Two", new Coordinate(568,233), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Goldrush_cap_2_B", "Second Cap, Stage Two", new Coordinate(566,481), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Goldrush_cap_3_A", "First Cap, Stage Three", new Coordinate(241,536), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Goldrush_cap_3_B", "Second Cap, Stage Three", new Coordinate(442,444), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Goldrush_cap_3_C", "Atomic Pit, Final Cap", new Coordinate(245,292), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new GoldrushMap(); //set the global map object to use our new GoldrushMap object.
