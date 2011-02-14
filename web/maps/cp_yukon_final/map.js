/////////////////////////////////////////////////////////////////////////////////////
// YukonMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var YukonMap = GameMap.extend({
  init: function(){
		this._super();
		
	  this.minX = -3790;
	  this.maxX = 3793;
	  this.minY = -2496;
	  this.maxY = 2512;
	  this.imgWidth = 750;
	  this.imgHeight = 500;
	  this.flipXY = true;
	  this.mapImageLocation = '/maps/cp_yukon_final/map.jpg';
	  this.scoreBoardCorner = "br"; //red last collides with scoreboard in default position
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("Center Control Point", "Center Control Point", this.generateImageCoordinate(new Coordinate(0,0)), "", this.capturePointRadius) //midpt, neutral
		  ,new CapturePointDrawable("Blue Base", "Blue Base", this.generateImageCoordinate(new Coordinate(-2355, 3218)), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("Blue Bridge", "Blue Bridge", this.generateImageCoordinate(new Coordinate(237, 2463)), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("Red Base", "Red Base", this.generateImageCoordinate(new Coordinate(2357, -3213)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("Red Bridge", "Red Bridge", this.generateImageCoordinate(new Coordinate(-231, -2461)), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new YukonMap(); //set the global map object to use our new YukonMap object.
