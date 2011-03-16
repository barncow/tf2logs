/////////////////////////////////////////////////////////////////////////////////////
// BadwaterMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var BadwaterMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.mirrorY = true;
	  this.minX = -2429;
	  this.maxX = 2986;
	  this.minY = -3309;
	  this.maxY = 2864;
	  this.imgWidth = 678;
	  this.imgHeight = 778;
	  this.mapImageLocation = '/maps/pl_badwater/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Badwater_cap_1", "First Capture point", this.generateImageCoordinate(new Coordinate(40,-1233)), "red", this.capturePointRadius) 
		  ,new CapturePointDrawable("#Badwater_cap_2", "Second Capture point", this.generateImageCoordinate(new Coordinate(-1220,-907)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Badwater_cap_3", "Third Capture point", this.generateImageCoordinate(new Coordinate(-1667,1964)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Badwater_cap_4", "Final Capture point", this.generateImageCoordinate(new Coordinate(8,863)), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new BadwaterMap(); //set the global map object to use our new BadwaterMap object.
