define(["require","exports","jquery"],function(a,b){"use strict";var c=function(){function a(a,b){this.data=a,this.$element=b}return a.prototype.initialize=function(){this.data.padding_top=parseInt(this.$element.css("paddingTop")),this.data.padding_bottom=parseInt(this.$element.css("paddingBottom"))},a.prototype.onResize=function(a,b){var c;(c=b.model.getControl("default","padding_top"))&&c.setValue(this.data.padding_top),(c=b.model.getControl("default","padding_bottom"))&&c.setValue(this.data.padding_bottom)},a.prototype.setData=function(a,b){0<=["padding_top","padding_bottom"].indexOf(a)&&(this.data[a]=b)},a.prototype.apply=function(a){this.$element.css({paddingTop:a.padding_top?a.padding_top+"px":null,paddingBottom:a.padding_bottom?a.padding_bottom+"px":null})},a}();b.CommonAdapter=c});