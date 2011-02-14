/////////////////////////////////////////////////////////////////////////////////////
// FreightFinal1Map class - Abstract class to perform operations onto the canvas for the specific map.
/////////////////////////////////////////////////////////////////////////////////////
var FreightFinal1Map = GameMap.extend({
  init: function(){
		this._super();
		
		this.negY = true;
	  this.minX = -4300;
	  this.maxX = 4246;
	  this.minY = -2417;
	  this.maxY = 2295;
	  this.imgWidth = 750;
	  this.imgHeight = 413;
	  this.mapImageLocation = '/maps/cp_freight_final1/map.jpg';
	  this.capturePoints = [
	    //first value should be the log's name for the point, the second value should be the value that shows up in game
		  new CapturePointDrawable("#Well_cap_center", "Central Control Point", new Coordinate(375,207), "", this.capturePointRadius) //midpt, neutral
		  ,new CapturePointDrawable("#Well_cap_blue_rocket", "BLU Base", new Coordinate(73,206), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("#Well_cap_blue_two", "BLU Warehouse", new Coordinate(159,336), "blue", this.capturePointRadius)
		  ,new CapturePointDrawable("#Well_cap_red_rocket", "RED Base", new Coordinate(678,206), "red", this.capturePointRadius)
		  ,new CapturePointDrawable("#Well_cap_red_two", "RED Warehouse", new Coordinate(583,77), "red", this.capturePointRadius)
	  ];
	}
});
gameMapObj = new FreightFinal1Map(); //set the global map object to use our new FreightFinal1Map object.
