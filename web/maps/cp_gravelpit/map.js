/////////////////////////////////////////////////////////////////////////////////////
// GravelPitMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
function GravelPitMap() {
	this.minX = -4659;
	this.maxX = 1280;
	this.minY = -316;
	this.maxY = 5605;
	this.imgWidth = 700;
	this.imgHeight = 696;
	this.capturePointRadius = 10;
	this.mapImageLocation = './gravelpit.jpg';
	this.capturePoints = [
		new CapturePoint("#Badlands_cap_cp3", new Coordinate(126,554), "red")
		,new CapturePoint("#Badlands_cap_blue_cp1", new Coordinate(-2240, 4095), "red")
		,new CapturePoint("#Badlands_cap_blue_cp2", new Coordinate(-2747, 1310), "red")
	];
}
GravelPitMap.prototype = new GameMap();
GravelPitMap.prototype.constructor = GravelPitMap;
gameMapObj = new GravelPitMap(); //set the global map object to use our new GravelPitMap object.