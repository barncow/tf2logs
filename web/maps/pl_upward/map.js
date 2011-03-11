/////////////////////////////////////////////////////////////////////////////////////
// UpwardMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var UpwardMap = GameMap.extend({
	init: function(){
		this._super();
		
		this.mirrorY = true;
		this.minX = -2659;
		this.maxX = 2785;
		this.minY = -3025;
		this.maxY = 1692;
		this.imgWidth = 721;
		this.imgHeight = 609;
		this.mapImageLocation = '/maps/pl_upward/map.jpg';
		this.capturePoints = [
			//first value should be the log's name for the point, the second value should be the value that shows up in game
			new CapturePointDrawable("#Badwater_cap_1", "First Capture Point (A)", new Coordinate(495, 416), "red", this.capturePointRadius)
			,new CapturePointDrawable("#Badwater_cap_2", "Second Capture Point (B)", new Coordinate(565, 89), "red", this.capturePointRadius)
			,new CapturePointDrawable("#Badwater_cap_3", "Third Capture Point (C)", new Coordinate(239, 146), "red", this.capturePointRadius)
			,new CapturePointDrawable("#Badwater_cap_4", "Final Capture Point (D)", new Coordinate(431, 286), "red", this.capturePointRadius)
		];
	}
});
gameMapObj = new UpwardMap(); //set the global map object to use our new UpwardMap object.
