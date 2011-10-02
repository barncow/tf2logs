/**
  Some common functions for working with routes.
*/

/**
  Creates a generic function to send data and close the request, depending on format type.
  @param req the request object as sent to the route
  @param res the response object as sent to the route
*/
module.exports.getReqHandler = function(req, res) {
  var reqHandler = function(data, doNext) {
      if(data.error) req.flash('error', data.error);
      if(data.info) req.flash('info', data.info);
      if(doNext.redirect) res.redirect(doNext.redirect);
      else if(doNext.render) res.render(doNext.render, data);
    };
    if(req.params && req.params.format === 'json') {
      reqHandler = function(data) {
        res.setHeader("Content-Type", "application/json");
        res.end(JSON.stringify(data));
      };
    }
  return reqHandler;
};