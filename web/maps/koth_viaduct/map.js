/////////////////////////////////////////////////////////////////////////////////////
// ViaductMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var ViaductMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.flipXY = true;
	  this.minX = -3230;
	  this.maxX = 3239;
	  this.minY = -2749;
	  this.maxY = -240;
	  this.imgWidth = 750;
	  this.imgHeight = 297;
	  this.mapImageLocation = '/maps/koth_viaduct/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#koth_viaduct_cap", "Control Point", this.generateImageCoordinate(new Coordinate(-1532,0)), "", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new ViaductMap(); //set the global map object to use our new ViaductMap object.
