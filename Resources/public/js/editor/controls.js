var __extends=this&&this.__extends||function(a,b){function c(){this.constructor=a}for(var d in b)b.hasOwnProperty(d)&&(a[d]=b[d]);a.prototype=null===b?Object.create(b):(c.prototype=b.prototype,new c)};define(["require","exports","underscore","./dispatcher","./ui"],function(a,b,c,d,e){"use strict";var f=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.initialize=function(b,d){var f=this;if(a.prototype.initialize.call(this,b,d),this.addControl("default",new e.Button({name:"power",title:"Toggle editor on/off",size:"md",theme:"primary",icon:"power",event:"controls.power.click"})),this.addControl("default",new e.Button({name:"reload",title:"Reload the page",size:"md",theme:"default",icon:"reload",event:"controls.reload.click"})),!d.viewports||0==d.viewports.length)throw"Viewport buttons are not configured";if(c.forEach(d.viewports,function(a){f.addControl("viewport",new e.Button({name:a.name,title:a.title+(a.width?" ("+a.width+"x"+a.height+")":""),size:"md",icon:a.icon,event:"controls.viewport.click",rotate:!1,active:a.active,data:{width:a.width,height:a.height}}))}),!d.locales||0==d.locales.length)throw"Locale buttons are not configured";this.addControl("document",new e.Select({name:"locale",title:"Select the locale",event:"controls.locale.change",choices:d.locales})),this.addControl("document",new e.Select({name:"page",title:"Select the page",event:"controls.page.change",choices:[],width:250})),this.addControl("document",new e.Button({name:"edit-page",title:"Edit the page",size:"md",theme:"warning",icon:"edit-page",event:"controls.edit_page.click"})),this.addControl("document",new e.Button({name:"new-page",title:"Create a new page",size:"md",theme:"success",icon:"new-page",event:"controls.new_page.click"}))},b}(e.Toolbar);b.MainToolbar=f;var g=function(a){function b(b){var d=a.call(this,b)||this;return d.template=c.template('\n            <div id="editor-control-main" class="btn-group"></div>\n            <div id="editor-control-viewport" class="btn-group"></div>\n        '),d}return __extends(b,a),b.prototype.initialize=function(a){var b=this;d["default"].on("controls.power.click",function(a){a.set("active",!a.get("active"))}),d["default"].on("controls.viewport.click",function(a){b.model.getGroup("viewport").get("controls").reject(function(b){return b==a}).forEach(function(a){return a.deactivate()}),a.get("active")?"Adjust"!=a.get("name")&&a.set("rotate",!a.get("rotate")):a.set("active",!0)})},b.prototype.position=function(a){},b.prototype.getLocaleSelect=function(){return this.model.getControl("document","locale")},b.prototype.getPageSelect=function(){return this.model.getControl("document","page")},b.prototype.getNewPageButton=function(){return this.model.getControl("document","new-page")},b.prototype.getEditPageButton=function(){return this.model.getControl("document","edit-page")},b}(e.ToolbarView);b.MainToolbarView=g});