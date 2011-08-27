var Validate = module.exports = function Validate() {
  if(!(this instanceof Validate)) return new Validate(); //protection for users not using "new"
  this.errors = [];
}

Validate.prototype.addMessage = function(checkFn) {
  if(typeof checkFn !== 'function') throw new Error("checkFn must be a function");

  try {
    checkFn();
  } catch(err) {
    this.errors.push(err.message);
  }

  return this; //for chaining
};