/////////////////////////////////////////////////////////////////////////////////////
// GullywashMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
function GullywashMap() {
	this.minX = -3541;
	this.maxX = 4899;
	this.minY = -2842;
	this.maxY = 2265;
	this.imgWidth = 700;
	this.imgHeight = 421;
	this.capturePointRadius = 10;
	this.mapImageLocation = './gullywash.jpg';
	this.capturePoints = [
		new CapturePoint("#Badlands_cap_cp3", new Coordinate(692,-296), "") //midpt, neutral
		,new CapturePoint("#Badlands_cap_blue_cp1", new Coordinate(4458, -1035), "blue")
		,new CapturePoint("#Badlands_cap_blue_cp2", new Coordinate(1720, -1894), "blue")
		,new CapturePoint("#Badlands_cap_red_cp1", new Coordinate(-3081, 450), "red")
		,new CapturePoint("#Badlands_cap_red_cp2", new Coordinate(-356, 1316), "red")
	];
}
GullywashMap.prototype = new GameMap();
GullywashMap.prototype.constructor = GullywashMap;
gameMapObj = new GullywashMap(); //set the global map object to use our new GullywashMap object.