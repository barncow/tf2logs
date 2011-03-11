/////////////////////////////////////////////////////////////////////////////////////
// GravelPitMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var GravelPitMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.mirrorY = true;
	  this.minX = -4659;
	  this.maxX = 1280;
	  this.minY = -316;
	  this.maxY = 5605;
	  this.imgWidth = 700;
	  this.imgHeight = 696;
	  this.mapImageLocation = '/maps/cp_gravelpit/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Gravelpit_cap_A", "The Radio Tower (A)", this.generateImageCoordinate(new Coordinate(126,554)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Gravelpit_cap_B", "The Radar (B)", this.generateImageCoordinate(new Coordinate(-2240, 4095)), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Gravelpit_cap_C", "The Laser Gun (C)", this.generateImageCoordinate(new Coordinate(-2747, 1310)), "red", this.capturePointRadius)
	  ];	
	}
});
gameMapObj = new GravelPitMap(); //set the global map object to use our new GravelPitMap object.
