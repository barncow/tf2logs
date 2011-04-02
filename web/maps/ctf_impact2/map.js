/////////////////////////////////////////////////////////////////////////////////////
// ImpactMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var ImpactMap = GameMap.extend({
	init: function(){
		this._super();
		
		this.flipXY = true;
		this.minX = 3990;
		this.maxX = -3145;
		this.minY = 2456;
		this.maxY = -782;
		this.imgWidth = 1024;
		this.imgHeight = 466;
		
		this.mapImageLocation = '/maps/ctf_impact2/map.jpg';
		this.capturePoints = [
			//first value should be the log's name for the point, the second value should be the value that shows up in game
			new CapturePointDrawable("blue_intel", "Blue Intel", this.generateImageCoordinate(new Coordinate(5,-1658)), "blue", this.capturePointRadius)
			,new CapturePointDrawable("red_intel", "Red Intel", this.generateImageCoordinate(new Coordinate(-4,2499)), "red", this.capturePointRadius)
		];
	}
});
gameMapObj = new ImpactMap(); //set the global map object to use our new ImpactMap object.
