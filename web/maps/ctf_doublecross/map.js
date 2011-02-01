/////////////////////////////////////////////////////////////////////////////////////
// DoublecrossMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var DoublecrossMap = GameMap.extend({
	init: function(){
		this._super();
		
		this.minX = -2573;
		this.maxX = 2540;
		this.minY = -2722;
		this.maxY = 2720;
		this.imgWidth = 874;
		this.imgHeight = 859;
		this.negY = true;
		this.mapImageLocation = '/maps/ctf_doublecross/map.jpg';
		this.capturePoints = [
			//first value should be the log's name for the point, the second value should be the value that shows up in game
			new CapturePointDrawable("blue_cap", "Blue Intel", new Coordinate(656, 51), "blue", this.capturePointRadius)
			,new CapturePointDrawable("red_cap", "Red Intel", new Coordinate(216, 819), "red", this.capturePointRadius)
		];
	}
});
gameMapObj = new DoublecrossMap(); //set the global map object to use our new DoublecrossMap object.
