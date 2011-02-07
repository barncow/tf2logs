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
			new CapturePointDrawable("#Badlands_cap_cp3", "Middle Point", new Coordinate(350,185), "", this.capturePointRadius) //midpt, neutral
			,new CapturePointDrawable("#Badlands_cap_blue_cp1", "Blue Last", new Coordinate(54, 130), "blue", this.capturePointRadius)
			,new CapturePointDrawable("#Badlands_cap_blue_cp2", "Blue Spire", new Coordinate(231, 55), "blue", this.capturePointRadius)
			,new CapturePointDrawable("#Badlands_cap_red_cp1", "Red Last", new Coordinate(643, 240), "red", this.capturePointRadius)
			,new CapturePointDrawable("#Badlands_cap_red_cp2", "Red Spire", new Coordinate(468, 317), "red", this.capturePointRadius)
		];
	}
});
gameMapObj = new BadlandsMap(); //set the global map object to use our new BadlandsMap object.
