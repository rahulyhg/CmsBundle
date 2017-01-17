var __extends=this&&this.__extends||function(a,b){function c(){this.constructor=a}for(var d in b)b.hasOwnProperty(d)&&(a[d]=b[d]);a.prototype=null===b?Object.create(b):(c.prototype=b.prototype,new c)};define(["require","exports","jquery","backbone","underscore","bootstrap","select2","./dispatcher"],function(a,b,c,d,e,f,g,h){"use strict";c.fn.select2.defaults.set("theme","bootstrap");var i=function(){function a(){}return a.addEditorParameterToUrl=function(a){var b=document.createElement("a");b.href=a;for(var c,d={},e=b.search.replace("?","").split("&"),f=e.length,g=0;g<f;g++)e[g]&&(c=e[g].split("="),d[c[0]]=c[1]);if(!d.hasOwnProperty("cms-editor-enable")){d["cms-editor-enable"]=1,e=[];for(var h in d)d.hasOwnProperty(h)&&e.push(h+"="+d[h]);b.search="?"+e.join("&")}return b.href},a}();b.Util=i;var j={name:null,title:null,disabled:!1},k=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.defaults=function(){return b.buildDefaults()},b.prototype.validate=function(a,b){if(a=a||this.attributes,0==String(a.name).length)throw"Control.name is mandatory";if(0==String(a.title).length)throw"Control.event is mandatory"},b.prototype.enable=function(){return this.set("disabled",!1),this},b.prototype.disable=function(){return this.set("disabled",!0),this},b.prototype.getName=function(){return this.get("name")},b.prototype.getValue=function(){return this.get("value")},b.prototype.setValue=function(a,b){void 0===b&&(b=!1),this.set("value",a)},b}(d.Model);k.buildDefaults=function(a){return e.extend(j,{key:function(a){for(var b="";b.length<a&&a>0;){var c=Math.random();b+=c<.1?Math.floor(100*c):String.fromCharCode(Math.floor(26*c)+(c>.5?97:65))}return b}(8)},a)};var l=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b}(d.View),m=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.createView=function(){return 0<this.get("choices").length?new o({model:this}):new n({model:this})},b.prototype.defaults=function(){return k.buildDefaults({size:"sm",theme:"default",icon:null,active:!1,spinning:!1,rotate:!1,confirm:null,event:null,choices:[],data:{}})},b.prototype.validate=function(b,c){if(a.prototype.validate.call(this,b,c),b=b||this.attributes,0==String(b.event).length)throw"Button.event is mandatory"},b.prototype.activate=function(){return this.set("active",!0),this},b.prototype.deactivate=function(){return this.set("active",!1),this},b.prototype.startSpinning=function(){return this.set("spinning",!0),this},b.prototype.stopSpinning=function(){return this.set("spinning",!1),this},b}(k);b.Button=m;var n=function(a){function b(b){var c;return b.tagName="span",b.attributes={"class":"input-group-btn"},c=a.call(this,b)||this,c.template=e.template('\n        <button type="button" class="btn btn-<%= theme %> btn-<%= size %>" title="<%= title %>">\n          <span class="cei cei-<%= icon %>"></span>\n        </button>\n        '),c.listenTo(c.model,"change",c.render),c}return __extends(b,a),b.prototype.events=function(){return{click:"onClick"}},b.prototype.onClick=function(a){var b=this;a.preventDefault();var c=function(){h["default"].trigger(b.model.get("event"),b.model,a)},d=this.model.get("confirm");d&&0<d.length?confirm(d)&&c():c()},b.prototype.render=function(){return this.$el.html(this.template(this.model.attributes)),this.$("button").prop("disabled",this.model.get("disabled")).toggleClass("active",this.model.get("active")).toggleClass("rotate",this.model.get("rotate")).find("span").toggleClass("cei-spin",this.model.get("spinning")),h["default"].trigger("ui.control.render",this),this},b}(l);b.ButtonView=n;var o=function(a){function b(b){var c;return b.tagName="span",b.attributes={"class":"input-group-btn"},c=a.call(this,b)||this,c.template=e.template('\n        <button type="button" class="btn btn-<%= theme %> btn-<%= size %> dropdown-toggle"\n                title="<%= title %>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\n          <span class="cei cei-<%= icon %>"></span> \n        </button>\n        <ul class="dropdown-menu"></ul>\n        '),c.listenTo(c.model,"change",c.render),c}return __extends(b,a),b.prototype.events=function(){return{"click li a":"onClick"}},b.prototype.onClick=function(a){var b=this;a.preventDefault();var d=c(a.target).closest("a"),f=e.findWhere(this.model.get("choices"),{name:d.data("choice")});if(!f)throw"Choice not found";var g=function(){h["default"].trigger(b.model.get("event"),b.model,f)},i=f.confirm;i&&0<i.length?confirm(i)&&g():g()},b.prototype.render=function(){this.$el.html(this.template(this.model.attributes)),this.$("button").prop("disabled",this.model.get("disabled")).toggleClass("active",this.model.get("active")).toggleClass("rotate",this.model.get("rotate")).find("span").toggleClass("cei-spin",this.model.get("spinning"));var a=this.$("ul");return this.model.get("choices").forEach(function(b){var d=c("<a></a>").attr("href","javascript:void(0)").data("choice",b.name).text(b.title).appendTo(a);c("<li></li>").append(d).appendTo(a)}),h["default"].trigger("ui.control.render",this),this},b}(l);b.ButtonDropdownView=o;var p=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.defaults=function(){return k.buildDefaults({value:1,min:1,max:12})},b.prototype.initialize=function(a,b){},b.prototype.createView=function(){return new q({model:this})},b.prototype.validate=function(b,c){if(a.prototype.validate.call(this,b,c),b=b||this.attributes,0==String(b.event).length)throw"Slider.event is mandatory"},b.prototype.setValue=function(a,b){void 0===b&&(b=!1),this.set("value",a),b&&h["default"].trigger(this.get("event"),this)},b}(k);b.Slider=p;var q=function(a){function b(b){var c;return b.tagName="span",b.attributes={"class":"input-group-slider"},c=a.call(this,b)||this,c.template=e.template('\n            <label for="<%= key %>-input"><%= title %></label>\n            <input id="<%= key %>-input" name="<%= name %>" value="<%= value %>" \n                   type="number" min="<%= min %>" max="<%= max %>">\n            <div>\n              <div id="<%= key %>-slider" class="slider"></div>\n            </div>\n        '),c.listenTo(c.model,"change",c.onModelChange),c}return __extends(b,a),b.prototype.events=function(){return{"change input":"onInputChange"}},b.prototype.onModelChange=function(){this.$(".slider").slider({value:this.model.getValue()})},b.prototype.onInputChange=function(a){a.preventDefault(),this.model.setValue(this.$("input").val(),!0)},b.prototype.render=function(){this.$el.html(this.template(this.model.attributes));var a=this.$("input");return this.$(".slider").slider({value:this.model.get("value"),min:this.model.get("min"),max:this.model.get("max"),step:1,slide:function(b,c){a.val(c.value)},stop:function(){a.trigger("change")}}),h["default"].trigger("ui.control.render",this),this},b}(l);b.SliderView=q;var r=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.defaults=function(){return k.buildDefaults({width:null,value:null,choices:[]})},b.prototype.initialize=function(a,b){a.choices.length&&this.setChoices(a.choices)},b.prototype.createView=function(){return new s({model:this})},b.prototype.validate=function(b,c){if(a.prototype.validate.call(this,b,c),b=b||this.attributes,0==String(b.event).length)throw"Button.event is mandatory"},b.prototype.setChoices=function(a){if(this.set("choices",a),a.length){var b=e.findWhere(a,{active:!0});b?this.setValue(b.value):(b=e.findWhere(a,{value:this.get("value")}),b?b.active=!0:(a[0].active=!0,this.setValue(a[0].value)))}return this},b.prototype.getActiveChoice=function(){return e.findWhere(this.get("choices"),{active:!0})},b.prototype.setValue=function(a,b){if(void 0===b&&(b=!1),a!=this.getValue()){var c;if(this.get("choices").forEach(function(b){b.value==a?(b.active=!0,c=b):b.active=!1}),!c)throw'Value "'+a+'" not found in select choices.';this.set("value",a),b&&h["default"].trigger(this.get("event"),this)}},b.prototype.select=function(a){this.setValue(a)},b}(k);b.Select=r;var s=function(a){function b(b){var c;return b.tagName="span",b.attributes={"class":"input-group-select"},c=a.call(this,b)||this,c.template=e.template('<select class="form-control" name="<%= name %>" title="<%= title %>"></select>'),c.listenTo(c.model,"change",c.render),c}return __extends(b,a),b.prototype.events=function(){return{"change select":"onSelectChange"}},b.prototype.onSelectChange=function(a){a.preventDefault(),this.model.setValue(this.$("select").val(),!0)},b.prototype.render=function(){this.$el.html(this.template(this.model.attributes));var a=this.$("select").empty().prop("disabled",this.model.get("disabled")),b=this.model.get("width");return 0<b&&a.removeAttr("style").css({width:b}),this.model.get("choices").forEach(function(b){c("<option></option>").attr("value",b.value).prop("selected",b.active).prop("disabled",b.disabled).text(b.title).appendTo(a)}),h["default"].trigger("ui.control.render",this),this},b}(l);b.SelectView=s;var t=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.defaults=function(){return{name:null,controls:new d.Collection}},b.prototype.addControl=function(a){return this.get("controls").add(a),this},b.prototype.getControl=function(a){return this.get("controls").findWhere({name:a})},b}(d.Model);b.ControlGroup=t;var u=function(a){function b(b){var c;return b.tagName="div",b.attributes={"class":"input-group"},c=a.call(this,b)||this,c.subViews=[],c.listenTo(c.model,"add remove",c.render),c}return __extends(b,a),b.prototype.clear=function(){this.subViews.forEach(function(a){return a.remove()})},b.prototype.render=function(){var a=this;return this.clear(),this.model.get("controls").each(function(b){var c=b.createView();a.$el.append(c.render().$el),a.subViews.push(c)}),this},b.prototype.remove=function(){return this.clear(),a.prototype.remove.call(this),this},b}(d.View);b.ControlGroupView=u;var v=function(a){function b(){return a.apply(this,arguments)||this}return __extends(b,a),b.prototype.defaults=function(){return{id:null,name:null,classes:["vertical"],origin:{top:0,left:0},groups:new d.Collection}},b.prototype.getName=function(){return this.get("name")},b.prototype.hasGroup=function(a){return-1<this.get("groups").findIndex(function(b){return b.get("name")===a})},b.prototype.addGroup=function(a){return this.get("groups").add(a),this},b.prototype.getGroup=function(a){return this.get("groups").findWhere({name:a})},b.prototype.addControl=function(a,b){return this.hasGroup(a)||this.addGroup(new t({name:a})),this.getGroup(a).addControl(b),this},b.prototype.getControl=function(a,b){return this.hasGroup(a)?this.getGroup(a).getControl(b):null},b}(d.Model);b.Toolbar=v;var w=function(a){function b(b){var c;return b.tagName="div",b.attributes={"class":"editor-toolbar "+b.model.get("classes").join(" ")},0<String(b.model.get("id")).length&&e.extend(b.attributes,{id:b.model.get("id")}),c=a.call(this,b)||this,c.subViews=[],c}return __extends(b,a),b.prototype.clear=function(){this.subViews.forEach(function(a){return a.remove()})},b.prototype.position=function(a){var b={};a.left>window.innerWidth/2?(this.$el.addClass("right").removeClass("left"),b.right=window.innerWidth-a.left):(this.$el.addClass("left").removeClass("right"),b.left=a.left),a.top>window.innerHeight/2?(this.$el.addClass("bottom").removeClass("top"),b.bottom=window.innerHeight-a.top):(this.$el.addClass("top").removeClass("bottom"),b.top=a.top),this.$el.removeAttr("style").css(b)},b.prototype.applyOriginOffset=function(a){return this.position({top:a.top+this.model.get("origin").top,left:a.left+this.model.get("origin").left}),this},b.prototype.render=function(){var a=this;return this.clear(),this.model.get("groups").each(function(b){var c=new u({model:b});a.$el.append(c.render().$el),a.subViews.push(c)}),this.position(this.model.get("origin")),this.postRender()},b.prototype.postRender=function(){return this.$(".dropdown-toggle").dropdown(),this.$("select").select2({width:"resolve"}),this},b.prototype.remove=function(){return this.clear(),a.prototype.remove.call(this),this},b}(d.View);b.ToolbarView=w});