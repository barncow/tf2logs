/////////////////////////////////////////////////////////////////////////////////////
// GranaryMap class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
function GranaryMap() {
	this.minX = -6255;
	this.maxX = 6264;
	this.minY = -2907;
	this.maxY = -192;
	this.imgWidth = 1024;
	this.imgHeight = 228;
	this.flipXY = true;
	this.negX = true;
	this.capturePointRadius = 10;
	this.mapImageLocation = './granary.jpg';
	this.capturePoints = [
		new CapturePoint("#Badlands_cap_cp3", new Coordinate(-1532,0), "") //midpt, neutral
		,new CapturePoint("#Badlands_cap_blue_cp1", new Coordinate(-1601, 5184), "blue") //still slightly off.
		,new CapturePoint("#Badlands_cap_blue_cp2", new Coordinate(-1537, 2963), "blue")
		,new CapturePoint("#Badlands_cap_red_cp1", new Coordinate(-1472, -5185), "red")
		,new CapturePoint("#Badlands_cap_red_cp2", new Coordinate(-1535, -2949), "red")
	];
}
GranaryMap.prototype = new GameMap();
GranaryMap.prototype.constructor = GranaryMap;
gameMapObj = new GranaryMap(); //set the global map object to use our new GranaryMap object.