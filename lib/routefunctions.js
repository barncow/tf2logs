/**
  Some common functions for working with routes.
*/

/**
  Creates a generic function to send data and close the request, depending on format type.
  @param req the request object as sent to the route
  @param res the response object as sent to the route
*/
module.exports.getReqHandler = function(req, res) {
  var reqHandler = function(data, redirect) {
      if(data.error) req.flash('error', data.error);
      if(data.info) req.flash('info', data.info);
      if(redirect) res.redirect(redirect);
    };
    if(req.params && req.params.format === 'json') {
      reqHandler = function(data, redirect) {
        res.end(JSON.stringify(data));
      };
    }
  return reqHandler;
};