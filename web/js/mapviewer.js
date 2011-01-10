//building helper functions into JS itself.
//found at http://www.tek-tips.com/faqs.cfm?fid=6620
String.prototype.trim = function(){return 
(this.replace(/^[\s\xA0]+/, "").replace(/[\s\xA0]+$/, ""))}

String.prototype.startsWith = function(str) 
{return (this.match("^"+str)==str)}

String.prototype.endsWith = function(str) 
{return (this.match(str+"$")==str)}

/////////////////////////////////////////////////////////////////////////////////////
// GameMap class - Abstract class to describe a map to view.
/////////////////////////////////////////////////////////////////////////////////////
var GameMap = Class.extend({
	init: function() {
		this.minX = 0;//min/max in game points
		this.maxX = 0;
		this.minY = 0;
		this.maxY = 0;
		this.imgWidth = 0;
		this.imgHeight = 0;
		this.capturePointRadius = 5;
		this.capturePoints = [];
		this.mapImageLocation = "";
		this.cellWidth = null;
		this.cellHeight = null;
		this.flipXY = false;
		this.negX = false;
		this.negY = false;
	},
	
	copyCapturePoints: function() {
		a = [];
		for(i in this.capturePoints) {
			cp = {};
			tt = {};
			$.extend(true, cp, this.capturePoints[i]);
			$.extend(true, tt, this.capturePoints[i].tooltip);
			cp.tooltip = tt;
			a.push(cp);
		}
		return a;
	},
	
	getCellWidth: function() {
		if(!this.cellWidth) {
			this.cellWidth = Math.abs(this.minX-this.maxX)/this.imgWidth;
		}
		return this.cellWidth;
	},
	
	getCellHeight: function() {
		if(!this.cellHeight) {
			this.cellHeight = Math.abs(this.minY-this.maxY)/this.imgHeight;
		}
		return this.cellHeight;
	},
	
	generateImageCoordinate: function(coordinate) {
		if(this.flipXY) {
			coordinate = new Coordinate(coordinate.y, coordinate.x);
		}
		if(this.negX) {
			coordinate.x = coordinate.x*-1;
		}
		if(this.negY) {
			coordinate.y = coordinate.y*-1;
		}

		xImg = Math.floor(coordinate.x-this.minX)/this.getCellWidth();
		yImg = Math.floor(coordinate.y-this.minY)/this.getCellHeight();
		return new Coordinate(xImg, yImg);
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// Coordinate class - basic data object to hold information about a coordinate.
/////////////////////////////////////////////////////////////////////////////////////
var Coordinate = Class.extend({
	init: function(x,y) {
		this.x = x;
		this.y = y;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// MapDrawer class - class that draws the main canvas for the MapViewer.
/////////////////////////////////////////////////////////////////////////////////////
var MapDrawer = Class.extend({
	init: function(mapViewerCanvas) {
		this.mapViewerCanvas = mapViewerCanvas; //raw canvas object (doc.getEById())
		this.mapViewerContext = null;
		this.fps = 30;
		this.interval = 1000/this.fps;
		this.drawTimeout = null;
		this.drawableStack = new DrawableStack();
		this.mouseLocation = new Coordinate(-1,-1);
		
		if(this.mapViewerCanvas.getContext) {	
			this.mapViewerContext = this.mapViewerCanvas.getContext('2d');
		}
	},
	
	//initiates draw interval
	startDrawing: function() {
		if(this.drawTimeout == null) {
			this.drawFrame(); //calling first since timeout will start after the timeout.
			this.scheduleNextFrame();
		}
	},
	
	//sets the next timeout for executing drawFrame.
	scheduleNextFrame: function() {
		this.drawTimeout = setTimeout(function(){mapViewerObj.mapDrawer.drawFrame()}, this.interval);
	},
	
	//Clears the frame, goes through drawables and draws them.
	drawFrame: function() {
		//don't need to clearRect the whole canvas, since we will just overlay with our bg img.
		
		//determine what is hovered
		hoveredObj = this.drawableStack.markIsHovered(this.mouseLocation);
		
		//draw everything in the stack
		this.drawableStack.drawAll(this.mapViewerCanvas, this.mapViewerContext);
		
		//draw scores
		this.drawScores(this.mapViewerCanvas, this.mapViewerContext, mapViewerObj.redScore, mapViewerObj.blueScore);
		
		//draw tooltip if needed
		if(hoveredObj != null && hoveredObj.tooltip.tooltipEnabled()) {
			this.drawTooltip(hoveredObj.tooltip);
		}
		
		//schedule next frame
		this.scheduleNextFrame();
	},
	
	drawScores: function(canvas, context, redScore, blueScore) {
	  boxwidth = 20;
	  boxheight = 15;
	  boxbotmargin = 10;
	  boxspacing = 10;
	  boxleftmargin = 10;
	  boxY = canvas.height - boxbotmargin - boxheight;
	  boxX = boxleftmargin;
	  leftPadding = 2;
	  topPadding = 2;
	  strokecolor = "#fff";
	  fontStyle = "bold 10px Arial";
	  
	  //red box
	  context.fillStyle = "rgb(200,0,0)";
	  context.strokeStyle = strokecolor;
	  this.roundRect(context, boxX, boxY, boxwidth, boxheight, 5, true, true);
	  context.fillStyle = strokecolor;
	  context.textBaseline = "top";
	  context.font = fontStyle;
		context.fillText(redScore,boxX+leftPadding,boxY);
	  
	  
	  //blue box
	  boxX += boxwidth+boxspacing;
	  context.fillStyle = "rgb(0,0,200)";
	  this.roundRect(context, boxX, boxY, boxwidth, boxheight, 5, true, true);
	  context.fillStyle = strokecolor;
	  context.textBaseline = "top";
	  context.font = fontStyle;
		context.fillText(blueScore,boxX+leftPadding,boxY);
	},
	
	/**
	 * Provided By:
	 * http://js-bits.blogspot.com/2010/07/canvas-rounded-corner-rectangles.html
	 * Draws a rounded rectangle using the current state of the canvas. 
	 * If you omit the last three params, it will draw a rectangle 
	 * outline with a 5 pixel border radius 
	 * @param {CanvasRenderingContext2D} ctx
	 * @param {Number} x The top left x coordinate
	 * @param {Number} y The top left y coordinate 
	 * @param {Number} width The width of the rectangle 
	 * @param {Number} height The height of the rectangle
	 * @param {Number} radius The corner radius. Defaults to 5;
	 * @param {Boolean} fill Whether to fill the rectangle. Defaults to false.
	 * @param {Boolean} stroke Whether to stroke the rectangle. Defaults to true.
	 */
	roundRect: function(ctx, x, y, width, height, radius, fill, stroke) {
	  if (typeof stroke == "undefined" ) {
		stroke = true;
	  }
	  if (typeof radius === "undefined") {
		radius = 5;
	  }
	  ctx.beginPath();
	  ctx.moveTo(x + radius, y);
	  ctx.lineTo(x + width - radius, y);
	  ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
	  ctx.lineTo(x + width, y + height - radius);
	  ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
	  ctx.lineTo(x + radius, y + height);
	  ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
	  ctx.lineTo(x, y + radius);
	  ctx.quadraticCurveTo(x, y, x + radius, y);
	  ctx.closePath();
	  if (stroke) {
		ctx.stroke();
	  }
	  if (fill) {
		ctx.fill();
	  }        
	},
	
	drawTooltip: function(tooltip) {
		lines = tooltip.text.split("\n");
		
		//find the widest line, use it as width of tooltip.
		boxWidth = 0;
		for(i in lines) {
			fontStyle = "";
			t = lines[i];
			if(t.startsWith("%b")) {
				t = t.replace("%b", "");
				fontStyle = "bold ";
			}
			if(t.startsWith("%s")) {
				t = t.replace("%s", "");
				fontStyle += tooltip.smallFontStyle();
			} else {
				fontStyle += tooltip.fontStyle();
			}
			this.mapViewerContext.font = fontStyle;
			w = this.mapViewerContext.measureText(t).width;
			if(w > boxWidth) {
				boxWidth = w;
			}
		}
		
		boxWidth += tooltip.padding*2;
		boxHeight = tooltip.fontSize*lines.length + tooltip.padding*2;
		x = this.mouseLocation.x;
		y = this.mouseLocation.y;
		//adjust position of box, if tip will move past edge of canvas.
		if(x+boxWidth+tooltip.offset.x > this.mapViewerCanvas.width) {
			//box goes past right side of canvas
			//adjust x so that the tooltip ends on the left side of mouse + offset.
			x = x-boxWidth-tooltip.offset.x;
		} else {
			//no collision issues, just keep x as it should be, with offset.
			x += tooltip.offset.x;
		}
		if(y+boxHeight+tooltip.offset.y > this.mapViewerCanvas.height) {
			//box goes below bottom of canvas.
			//adjust y so that the tooltip ends on the top side of mouse + offset.
			y = y-boxHeight-tooltip.offset.y;
		} else {
			//no collision issues, just keep y as it should be, with offset.
			y += tooltip.offset.y;
		}
		
		//draw the background box
		this.mapViewerContext.fillStyle = tooltip.backgroundColor;
		this.mapViewerContext.strokeStyle = tooltip.fontColor;
		this.roundRect(this.mapViewerContext, x, y, boxWidth, boxHeight, 5, true, true);
		
		//draw the text of the tooltip
		this.mapViewerContext.fillStyle = tooltip.fontColor;
		this.mapViewerContext.textBaseline = "top";
		for(i in lines) {
			fontStyle = "";
			t = lines[i];
			if(t.startsWith("%b")) {
				t = t.replace("%b", "");
				fontStyle = "bold ";
			}
			if(t.startsWith("%s")) {
				t = t.replace("%s", "");
				fontStyle += tooltip.smallFontStyle();
			} else {
				fontStyle += tooltip.fontStyle();
			}
			this.mapViewerContext.font = fontStyle;
			
			this.mapViewerContext.fillText(t,x+tooltip.padding,y+(i*tooltip.fontSize)+tooltip.padding);
		}
		
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// MapViewer class - class that handles everything for the mapviewer.
/////////////////////////////////////////////////////////////////////////////////////
var MapViewer = Class.extend({
	init: function(gameMap, playerCollection, logEventCollection, mapViewerCanvas, jqPlaybackControls, jqPlayPause, jqPlaybackProgress, jqChatBox) {
		this.mapDrawer = null;
		this.jqMapViewerCanvas = mapViewerCanvas;
		this.mapViewerCanvas = this.jqMapViewerCanvas[0];
		this.canvasOffset = this.jqMapViewerCanvas.offset();this.eventType = "team_say";
		this.gameMap = gameMap; //GameMap child object
		this.dataTimeout = null;
		this.mapImgDrawable = null;
		this.jqPlayPause = jqPlayPause;
		this.jqPlaybackControls = jqPlaybackControls;
		this.jqPlaybackProgress = jqPlaybackProgress;
		this.jqChatBox = jqChatBox;
		this.playbackState = 0; //0 is paused, 1 is playing.
		this.playbackPosition = 0; //time in seconds
		this.playbackStateBeforeSlide = null; //state of playback when sliding. will return to it when done.
		this.playerCollection = playerCollection;
		this.logEventCollection = logEventCollection;
		this.numberOfSecondsKeepEventOnScreen = 5;
		this.playbackMax = this.logEventCollection.getDuration(); //duration in seconds
		this.areAvatarsLoaded = false;
		this.isMapImgLoaded = false;
		this.blueScore = 0;
		this.redScore = 0;
		
		$('#totalTime').html(this.getSecondsAsString(this.playbackMax));
		
		//set canvas size.
		this.mapViewerCanvas.width = this.gameMap.imgWidth;
		this.mapViewerCanvas.height = this.gameMap.imgHeight;
		this.jqPlaybackControls.width(this.gameMap.imgWidth);
		this.jqPlaybackProgress.width(this.gameMap.imgWidth-this.jqPlayPause.width()-40);
		this.jqChatBox.width(this.gameMap.imgWidth);
		
		//load map overhead image
		this.mapImg = new Image();
		this.mapImg.onload = function() {mapViewerObj.mapImgLoadingComplete();};
		this.mapImg.src = this.gameMap.mapImageLocation;
		
		this.checkAvatarsAreLoaded();
		
		//set canvas mousemove handler. This will determine where the mouse is hovering.
		this.jqMapViewerCanvas.mousemove(function(event){
			mapViewerObj.mapDrawer.mouseLocation = new Coordinate(event.offsetX, event.offsetY);
		});
		
		//set canvas mouseout handler. If a highlighted box was right on the corner,
		//and the mouse moved outside the canvas, the box would remain highlighted.
		//the mouse position in the canvas is reset, so no boxes should be selected.
		this.jqMapViewerCanvas.mouseout(function(){
			mapViewerObj.mapDrawer.mouseLocation = new Coordinate(-1,-1);
		});
		
		//init the Play and Pause Button. Set initial state to pause.
		this.pause();
		this.jqPlayPause.click(function(event){
			if(mapViewerObj.isThisPlaying()) {
				mapViewerObj.pause();
			} else {
				mapViewerObj.play();
			}
		});
		
		//init the playback slider
		this.jqPlaybackProgress.slider({
			min: 0, max: this.playbackMax,
			start: function(event, ui) {
				//user has started to slide.
				mapViewerObj.playbackStateBeforeSlide = mapViewerObj.playBackState;
				mapViewerObj.pause();
			},
			slide: function(event, ui) {
				//user is sliding manually. update playback info
				 mapViewerObj.playbackPosition = ui.value;
				 //only want data for this frame.
				 mapViewerObj.iterateData(false);
			},
			stop: function(event,ui) {
				//user has stopped sliding
				if(mapViewerObj.isPlaying(mapViewerObj.playbackStateBeforeSlide)) {
					//if the user was playing before slide, continue playing
					//otherwise, stay paused.
					mapViewerObj.play();
				}
				mapViewerObj.playbackStateBeforeSlide = null;
			}
		});
	},
	
	resetChatBox: function() {
		this.jqChatBox.children("ul").html("");
	},
	
	appendToChatBox: function(html) {
		this.jqChatBox.children("ul").append($("<li>").html(html));
	},
	
	scrollChatBox: function() {
		last = this.jqChatBox.children("ul").children(":last");
		if(last.length == 1) {
			this.jqChatBox.scrollTop(last.position().top);
		}
	},
	
	updateSliderLabel: function(secs) {
		$("#playbackProgress .ui-slider-handle").text(this.getSecondsAsString(secs));
	},
	
	getSecondsAsString: function(seconds) {
		mins = Math.floor(seconds/60);
		secs = seconds%60;
		szero = "";
		if(secs < 10) szero = "0";
		mzero = "";
		if(mins < 10) mzero = "0";
		return mzero+mins+":"+szero+secs;
	},
	
	pause: function() {
		this.playBackState = 0; //pausing playback
		this.jqPlayPause.html(">");
		this.clearTimeout(); //could have a next iteration scheduled, need to clear it.
	},
	
	play: function () {
		this.playBackState = 1; //playing
		this.jqPlayPause.html("||");
		this.iterateData(true);
	},
	
	isThisPlaying: function() {
		return this.isPlaying(this.playBackState);
	},
	
	isPlaying: function(pbState) {
		return pbState == 1;
	},
	
	scheduleNextDataIteration: function() {
		if(this.isThisPlaying()) {
			this.dataTimeout = setTimeout(function(){mapViewerObj.iterateData(true)}, 1000);
		}
	},
	
	clearTimeout: function() {
		if(this.dataTimeout != null) {
			clearTimeout(this.dataTimeout);
		}
	},

	//action to perform after all images are loaded
	initAfterLoad: function() {
		if(this.areAvatarsLoaded && this.isMapImgLoaded) {
			//create the map image as a drawable for use in iterateData.
			id = new ImageDrawable(this.mapImg, new Coordinate(0,0));
			id.onTopIfHovered = false;
			this.mapImgDrawable = id;
			
			this.mapDrawer = new MapDrawer(this.mapViewerCanvas);
			
			//starting the data iteration
			this.iterateData(false);
			this.mapDrawer.startDrawing();
		}
	},
	
	//This method will be called to tick the frame of the animation.
	//It will update the data in MapDrawer with a new drawableStack
	//depending on what data should be displayed in this frame.
	iterateData: function(iterateSlider) {
		if(iterateSlider && this.playbackPosition < this.playbackMax) {
			++this.playbackPosition;
			this.jqPlaybackProgress.slider("option", "value", this.playbackPosition);
		}
		
		this.updateSliderLabel(this.playbackPosition);
				
		drawableStack = new DrawableStack();
		
		//add map image as background
		drawableStack.add(this.mapImgDrawable);

		//get a clean copy of our capture points to customize and display
		cps = this.gameMap.copyCapturePoints();
		
		//clean the chat log
		this.resetChatBox();
		
		//get all events pertinent for this frame
		events = this.logEventCollection.getDrawablesForDuration(this.playbackPosition-this.numberOfSecondsKeepEventOnScreen, this.playbackPosition
				, this.playerCollection, this.gameMap, cps);
				
		//scroll chat log to bottom
		this.scrollChatBox();
		
		//capture points are modified in the getDrawables method. Add them to the stack.
		drawableStack.addAll(cps);
		
		drawableStack.addAll(events);		
		
		//todo "thread" safe?
		this.mapDrawer.drawableStack = drawableStack;
		
		//schedule next iteration if not at the end
		if(this.playbackPosition >= this.playbackMax) {
			this.pause();
		} else {		
			this.scheduleNextDataIteration();
		}
	},
	
	avatarLoadingComplete: function() {
		//todo move to playerCollection
		//go through PlayerDrawable objects and assign avatar images to each drawable.
		a = $(".avatar");
		for(i in this.playerCollection.players) {
			pd = this.playerCollection.players[i];
			a.each(function(imgIndex, img) {
				//avatar id's start with string "avatar", then id number.
				id = parseInt(img.id.substring(6));
				if(id == pd.playerId) {
					pd.img = img;
					return false;
				}
			});
		}
		this.areAvatarsLoaded = true;
		this.initAfterLoad(); //will check that everything is loaded.
	},
	
	mapImgLoadingComplete: function() {
		this.isMapImgLoaded = true;
		this.initAfterLoad(); //will check that everything is loaded.
	},
	
	checkAvatarsAreLoaded: function() {
		a = $(".avatar");
		if(a == null || a.length == 0) {
			//no avatars on page, just keep going.
			this.avatarLoadingComplete();
		} else {
			loaded = true;
			a.each(function(imgIndex, img) {
				if(!img.complete) {
					loaded = false;
					return false;
				}
			});
			if(loaded) {
				//all are loaded, move on.
				this.avatarLoadingComplete();
			} else {
				//avatars not loaded, wait until they are.
				setTimeout(function(){mapViewerObj.checkAvatarsAreLoaded()}, 250);
			}
		}
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// ToolTip class - class that holds tooltip properties.
/////////////////////////////////////////////////////////////////////////////////////
var ToolTip = Class.extend({
	init: function(text) {
		this.text = "";
		if(text != null && text != "") this.text = text;
		this.offset = new Coordinate(15,15);
		this.padding = 5;
		this.fontSize = 12; //in pixels
		this.smallFontSize = 10; //in pixels
		this.fontFace = "Arial";
		this.fontColor = "rgb(255,255,255)";
		this.backgroundColor = "rgb(0,0,0)";
	},
	
	tooltipEnabled: function() {
		return this.text != null && this.text != "";
	},
	
	fontStyle: function() {
		return this.fontSize+"px "+this.fontFace;
	},
	
	smallFontStyle: function() {
		return this.smallFontSize+"px "+this.fontFace;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// Drawable class - abstract class to handle drawing objects. Subclass name should end
// with Drawable ie. ControlPointDrawable
/////////////////////////////////////////////////////////////////////////////////////
var Drawable = Class.extend({
	init: function() {
		this.coordinate = new Coordinate(0,0);
		this.height = 0;
		this.width = 0;
		this.isHovered = false;
		this.onTopIfHovered = true;
		this.highlightWidth = 2;
		this.highlightColor = "rgb(252,207,25)";
		this.tooltip = new ToolTip();
		
		//determines if the coordinate represents the topleft corner (false)
		//or the center of the drawable.
		this.isCentered = false;
	},
	
	//this is the main drawing method. Override with your own.
	//check this.isHovered to determine if highlighting can be done.
	draw: function(canvas, canvasContext) {
	},
	
	//returns this.coordinate, centered using this.width and height.
	getCenteredCoordinate: function() {
		x = this.coordinate.x-(this.width/2);
		y = this.coordinate.y-(this.height/2);
		return new Coordinate(x,y);
	},
	
	/*this checks if the given Coordinate is within this Drawable.
	the base method will check if the point given is within
	this via this.coordinate, this.height, and this.width.
	If subclassing with a more complex object, use this check
	via this._super(); and see if the coordinate is in the
	area of this object. If true, do a more precise calculation.
	*/
	checkCollision: function(coord) {
		tc = this.coordinate;
		
		if(this.isCentered) {
			tc = this.getCenteredCoordinate();
		}

		return coord.x >= tc.x 
			&& coord.x <= tc.x+this.width
			&& coord.y >= tc.y
			&& coord.y <= tc.y+this.height;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// CapturePointDrawable class - holds information about a capture point, and can be drawn
/////////////////////////////////////////////////////////////////////////////////////
var CapturePointDrawable = Drawable.extend({
	init: function(pointName, desc, coordinate, owningTeam, capturePointRadius) {
		this._super();
		
		this.pointName = pointName;
		this.desc = desc;
		this.resetTooltip();
		this.coordinate = coordinate;
		this.owningTeam = owningTeam;
		this.height = capturePointRadius*2+this.highlightWidth;
		this.width = capturePointRadius*2+this.highlightWidth;
		this.capturePointRadius = capturePointRadius;
		this.isCentered = true;
		this.justCapped = false;
		this.justCappedRadius = this.capturePointRadius+20;
	},
	
	resetTooltip: function() {
		if(this.desc != null && this.desc != "") {
			this.tooltip.text = this.desc;
		} else {
			this.tooltip.text = this.pointName;
		}
	},
	
	draw: function(canvas, canvasContext) {  				
		strokestring = "#FFF";
		if(this.isHovered) {
			//orange color.
			strokestring = this.highlightColor;
		}
		
		colorstring = "100,100,100"; //neutral gray
		if(this.owningTeam == "red") {
			colorstring = "200,0,0";
		} else if(this.owningTeam == "blue") {
			colorstring = "0,0,200";
		}
		
		if(this.justCapped) {
			grd=canvasContext.createRadialGradient(this.coordinate.x,this.coordinate.y,2,this.coordinate.x,this.coordinate.y,this.justCappedRadius);
			grd.addColorStop(0,"rgba("+colorstring+",1)");
			grd.addColorStop(.6,"rgba("+colorstring+",.25)"); 
			grd.addColorStop(1,"rgba("+colorstring+",0)"); 
			this.drawFilledCircle(canvasContext, this.coordinate, this.justCappedRadius, grd);
		}
		
		this.drawFilledCircle(canvasContext, this.coordinate, this.capturePointRadius,  "rgb("+colorstring+")", this.highlightWidth, strokestring);
	},
	
	//convenience method for drawing filled circles. Maybe put in MapDrawer for All to use? or Drawable?
	drawFilledCircle: function(canvasContext, coord, radius, fillStyle, strokeWidth, strokeStyle) {
		canvasContext.fillStyle = fillStyle;  
		canvasContext.beginPath();
		canvasContext.arc(coord.x, coord.y, radius, 0, Math.PI*2, true);
		canvasContext.fill();
		if(strokeWidth != null && strokeStyle != null) {
			canvasContext.lineWidth = strokeWidth;
			canvasContext.strokeStyle = strokeStyle;
			canvasContext.stroke();
		}
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// ImageDrawable class - holds an image and can draw it to the canvas.
// Does not handle preloading - be sure it is loaded before using!
/////////////////////////////////////////////////////////////////////////////////////
var ImageDrawable = Drawable.extend({
	init: function(img, coord) {
		this._super();
		
		this.img = img;
		if(img != null) {
			this.height = img.height;
			this.width = img.width;
		} else {
			this.height = 0;
			this.width = 0;
		}
		this.coordinate = coord;
		this.isCentered = false; //making sure it is false since the bg uses this
	},
	
	draw: function(canvas, canvasContext) {
		if(this.img == null) return;
		c = this.coordinate;
		if(this.isCentered) {
			c = this.getCenteredCoordinate();
		}
		canvasContext.drawImage(this.img,c.x,c.y, this.width, this.height); 
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// PlayerDrawable class - holds player information and draws it to the canvas.
// Does not handle preloading - be sure it is loaded before using!
/////////////////////////////////////////////////////////////////////////////////////
var PlayerDrawable = ImageDrawable.extend({
	init: function(playerId, name, team) {
		size = 16;
		this._super(null, new Coordinate(-size*2,-size*2));
		this.height = size;
		this.width = size;
		this.playerId = playerId;
		this.name = name;
		this.tooltip.text = name;
		this.team = team;
		this.isCentered = true;
	},
	
	setVictimTooltip: function(textFromArrow){
		a = textFromArrow.split("\n");
		//bold third line
		a[2] = "%b"+a[2];
		this.tooltip.text = a.join("\n");
	},
	
	setAttackerTooltip: function(textFromArrow){
		a = textFromArrow.split("\n");
		//bold first line
		a[0] = "%b"+a[0];
		this.tooltip.text = a.join("\n");
	},
	
	setCoordinate: function(coord) {
		this.coordinate = coord;
		this.colCoordinate = new Coordinate(coord.x-this.highlightWidth, coord.y-this.highlightWidth);
	},
	
	draw: function(canvas, canvasContext) {
		colorstring = "#fff";
		if(this.isHovered) {
			colorstring = this.highlightColor;
		} else {
			if(this.team == "blue") {
				colorstring = "rgb(0,0,255)";
			} else if(this.team == "red") {
				colorstring = "rgb(255,0,0)";
			}
		}
		
		//draw stroke
		canvasContext.fillStyle = colorstring;
		c = this.getCenteredCoordinate();
		canvasContext.fillRect (c.x-this.highlightWidth, c.y-this.highlightWidth, this.width+this.highlightWidth*2, this.height+this.highlightWidth*2);
		
		//draw the image
		this._super(canvas, canvasContext); 
	},
	
	//overriding the checkCollision handler to handle the stroke around the image.
	checkCollision: function(coord) {
		tc = this.coordinate;
		
		if(this.isCentered) {
			tc = this.getCenteredCoordinate();
		}

		return coord.x >= tc.x-this.highlightWidth
			&& coord.x <= tc.x+this.width+this.highlightWidth
			&& coord.y >= tc.y-this.highlightWidth
			&& coord.y <= tc.y+this.height+this.highlightWidth;
	},
	
	//gets the tip coordinate for this obj from the given coordinate
	getEdgeCoordinate: function(fromCoord) {
		x = this.coordinate.x;
		y = this.coordinate.y;
		
		if(fromCoord.y > this.coordinate.y) {
			//attacker below vic
			y = y+this.height/2+this.highlightWidth;
		} else if (fromCoord.y < this.coordinate.y) {
			//attacker above vic
			y = y-this.height/2-this.highlightWidth;
		}
		
		if(fromCoord.x > this.coordinate.x) {
			//attacker right of vic
			x = x+this.width/2+this.highlightWidth;
		} else if (fromCoord.x < this.coordinate.x) {
			//attacker left of vic
			x = x-this.width/2-this.highlightWidth;
		}
		
		return new Coordinate(x,y);
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// KillArrowDrawable class - draws a kill arrow from the attacker to the victim position.
// Be sure that the PlayerDrawables given have the correct coordinates supplied.
/////////////////////////////////////////////////////////////////////////////////////
var KillArrowDrawable = Drawable.extend({
	init: function(attackerPlayerDrawable, victimPlayerDrawable) {
		this._super();
		
		this.attacker = attackerPlayerDrawable;
		this.victim = victimPlayerDrawable;
		this.onTopIfHovered = false;
		this.finLength = 5;
		this.calculateDimensions();
		this.tooltip.text = this.attacker.name+"\nkilled\n"+this.victim.name;
	},
	
	//helper method to calculate width, height, x, y
	calculateDimensions: function() {
		ae = this.attacker.getEdgeCoordinate(this.victim.coordinate);
		ve = this.victim.getEdgeCoordinate(this.attacker.coordinate);
		
		if(ae.x < ve.x) {
			this.coordinate.x = ae.x;
		} else {
			this.coordinate.x = ve.x;
		}
		
		if(ae.y < ve.y) {
			this.coordinate.y = ae.y;
		} else {
			this.coordinate.y = ve.y;
		}
		
		mindimension = this.finLength*2;
		
		//need to ensure that the width, height are of a minimum size to better reflect
		//the actual geometry of the arrow. Will also adjust the x,y as necessary.
		this.width = Math.abs(ae.x-ve.x);
		if(this.width < mindimension) {
			this.width = mindimension;
			this.coordinate.x -= mindimension/2;
		}
		
		this.height = Math.abs(ae.y-ve.y);
		if(this.height < mindimension) {
			this.height = mindimension;
			this.coordinate.y -= mindimension/2;
		}
	},
	
	draw: function(canvas, canvasContext) {
		strokeStyle = "#fff";
		if(this.isHovered) {
			strokeStyle = this.highlightColor;
		}
		this.drawArrow(this.attacker.coordinate, this.victim.getEdgeCoordinate(this.attacker.coordinate), strokeStyle, canvasContext);
	},
	
	//creates the path for the arrow, but does not stroke it.
	//this way, either we can draw the path, or we can do ispointinpath for hit detection.
	sketchArrow: function(startCoord, endCoord, canvasContext) {
		canvasContext.save(); //saving original origin
		
		canvasContext.beginPath();
		canvasContext.moveTo(startCoord.x,startCoord.y); 
		canvasContext.lineTo(endCoord.x,endCoord.y);	 

		canvasContext.translate(endCoord.x,endCoord.y);//translate to victim
		
		addend = 0;
		if(endCoord.x < startCoord.x) addend = Math.PI;
		canvasContext.rotate(Math.atan((endCoord.y-startCoord.y)/(endCoord.x-startCoord.x))+addend);
		
		canvasContext.save();//saving first rotation, trans

		canvasContext.rotate(Math.PI/4); //rotate for first fin
		canvasContext.moveTo(0,0);
		canvasContext.lineTo(0,this.finLength);
		canvasContext.restore(); //restore to first rotation, trans
		
		canvasContext.rotate(Math.PI/-4); //rotate back the other way for second fin
		canvasContext.moveTo(0,0);
		canvasContext.lineTo(0,-this.finLength);
		
		canvasContext.restore();//restore to first rotation, trans

		canvasContext.lineJoin = "bevel";

		canvasContext.restore(); //restoring to original origin
	},
	
	drawArrow: function(startCoord, endCoord, strokeStyle, canvasContext) {
		this.sketchArrow(startCoord, endCoord, canvasContext);
		canvasContext.strokeStyle = strokeStyle;
		canvasContext.stroke();
	},
	
	//overriding the checkCollision handler to better detect a hover on the arrow path.
	checkCollision: function(coord) {
		if(!this._super(coord)) return false;
		
		//we are in the boundary of this arrow. Do a per-pix calc.
		/*
		Because isPointInPath seems unreliable, the below method is used.
		The way this works, is we draw an enlarged version of the arrow that we will draw later.
		It is enlarged so that the user has a little bit larger field to actually hover the 
		element instead of the tiny arrow. The arrow is drawn in black. This will be
		the only drawing at the time, because collision detection is done before drawing. So,
		the only black color on the screen, will be the new arrow. So we get the pixel at the
		mouse location. If it is black, the object is being hovered. If not, it is not being 
		hovered. We then clear the frame to prevent false positives for other objects.
		*/
		canvasContext = mapViewerObj.mapDrawer.mapViewerContext;
		lw = canvasContext.lineWidth;
		canvasContext.lineWidth = 10;
		this.sketchArrow(this.attacker.coordinate, this.victim.getEdgeCoordinate(this.attacker.coordinate), canvasContext);
		canvasContext.strokeStyle = "#000";
		canvasContext.stroke();
		myImageData = canvasContext.getImageData(coord.x, coord.y, 1, 1);
		b = myImageData.data[0] == 0;
		canvasContext.clearRect(0,0,mapViewerObj.mapViewerCanvas.width, mapViewerObj.mapViewerCanvas.height);
		canvasContext.lineWidth = lw;

		return b;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// DrawableStack class - ArrayList style class usable for Drawables.
/////////////////////////////////////////////////////////////////////////////////////
var DrawableStack = Class.extend({
	init: function() {
		this.stack = [];
	},
	
	//clears the stack
	clear: function() {
		this.stack = [];
	},
	
	//adds the object given, if it is a Drawable.
	add: function(drawable) {
		if(drawable.draw) {
			this.stack.push(drawable);
		}
	},
	
	//adds the drawables given, either in a simple array form or another DrawableStack.
	addAll: function(drawables) {
		array = [];
		if(drawables instanceof Array) {
			array = drawables;
		} else if(drawables instanceof DrawableStack) {
			array = drawables.stack;
		}
		
		for(i in array) {
			this.add(array[i]);
		}
	},
	
	//calls .draw on all objects in this stack.
	drawAll: function(canvas, canvasContext) {
		dahoveredObj = null;
		for(i in this.stack) {
			d = this.stack[i];
			if(d.isHovered && d.onTopIfHovered) {
				//d is being moused over and wants to be on top
				dahoveredObj = d;
			} else {
				d.draw(canvas, canvasContext);
			}
		}
		
		//draw the highlighted obj on top, if any
		if(dahoveredObj != null) {
			dahoveredObj.draw(canvas, canvasContext);
		}
	},
	
	//finds the element that should be highlighted, and sets its isHovered Drawable attr
	//it will also disable any other isHovered
	//Returns the object that is being hovered if found, otherwise null.
	markIsHovered: function(coord) {
		mahoveredObj = null;
		//pref given to items higher in stack, so start from last.
		for(i = this.stack.length-1; i >= 0; --i) {
			d = this.stack[i];
			if(mahoveredObj == null && d.checkCollision(coord)) {
				//we only want one Drawable highlighted, and we have just found it.
				d.isHovered = true;
				mahoveredObj = d;
				//do not break here, we need to mark the rest as isHovered = false.
			} else {
				d.isHovered = false;
			}
		}
		return mahoveredObj;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// LogEvent class - class that holds information about an event that ocurred.
/////////////////////////////////////////////////////////////////////////////////////
var LogEvent = Class.extend({
	init: function(elapsedSeconds) {
		this.elapsedSeconds = elapsedSeconds;
		this.eventType = "";
		return this;//used for chaining.
	},
	
	//kill
	k: function(attackerPlayerId, attackerCoord, victimPlayerId, victimCoord, assistPlayerId, assistCoord) {
		this.eventType = "kill";
		this.attackerPlayerId = attackerPlayerId;
		this.attackerCoord = attackerCoord;
		this.victimPlayerId = victimPlayerId;
		this.victimCoord = victimCoord;
		this.assistPlayerId = assistPlayerId;
		this.assistCoord = assistCoord;
		return this; // used for chaining
	},
	
	//point capture
	pc: function(cp, team, players) {
		this.eventType = "pointCaptured";
		this.cp = cp;
		this.team = team;
		this.players = players;
		return this; // used for chaining
	},
	
	//say (chat)
	s: function(playerId, text) {
		this.eventType = "say";
		this.playerId = playerId;
		this.text = text;
		return this; // used for chaining
	},
	
	//team_say (chat)
	ts: function(playerId, text) {
		this.eventType = "team_say";
		this.playerId = playerId;
		this.text = text;
		return this; // used for chaining
	},
	
	//round start
	rs: function(redScore, blueScore) {
	  this.eventType = "rndStart";
	  this.redScore = redScore;
	  this.blueScore = blueScore;
	  return this;
	},
	
	getAsDrawables: function(playerCollection, gameMap, capturePoints, isDrawableCurrent) {
		a = [];
		if(this.eventType == "kill") {
			 
			att = playerCollection.getPlayerById(this.attackerPlayerId);
			att.coordinate = gameMap.generateImageCoordinate(this.attackerCoord);
			
			vic = playerCollection.getPlayerById(this.victimPlayerId);
			vic.coordinate = gameMap.generateImageCoordinate(this.victimCoord);
			
			ka = new KillArrowDrawable(att,vic);
			vic.setVictimTooltip(ka.tooltip.text);
			att.setAttackerTooltip(ka.tooltip.text);
			a.push(ka);
			a.push(vic);
			
			if(this.assistPlayerId != null) {
				asst = playerCollection.getPlayerById(this.assistPlayerId);
				asst.coordinate = gameMap.generateImageCoordinate(this.assistCoord);
				asst.tooltip.text = ka.tooltip.text;
				a.push(asst);
			}
			
			a.push(att);
		} else if(this.eventType == "pointCaptured") {
			//find the capture point we want
			for(i in capturePoints) {
				point = capturePoints[i];
				if(point.pointName == this.cp) {
					if(isDrawableCurrent) {
						point.justCapped = true;
					}
					point.owningTeam = this.team;
					point.resetTooltip();
					point.tooltip.text += "\n%sCaptured By:";
					for(p in this.players) {
						point.tooltip.text += "\n%s"+playerCollection.getPlayerById(this.players[p]).name;
					}
				}
			}
		} else if(this.eventType == "say" || this.eventType == "team_say") {
			p = playerCollection.getPlayerById(this.playerId);
			teamtxt = "";
			if(this.eventType == "team_say") teamtxt = "(team)";
			mapViewerObj.appendToChatBox("<span class=\"chatTime\">"+mapViewerObj.getSecondsAsString(this.elapsedSeconds)+"</span> <span class=\"chatUser "+p.team+"\">"+p.name+teamtxt+":</span> "+this.text);
		} else if(this.eventType == "rndStart") {
		  mapViewerObj.blueScore = this.blueScore;
		  mapViewerObj.redScore = this.redScore;
		}
		
		return a;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// LogEventCollection class - ArrayList style class usable for LogEvents.
// todo incorporate rewrites into DrawableStack
/////////////////////////////////////////////////////////////////////////////////////
var LogEventCollection = Class.extend({
	init: function(e) {
		this.clear();
		if(e) {
			this.addAll(e);
		} 
	},
	
	clear: function() {
		this.logEvents = [];
		this.capEvents = [];
		this.chatEvents = [];
		this.roundEvents = [];
	},
	
	//adds the object given, if it is a Drawable.
	add: function(logEvent) {
		if(logEvent instanceof LogEvent) {
			if(logEvent.eventType == "pointCaptured") {
				this.capEvents.push(logEvent);
			} else if(logEvent.eventType == "say" || logEvent.eventType == "team_say") {
				this.chatEvents.push(logEvent);
			} else if(logEvent.eventType == "rndStart") {
			  this.roundEvents.push(logEvent);
			} else {
				this.logEvents.push(logEvent);
			}
		}
	},
	
	//adds the logevents given, either in a simple array form 
	addAll: function(e) {
		if(e instanceof Array) {
			for(i in e) {
				this.add(e[i]);
			}
		}			
	},
	
	getDrawablesForDuration: function(startSeconds, endSeconds, playerCollection, gameMap, capturePoints) {
		drawevents = [];
		for(c in this.logEvents) {
			l = this.logEvents[c];
			if(l.elapsedSeconds >= startSeconds && l.elapsedSeconds <= endSeconds) {
				d = l.getAsDrawables(playerCollection, gameMap, capturePoints, l.elapsedSeconds >= startSeconds && l.elapsedSeconds <= endSeconds);
				for(i in d) {
					drawevents.push(d[i]);
				}
			}
		}
		lastRoundStartElapsedSeconds = 0;
		for(r in this.roundEvents) {
			l = this.roundEvents[r];
			if(l.elapsedSeconds <= endSeconds) {
				l.getAsDrawables(playerCollection, gameMap, capturePoints, l.elapsedSeconds >= startSeconds && l.elapsedSeconds <= endSeconds);
				lastRoundStartElapsedSeconds = l.elapsedSeconds;
			}
		}
		//only want the caps starting from the last round start for the duration.
		for(c in this.capEvents) {
			l = this.capEvents[c];
			if(l.elapsedSeconds >= lastRoundStartElapsedSeconds && l.elapsedSeconds <= endSeconds) {
				l.getAsDrawables(playerCollection, gameMap, capturePoints, l.elapsedSeconds >= startSeconds && l.elapsedSeconds <= endSeconds);
			}
		}
		for(c in this.chatEvents) {
			l = this.chatEvents[c];
			if(l.elapsedSeconds <= endSeconds) {
				l.getAsDrawables(playerCollection, gameMap, capturePoints, l.elapsedSeconds >= startSeconds && l.elapsedSeconds <= endSeconds);
			}
		}
		return drawevents;
	},
	
	getDuration: function() {
		d = 0;
		for(c in this.logEvents) {
			l = this.logEvents[c];
			if(l.elapsedSeconds > d) {
				d = l.elapsedSeconds;
			}
		}
		for(c in this.capEvents) {
			l = this.capEvents[c];
			if(l.elapsedSeconds > d) {
				d = l.elapsedSeconds;
			}
		}
		return d;
	}
});

/////////////////////////////////////////////////////////////////////////////////////
// PlayerCollection class - ArrayList style class usable for PlayerDrawables.
/////////////////////////////////////////////////////////////////////////////////////
var PlayerCollection = Class.extend({
	init: function(p) {
		this.clear();
		if(p) {
			this.addAll(p);
		}
	},
	
	clear: function() {
		this.players = [];
	},
	
	//adds the object given, if it is a Drawable.
	add: function(p) {
		if(p instanceof PlayerDrawable) {
			this.players.push(p);
		}
	},
	
	//adds the logevents given, either in a simple array form or another LogEventCollection.
	addAll: function(p) {
		if(p instanceof Array) {
			for(i in p) {
				this.add(p[i]);
			}
		} else if(p instanceof PlayerCollection) {
			this.players.concat(p.players);
		}		
	},
	
	getPlayerById: function(id) {
		for(i in this.players) {
			player = this.players[i];
			if(parseInt(player.playerId) == parseInt(id)) {
				return player;
			}
		}
		return null;
	}
});
