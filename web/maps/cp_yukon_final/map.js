/////////////////////////////////////////////////////////////////////////////////////
// YukonMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
function YukonMap() {
	this.minX = -3790;
	this.maxX = 3793;
	this.minY = -2496;
	this.maxY = 2512;
	this.imgWidth = 895;
	this.imgHeight = 596;
	this.flipXY = true;
	this.negX = true;
	this.negY = true;
	this.capturePointRadius = 10;
	this.mapImageLocation = './yukon.jpg';
	this.capturePoints = [
		new CapturePoint("#Badlands_cap_cp3", new Coordinate(0,0), "") //midpt, neutral
		,new CapturePoint("#Badlands_cap_blue_cp1", new Coordinate(-2355, 3218), "blue")
		,new CapturePoint("#Badlands_cap_blue_cp2", new Coordinate(237, 2463), "blue")
		,new CapturePoint("#Badlands_cap_red_cp1", new Coordinate(2357, -3213), "red")
		,new CapturePoint("#Badlands_cap_red_cp2", new Coordinate(-231, -2461), "red")
	];
}
YukonMap.prototype = new GameMap();
YukonMap.prototype.constructor = YukonMap;
gameMapObj = new YukonMap(); //set the global map object to use our new YukonMap object.