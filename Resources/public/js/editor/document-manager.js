define(["require","exports","jquery","underscore","es6-promise","routing","./dispatcher","./ui"],function(a,b,c,d,e,f,g,h){"use strict";e.polyfill();var i=e.Promise,j=function(){function a(){}return a.setContentWindow=function(a){this.contentWindow=a},a.getContentWindow=function(){if(!this.contentWindow)throw"Window is not defined.";return this.contentWindow},a.setContentDocument=function(a){this.$contentDocument=a;var b=a.find("html").data("document-data");if(!b)throw"Undefined document data.\nDid you forget to use the cms_document_data() twig function in your template ?";this.setDocumentData(b)},a.getContentDocument=function(){if(!this.$contentDocument)throw"Document is not defined.";return this.$contentDocument},a.setDocumentData=function(a){this.documentData=a},a.getDocumentData=function(){return this.documentData},a.getDocumentLocale=function(){if(!this.documentData)throw"Content data is not defined.";return this.documentData.locale},a.clear=function(){this.contentWindow=null,this.$contentDocument=null,this.documentData=null},a.findElementById=function(a){return this.$contentDocument.find("#"+a)},a.createElement=function(a,b){if(!b)throw"Undefined parent.";return c("<div></div>").attr("id",a).appendTo(b)},a.findOrCreateElement=function(a,b){var c=this.findElementById(a);return 0==c.length&&(c=this.createElement(a,b)),c},a.setElementAttributes=function(a,b){a.removeAttr("class").attr("class",b.classes).removeAttr("data-cms").data("cms",b.data),b.style&&a.removeAttr("style").attr("style",b.style)},a.sortChildren=function(a){var b=a.children();b.detach().get().sort(function(a,b){var d=c(a).data("cms").position,e=c(b).data("cms").position;return d==e?0:d>e?1:-1}).forEach(function(b){a.append(b)})},a.generateUrl=function(b,c){return f.generate(b,d.extend({},c||{},{_document_locale:a.getDocumentLocale()}))},a.request=function(a){var b=this;a=d.extend({},a,{method:"POST"});var e=c.ajax(a);return e.done(function(a){a.hasOwnProperty("removed")&&a.removed.forEach(function(a){b.$contentDocument.find("#"+a).remove()}),a.hasOwnProperty("content")?k.parse(a.content):a.hasOwnProperty("containers")?l.parse(a.containers):a.hasOwnProperty("rows")?m.parse(a.rows):a.hasOwnProperty("blocks")&&n.parse(a.blocks),g["default"].trigger("base_manager.response_parsed",a.hasOwnProperty("created")?a.created:void 0)}),e.fail(function(){throw"Editor request failed."}),e},a}();b.BaseManager=j;var k=function(){function a(){}return a.parse=function(a){if(!a.hasOwnProperty("attributes"))throw"Unexpected content data";var b=j.findElementById(a.attributes.id);if(0==b.length)throw"Content not found.";j.setElementAttributes(b,a.attributes),a.hasOwnProperty("containers")&&l.parse(a.containers,b),j.sortChildren(b)},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return j.generateUrl(b,d.extend({},c||{},{contentId:e}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),j.request(d)},a}();b.ContentManager=k;var l=function(){function a(){}return a.parse=function(a,b){a.forEach(function(c,d){if(!c.hasOwnProperty("attributes"))throw"Unexpected container data";var e=j.findOrCreateElement(c.attributes.id,b);j.setElementAttributes(e,c.attributes);var f=c.hasOwnProperty("content")?c.content:null;if(f&&0<f.length)e.html(c.content);else{var g;c.hasOwnProperty("inner_attributes")?(g=j.findOrCreateElement(c.inner_attributes.id,e),j.setElementAttributes(g,c.inner_attributes)):g=e.find("> .cms-inner-container"),c.hasOwnProperty("rows")&&m.parse(c.rows,g),j.sortChildren(g),b||d!=a.length-1||j.sortChildren(e.closest(".cms-content"))}})},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return j.generateUrl(b,d.extend({},c||{},{containerId:e}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),j.request(d)},a.edit=function(a){o.createContainerPlugin(a.data("cms").type,a)},a.changeType=function(b,c){a.request(b,"ekyna_cms_editor_container_change_type",null,{data:{type:c}})},a.remove=function(b){a.request(b,"ekyna_cms_editor_container_remove")},a.add=function(a,b){var c=a.closest(".cms-content");if(1!=c.length)throw"Container content not found.";k.request(c,"ekyna_cms_editor_content_create_container",null,{data:{type:b}})},a.moveUp=function(b){a.request(b,"ekyna_cms_editor_container_move_up")},a.moveDown=function(b){a.request(b,"ekyna_cms_editor_container_move_down")},a}();b.ContainerManager=l;var m=function(){function a(){}return a.parse=function(a,b){a.forEach(function(c,d){if(!c.hasOwnProperty("attributes"))throw"Unexpected row data";var e=j.findOrCreateElement(c.attributes.id,b);j.setElementAttributes(e,c.attributes),c.hasOwnProperty("blocks")&&n.parse(c.blocks,e),j.sortChildren(e),b||d!=a.length-1||j.sortChildren(e.closest(".cms-inner-container"))})},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return j.generateUrl(b,d.extend({},c||{},{rowId:e}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),j.request(d)},a.remove=function(b){a.request(b,"ekyna_cms_editor_row_remove")},a.add=function(a){var b=a.closest(".cms-container");if(1!=b.length)throw"Row container not found.";l.request(b,"ekyna_cms_editor_container_create_row")},a.moveUp=function(b){a.request(b,"ekyna_cms_editor_row_move_up")},a.moveDown=function(b){a.request(b,"ekyna_cms_editor_row_move_down")},a}();b.RowManager=m;var n=function(){function a(){}return a.parse=function(a,b){a.forEach(function(c,d){var e,f;c.hasOwnProperty("attributes")&&(e=j.findOrCreateElement(c.attributes.id,b),j.setElementAttributes(e,c.attributes)),c.hasOwnProperty("plugin_attributes")&&(f=j.findOrCreateElement(c.plugin_attributes.id,e),j.setElementAttributes(f,c.plugin_attributes),c.hasOwnProperty("content")&&f.html(c.content)),!f&&!e||b||d!=a.length-1||(e||(e=f.closest(".cms-column")),j.sortChildren(e.closest(".cms-row")))})},a.generateUrl=function(a,b,c){var e=a.data("cms").id;if(!e)throw"Invalid id";return j.generateUrl(b,d.extend({},c||{},{blockId:e}))},a.request=function(a,b,c,d){return d=d||{},d.url=this.generateUrl(a,b,c),j.request(d)},a.edit=function(a){o.createBlockPlugin(a.data("cms").type,a)},a.changeType=function(b,c){a.request(b,"ekyna_cms_editor_block_change_type",null,{data:{type:c}})},a.remove=function(b){a.request(b,"ekyna_cms_editor_block_remove")},a.add=function(a,b){var c=a.closest(".cms-row");if(1!=c.length)throw"Block row not found.";m.request(c,"ekyna_cms_editor_row_create_block",null,{data:{type:b}})},a.moveLeft=function(b){a.request(b,"ekyna_cms_editor_block_move_left")},a.moveRight=function(b){a.request(b,"ekyna_cms_editor_block_move_right")},a.moveUp=function(a){throw"Not yet implemented"},a.moveDown=function(a){throw"Not yet implemented"},a.expand=function(b){a.request(b,"ekyna_cms_editor_block_expand")},a.compress=function(b){a.request(b,"ekyna_cms_editor_block_compress")},a}();b.BlockManager=n,g["default"].on("block.edit",function(a){return n.edit(a.get("data").$block)}),g["default"].on("block.change-type",function(a,b){return n.changeType(a.get("data").$block,b.data.type)}),g["default"].on("block.move-left",function(a){return n.moveLeft(a.get("data").$block)}),g["default"].on("block.move-right",function(a){return n.moveRight(a.get("data").$block)}),g["default"].on("block.move-up",function(a){return n.moveUp(a.get("data").$block)}),g["default"].on("block.move-down",function(a){return n.moveDown(a.get("data").$block)}),g["default"].on("block.expand",function(a){return n.expand(a.get("data").$block)}),g["default"].on("block.compress",function(a){return n.compress(a.get("data").$block)}),g["default"].on("block.remove",function(a){return n.remove(a.get("data").$block)}),g["default"].on("block.add",function(a,b){return n.add(a.get("data").$block,b.data.type)}),g["default"].on("row.move-up",function(a){return m.moveUp(a.get("data").$row)}),g["default"].on("row.move-down",function(a){return m.moveDown(a.get("data").$row)}),g["default"].on("row.remove",function(a){return m.remove(a.get("data").$row)}),g["default"].on("row.add",function(a){return m.add(a.get("data").$row)}),g["default"].on("container.edit",function(a){return l.edit(a.get("data").$container)}),g["default"].on("container.change-type",function(a,b){return l.changeType(a.get("data").$container,b.data.type)}),g["default"].on("container.move-up",function(a){return l.moveUp(a.get("data").$container)}),g["default"].on("container.move-down",function(a){return l.moveDown(a.get("data").$container)}),g["default"].on("container.remove",function(a){return l.remove(a.get("data").$container)}),g["default"].on("container.add",function(a,b){return l.add(a.get("data").$container,b.data.type)});var o=function(){function b(){}return b.load=function(a){this.registry=a},b.getActivePlugin=function(){if(!this.hasActivePlugin())throw"Active plugin is not set";return this.activePlugin},b.hasActivePlugin=function(){return!!this.activePlugin},b.clearActivePlugin=function(){var a=this;return this.hasActivePlugin()?this.activePlugin.destroy().then(function(){a.activePlugin=null}):i.resolve()},b.createPlugin=function(b,c,d){var e=this;this.clearActivePlugin().then(function(){throw b.forEach(function(b){return b.name===c?void a([b.path],function(a){e.activePlugin=new a(d,j.getContentWindow()),e.activePlugin.edit()}):void 0}),'Plugin "'+c+'" not found.'})},b.createBlockPlugin=function(a,b){this.createPlugin(this.getBlockPluginsConfig(),a,b)},b.createContainerPlugin=function(a,b){this.createPlugin(this.getContainerPluginsConfig(),a,b)},b.getBlockPluginsConfig=function(){if(!this.registry)throw"Plugins registry is not configured";return this.registry.block},b.getContainerPluginsConfig=function(){if(!this.registry)throw"Plugins registry is not configured";return this.registry.container},b}();b.PluginManager=o;var p=function(){function a(){}return a.getToolbar=function(){if(!this.hasToolbar())throw"Toolbar is not set";return this.toolbar},a.hasToolbar=function(){return!!this.toolbar},a.clearToolbar=function(){this.hasToolbar()&&(this.toolbar.remove(),this.toolbar=null)},a.createToolbar=function(a){this.clearToolbar(),this.toolbar=new h.ToolbarView({model:a}),c(document).find("body").append(this.toolbar.$el),this.toolbar.render()},a.createBlockToolbar=function(a,b){var c=a.closest(".cms-column"),d=c.closest(".cms-row"),e=new h.Toolbar({classes:["vertical","block-toolbar"],origin:b});e.addControl("default",new h.Button({name:"edit",title:"Edit",icon:"pencil",event:"block.edit",data:{$block:a}}));var f=[];o.getBlockPluginsConfig().forEach(function(a){f.push({name:a.name,title:a.title,confirm:"Êtes-vous sûr de vouloir changer le type de ce bloc ? (Le contenu actuel sera définitivement perdu).",data:{type:a.name}})}),e.addControl("default",new h.Button({name:"change-type",title:"Change type",icon:"cog",event:"block.change-type",data:{$block:a},choices:f})),1==d.length&&(e.addControl("horizontal",new h.Button({name:"move-left",title:"Move left",icon:"arrow-left",disabled:c.is(":first-child"),event:"block.move-left",data:{$block:a}})),e.addControl("horizontal",new h.Button({name:"move-right",title:"Move right",icon:"arrow-right",disabled:c.is(":last-child"),event:"block.move-right",data:{$block:a}})),e.addControl("resize",new h.Button({name:"expand",title:"Expand size",icon:"expand",disabled:function(a){var b=a.children(".cms-column").length;return!(b>1&&6>=b)}(d),event:"block.expand",data:{$block:a}})),e.addControl("resize",new h.Button({name:"compress",title:"Compress size",icon:"compress",disabled:1==d.children(".cms-column").length||2>=parseInt(c.data("cms").size),event:"block.compress",data:{$block:a}})),e.addControl("add",new h.Button({name:"remove",title:"Remove",icon:"remove",disabled:1>=d.children(".cms-column").length,confirm:"Êtes-vous sûr de vouloir supprimer ce bloc ?",event:"block.remove",data:{$block:a}})),f=[],o.getBlockPluginsConfig().forEach(function(a){f.push({name:a.name,title:a.title,data:{type:a.name}})}),e.addControl("add",new h.Button({name:"add",title:"Create a new block after this one",icon:"plus",disabled:6<=d.children(".cms-column").length,event:"block.add",data:{$block:a},choices:f}))),this.createToolbar(e)},a.createRowToolbar=function(a,b){var c=a.closest(".cms-inner-container"),d=new h.Toolbar({classes:["vertical","row-toolbar"],origin:b});1==c.length&&(d.addControl("move",new h.Button({name:"move-up",title:"Move up",icon:"arrow-up",disabled:a.is(":first-child"),event:"row.move-up",data:{$row:a}})),d.addControl("move",new h.Button({name:"move-down",title:"Move down",icon:"arrow-down",disabled:a.is(":last-child"),event:"row.move-down",data:{$row:a}})),d.addControl("add",new h.Button({name:"remove",title:"Remove",icon:"remove",disabled:1>=c.children(".cms-row").length,confirm:"Êtes-vous sûr de vouloir supprimer cette ligne ?",event:"row.remove",data:{$row:a}})),d.addControl("add",new h.Button({name:"add",title:"Create a new row",icon:"plus",event:"row.add",data:{$row:a}}))),this.createToolbar(d)},a.createContainerToolbar=function(a,b){var c=a.closest(".cms-content"),d=new h.Toolbar({classes:["vertical","container-toolbar"],origin:b});d.addControl("default",new h.Button({name:"edit",title:"Edit",icon:"pencil",event:"container.edit",data:{$container:a}}));var e=[];o.getContainerPluginsConfig().forEach(function(a){e.push({name:a.name,title:a.title,confirm:"Êtes-vous sûr de vouloir changer le type de ce contener ? (Le contenu actuel sera définitivement perdu).",data:{type:a.name}})}),d.addControl("default",new h.Button({name:"change-type",title:"Change type",icon:"cog",event:"container.change-type",data:{$container:a},choices:e})),1==c.length&&(d.addControl("move",new h.Button({name:"move-up",title:"Move up",icon:"arrow-up",disabled:a.is(":first-child"),event:"container.move-up",data:{$container:a}})),d.addControl("move",new h.Button({name:"move-down",title:"Move down",icon:"arrow-down",disabled:a.is(":last-child"),event:"container.move-down",data:{$container:a}})),d.addControl("add",new h.Button({name:"remove",title:"Remove",icon:"remove",disabled:1>=c.children(".cms-container").length,confirm:"Êtes-vous sûr de vouloir supprimer ce conteneur ?",event:"container.remove",data:{$container:a}})),e=[],o.getContainerPluginsConfig().forEach(function(a){e.push({name:a.name,title:a.title,data:{type:a.name}})}),d.addControl("add",new h.Button({name:"add",title:"Create a new container after this one",icon:"plus",event:"container.add",data:{$container:a},choices:e}))),this.createToolbar(d)},a}(),q=function(){function a(a){var b=this;this.enabled=!1,this.$clickTarget=null,this.clickOrigin=null,this.hostname=a,this.viewportOrigin={top:50,left:0},this.selectionOffset={top:0,left:0},this.selectionId=null,this.documentMouseDownHandler=function(a){return b.onDocumentMouseDown(a)},this.documentMouseUpHandler=function(){return b.onDocumentMouseUp()},this.powerClickHandler=function(a){return b.onPowerClick(a)},this.viewportLoadHandler=function(a,c){return b.onViewportLoad(a,c)},this.viewportUnloadHandler=function(a){return b.onViewportUnload(a)},this.viewportResizeHandler=function(a){return b.onViewportResize(a)}}return a.prototype.initialize=function(){var a=this;g["default"].on("viewport.resize",this.viewportResizeHandler),g["default"].on("base_manager.response_parsed",function(b){var c;b?c=j.findElementById(b):a.selectionId&&(c=j.findElementById(a.selectionId)),a.deselect().then(function(){c&&1==c.length&&a.select(c)})}),g["default"].on("block.edit",function(){return p.clearToolbar()})},a.prototype.onPowerClick=function(a){var b=a.get("active");b&&!this.enabled?(this.enabled=!0,this.enableEdition()):this.enabled&&!b?(this.enabled=!1,this.disableEdition()):this.enabled=b},a.prototype.onViewportLoad=function(a,b){var d=this;return j.setContentWindow(a),j.setContentDocument(c(b)),j.getContentDocument().find("a[href]").off("click").on("click",function(a){a.preventDefault(),a.stopPropagation();var b=a.currentTarget;b.hostname!==d.hostname?console.log("Attempt to navigate out of the website has been blocked."):g["default"].trigger("document_manager.navigate",b.href)}),this.enabled&&this.enableEdition(),g["default"].trigger("document_manager.document_data",j.getDocumentData()),this},a.prototype.onViewportUnload=function(a){return a.defaultPrevented?void 0:o.hasActivePlugin()&&o.getActivePlugin().isUpdated()?(a.preventDefault(),a.returnValue="Vos changements n'ont pas été sauvegardés !",this):(o.clearActivePlugin(),j.clear(),this)},a.prototype.onViewportResize=function(a){return this.viewportOrigin=a,p.hasToolbar()&&p.getToolbar().applyOriginOffset(a),this},a.prototype.onDocumentMouseDown=function(a){this.$clickTarget=null,this.clickOrigin=null;var b={top:a.clientY,left:a.clientX},d=c(a.target);if(!(0<d.closest("#editor-document-toolbar").length||o.hasActivePlugin()&&o.getActivePlugin().preventDocumentSelection(d))){var e=d.closest(".cms-block, .cms-row, .cms-container");1==e.length?e.attr("id")!=this.selectionId&&(this.clickOrigin=b,this.$clickTarget=e):this.clickOrigin=b}},a.prototype.onDocumentMouseUp=function(){var a=this;this.clickOrigin&&this.deselect().then(function(){a.$clickTarget?a.select(a.$clickTarget,a.clickOrigin):a.createToolbar(),a.$clickTarget=null,a.clickOrigin=null})},a.prototype.deselect=function(){var a=this;return o.clearActivePlugin().then(function(){p.clearToolbar(),a.selectionId&&(j.findElementById(a.selectionId).removeClass("selected"),a.selectionId=null)})},a.prototype.select=function(a,b){1==a.length&&(this.selectionId=a.addClass("selected").attr("id"),this.createToolbar(a,b))},a.prototype.createToolbar=function(a,b){if(a=a||j.findElementById(this.selectionId),1==a.length){if(b?this.selectionOffset={top:b.top-a.offset().top,left:b.left-a.offset().left}:b={top:a.offset().top+this.selectionOffset.top,left:a.offset().left+this.selectionOffset.left},a.hasClass("cms-block"))p.createBlockToolbar(a,b);else if(a.hasClass("cms-row"))p.createRowToolbar(a,b);else{if(!a.hasClass("cms-container"))throw"Unexpected element";p.createContainerToolbar(a,b)}p.getToolbar().applyOriginOffset(this.viewportOrigin)}},a.prototype.enableEdition=function(){var a=j.getContentDocument();if(this.enabled&&null!==a){if(0==a.find("link#cms-editor-stylesheet").length){var b=document.createElement("link");b.id="cms-editor-stylesheet",b.href="/bundles/ekynacms/css/editor-document.css",b.type="text/css",b.rel="stylesheet",a.find("head").append(b)}return a.on("mousedown",this.documentMouseDownHandler),a.on("mouseup",this.documentMouseUpHandler),this}},a.prototype.disableEdition=function(){var a=j.getContentDocument();if(!this.enabled&&null!==a){this.deselect(),a.off("mousedown",this.documentMouseDownHandler),a.off("mouseup",this.documentMouseUpHandler);var b=a.find("link#cms-editor-stylesheet");return b.length&&b.remove(),this}},a}();b.DocumentManager=q});