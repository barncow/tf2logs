var Hook = require('hook.io').Hook
, parent = new Hook({name: "parent"});

parent.on("hook::ready", function() {
	
});

parent.start();