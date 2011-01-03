/////////////////////////////////////////////////////////////////////////////////////
// BadlandsMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var BadlandsMap = GameMap.extend({
	init: function(){
		this._super();
		
		this.minX = -4839;
		this.maxX = 4839;
		this.minY = -2503;
		this.maxY = 2503;
		this.imgWidth = 700;
		this.imgHeight = 370;
		this.flipXY = true;
		this.mapImageLocation = '/maps/cp_badlands/map.jpg';
		this.capturePoints = [
			//first value should be the log's name for the point, the second value should be the value that shows up in game
			new CapturePointDrawable("#Badlands_cap_cp3", "Middle Point", this.generateImageCoordinate(new Coordinate(0,0)), "", this.capturePointRadius) //midpt, neutral
			,new CapturePointDrawable("#Badlands_cap_blue_cp1", "Blue Last", this.generateImageCoordinate(new Coordinate(-765, -4096)), "blue", this.capturePointRadius)
			,new CapturePointDrawable("#Badlands_cap_blue_cp2", "Blue Spire", this.generateImageCoordinate(new Coordinate(-1839, -1660)), "blue", this.capturePointRadius)
			,new CapturePointDrawable("#Badlands_cap_red_cp1", "Red Last", this.generateImageCoordinate(new Coordinate(768, 4096)), "red", this.capturePointRadius)
			,new CapturePointDrawable("#Badlands_cap_red_cp2", "Red Spire", this.generateImageCoordinate(new Coordinate(1853, 1644)), "red", this.capturePointRadius)
		];
	}
});
gameMapObj = new BadlandsMap(); //set the global map object to use our new BadlandsMap object.
