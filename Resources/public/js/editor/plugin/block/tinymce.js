var __extends=this&&this.__extends||function(a,b){function c(){this.constructor=a}for(var d in b)b.hasOwnProperty(d)&&(a[d]=b[d]);a.prototype=null===b?Object.create(b):(c.prototype=b.prototype,new c)};define(["require","exports","es6-promise","../../dispatcher","../base-plugin","../../document-manager"],function(a,b,c,d,e,f){"use strict";c.polyfill();var g=c.Promise,h=function(a){function b(){a.apply(this,arguments)}return __extends(b,a),b.clear=function(){b.initPromise=null,b.config=null,b.externalPlugins=null,b.tinymce=null},b.prototype.edit=function(){var b=this;a.prototype.edit.call(this),this.initialize().then(function(){return b.destroyed?g.resolve():void b.createEditor()})},b.prototype.save=function(){var c=this;return this.initialize().then(function(){if(c.isUpdated()){d["default"].trigger("editor.set_busy");var e=b.tinymce.get("tinymce-plugin-editor");if(!e)throw"Failed to get tinymce editor instance.";var h=e.getContent();return f.BlockManager.request(c.$element,"ekyna_cms_editor_block_edit",null,{data:{data:{content:h}}}).then(function(){return c.$element.html(h),c.updated=!1,d["default"].trigger("editor.unset_busy"),a.prototype.save.call(c)})}return g.resolve()})},b.prototype.destroy=function(){var c=this;return this.save().then(function(){var d=b.tinymce.get("tinymce-plugin-editor");d&&d.remove();var e=c.$element.find("#tinymce-plugin-editor");return e.length&&e.children().first().unwrap(),b.clear(),a.prototype.destroy.call(c)})},b.prototype.preventDocumentSelection=function(a){return 0<a.closest("#tinymce-plugin-editor, .mce-container").length},b.prototype.initialize=function(){var a=this;return b.initPromise||(d["default"].trigger("editor.set_busy"),b.initPromise=new g(function(c){if(b.tinymce&&c(),!a.window.hasOwnProperty("require")||"function"!=typeof a.window.require)throw"requireJs is not available the content window.";a.window.require(["json!tinymce_config","tinymce"],function(e){if("undefined"==typeof a.window.tinymce)throw"Failed to load tinymce from the content iFrame.";if(b.config=e,b.tinymce=a.window.tinymce,b.tinymce.baseURL=b.config.tinymce_url,b.tinymce.suffix=".min",b.externalPlugins=[],"object"==typeof b.config.external_plugins)for(var f in b.config.external_plugins)if(b.config.external_plugins.hasOwnProperty(f)){var g=b.config.external_plugins[f],h=g.url||null;h&&(b.externalPlugins.push({id:f,url:h}),b.tinymce.PluginManager.load(f,h))}d["default"].trigger("editor.unset_busy"),c()})})),b.initPromise},b.prototype.createEditor=function(){var a=this;0==this.$element.find("#tinymce-plugin-editor").length&&this.$element.wrapInner('<div id="tinymce-plugin-editor"></div>');var c=b.config.theme.advanced;c.external_plugins=c.external_plugins||{};for(var d=0;d<b.externalPlugins.length;d++)c.external_plugins[b.externalPlugins[d].id]=b.externalPlugins[d].url;c.add_unload_trigger=!1,c.inline=!0,c.menubar=!1,c.entity_encoding="raw",c.toolbar_items_size="small",c.paste_as_text=!0,c.relative_urls=!1,c.content_css=[],c.setup=function(c){if("object"==typeof b.config.tinymce_buttons)for(var d in b.config.tinymce_buttons)b.config.tinymce_buttons.hasOwnProperty(d)&&!function(a,b){b.onclick=function(){var b=this.window["tinymce_button_"+a];"function"==typeof b?b(c):alert('You have to create callback function: "tinymce_button_'+a+'"')},c.addButton(a,b)}(d,clone(b.config.tinymce_buttons[d]));c.on("click",function(a){a.stopPropagation()}),c.on("init",function(){if(b.config.use_callback_tinymce_init){var d=a.window.callback_tinymce_init;"function"==typeof d?d(c):alert("You have to create callback function: callback_tinymce_init")}c.focus()}),c.on("change",function(){a.setUpdated(!0)})};var e=new b.tinymce.Editor("tinymce-plugin-editor",c,b.tinymce.EditorManager);e.render(),e.show()},b}(e.BasePlugin);return h});