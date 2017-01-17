var __extends=this&&this.__extends||function(a,b){function c(){this.constructor=a}for(var d in b)b.hasOwnProperty(d)&&(a[d]=b[d]);a.prototype=null===b?Object.create(b):(c.prototype=b.prototype,new c)};define(["require","exports","jquery","backbone","underscore","./dispatcher","./ui"],function(a,b,c,d,e,f,g){"use strict";var h=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.defaults=function(){return{url:null,size:null}},b}(d.Model);b.ViewportModel=h;var i=function(a){function b(b){var c;return b.tagName="div",b.attributes={id:"editor-viewport"},c=a.call(this,b)||this,c.template=e.template('<iframe id="editor-viewport-frame" frameborder="0"></iframe>'),c.onControlsReloadClickHandler=function(a){return c.reload()},c.onControlsViewportClickHandler=function(a){return c.onViewportButtonClick(a)},c.onDocumentManagerNavigateHandler=function(a){return c.load(a)},c}return __extends(b,a),b.prototype.initialize=function(a){e.bindAll(this,"resize","reload"),this.model.bind("change:size",this.resize),c(window).resize(this.resize)},b.prototype.reload=function(){this.iFrame.contentWindow.location.reload()},b.prototype.onViewportButtonClick=function(a){var b=null,c=a.get("data");0<c.width&&0<c.height&&(b=a.get("rotate")?{width:c.height,height:c.width}:{width:c.width,height:c.height}),this.model.set("size",b)},b.prototype.render=function(){return this.$el.html(this.template()),this},b.prototype.load=function(a){this.iFrame.src=g.Util.addEditorParameterToUrl(a)},b.prototype.initIFrame=function(a){var b=this;return this.iFrame=document.getElementById("editor-viewport-frame"),this.iFrame.onload=function(){f["default"].trigger("viewport_iframe.load",b.iFrame.contentWindow||b.iFrame,b.iFrame.contentDocument),b.iFrame.contentWindow.onbeforeunload=function(a){if(f["default"].trigger("viewport_iframe.unload",a),a.returnValue)return a.returnValue}},this.load(a),this},b.prototype.resize=function(){var a=this.model.get("size"),b={top:50,left:0},c={top:50,bottom:0,left:0,right:0},d=window.innerWidth,e=window.innerHeight;a?(e-50>=a.height?(b.top=c.top=e/2-a.height/2+25,c.bottom=e/2-a.height/2-25):(c.top=50,c.height=a.height,c.marginTop=50,c.marginBottom=50,b.top=c.top+c.marginTop),d>=a.width?(b.left=c.left=d/2-a.width/2,c.right=d/2-a.width/2):(c.left=0,c.width=a.width,c.marginLeft=50,c.marginRight=50,b.left=c.left+c.marginLeft)):a={width:d,height:e},this.$el.removeAttr("style").css(c),f["default"].trigger("viewport.resize",{origin:b,size:a})},b}(d.View);b.ViewportView=i});