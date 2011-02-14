/////////////////////////////////////////////////////////////////////////////////////
// ColdfrontMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var ColdfrontMap = GameMap.extend({
  init: function(){
		this._super();
		
		this.flipXY = true;
	  this.minX = -6184;
	  this.maxX = 6192;
	  this.minY = -1524;
	  this.maxY = 1521;
	  this.imgWidth = 750;
	  this.imgHeight = 189;
	  this.mapImageLocation = '/maps/cp_coldfront/map.jpg';
	  this.scoreBoardCorner = "br";
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Granary_cap_cp3", "Central Control Point", new Coordinate(373,93), "", this.capturePointRadius) //midpt, neutral
		  ,new CapturePointDrawable("#Granary_cap_blue_cp1", "BLU Base", new Coordinate(44,124), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("#Granary_cap_blue_cp2", "BLU Warehouse", new Coordinate(207,105), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("#Granary_cap_red_cp1", "RED Base", new Coordinate(704,61), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Granary_cap_red_cp2", "RED Warehouse", new Coordinate(544,83), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new ColdfrontMap(); //set the global map object to use our new ColdfrontMap object.
