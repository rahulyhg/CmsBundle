define(["require","exports","es6-promise"],function(a,b,c){"use strict";c.polyfill();var d=c.Promise,e=function(){function a(a,b){this.window=b,this.$element=a,this.updated=!1}return a.setup=function(){return new d(function(a,b){a()})},a.tearDown=function(){return new d(function(a,b){a()})},a.prototype.setUpdated=function(a){this.updated=a},a.prototype.isUpdated=function(){return this.updated},a.prototype.edit=function(){this.destroyed=!1},a.prototype.save=function(){var a=this;return new d(function(b,c){if(a.isUpdated())throw"Plugin has updates.";b()})},a.prototype.destroy=function(){var a=this;return this.save().then(function(){a.destroyed=!0})},a.prototype.preventDocumentSelection=function(a){return!1},a}();b.BasePlugin=e});