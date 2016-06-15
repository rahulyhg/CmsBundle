var __extends=this&&this.__extends||function(a,b){function c(){this.constructor=a}for(var d in b)b.hasOwnProperty(d)&&(a[d]=b[d]);a.prototype=null===b?Object.create(b):(c.prototype=b.prototype,new c)};define(["require","exports","es6-promise","../base-plugin","../../document-manager"],function(a,b,c,d,e){"use strict";c.polyfill();var f=c.Promise,g=function(a){function b(){a.apply(this,arguments)}return __extends(b,a),b.prototype.edit=function(){var b=this;a.prototype.edit.call(this),this.initialize().then(function(){b.destroyed||b.createEditor()})},b.prototype.save=function(){var a=this;return this.initialize().then(function(){if(a.isUpdated()){var b=a.tinymce.get("tinymce-plugin-editor");if(!b)throw"Failed to get tinymce editor instance.";var c=b.getContent();return e.BlockManager.request("ekyna_cms_editor_block_edit",a.$block,{data:{data:{content:c}}}).then(function(){a.$block.html(c),a.updated=!1})}})},b.prototype.destroy=function(){var a=this;return this.save().then(function(){var b=a.tinymce.get("tinymce-plugin-editor");b&&b.remove()})},b.prototype.focus=function(){var a=this;this.initialize().then(function(){var b=a.tinymce.get("tinymce-plugin-editor");b&&b.focus()})},b.prototype.initialize=function(){var a=this;return this.initPromise||(this.initPromise=new f(function(b){if(a.tinymce&&b(),!a.window.hasOwnProperty("require")||"function"!=typeof a.window.require)throw"requireJs is not available the content window.";a.window.require(["json!tinymce_config","tinymce"],function(c){if("undefined"==typeof a.window.tinymce)throw"Failed to load tinymce from the content iFrame.";if(a.config=c,a.tinymce=a.window.tinymce,a.tinymce.baseURL=a.config.tinymce_url,a.tinymce.suffix=".min",a.externalPlugins=[],"object"==typeof a.config.external_plugins)for(var d in a.config.external_plugins)if(a.config.external_plugins.hasOwnProperty(d)){var e=a.config.external_plugins[d],f=e.url||null;f&&(a.externalPlugins.push({id:d,url:f}),a.tinymce.PluginManager.load(d,f))}b()})})),this.initPromise},b.prototype.createEditor=function(){var a=this;this.$block.wrapInner('<div id="tinymce-plugin-editor"></div>');var b=this.config.theme.advanced;b.external_plugins=b.external_plugins||{};for(var c=0;c<this.externalPlugins.length;c++)b.external_plugins[this.externalPlugins[c].id]=this.externalPlugins[c].url;b.add_unload_trigger=!1,b.inline=!0,b.menubar=!1,b.entity_encoding="raw",b.toolbar_items_size="small",b.paste_as_text=!0,b.relative_urls=!1,b.content_css=[],b.setup=function(b){if("object"==typeof a.config.tinymce_buttons)for(var c in a.config.tinymce_buttons)a.config.tinymce_buttons.hasOwnProperty(c)&&!function(a,c){c.onclick=function(){var c=window["tinymce_button_"+a];"function"==typeof c?c(b):alert('You have to create callback function: "tinymce_button_'+a+'"')},b.addButton(a,c)}(c,clone(a.config.tinymce_buttons[c]));b.on("click",function(a){a.stopPropagation()}),b.on("init",function(){if(a.config.use_callback_tinymce_init){var c=window.callback_tinymce_init;"function"==typeof c?c(b):alert("You have to create callback function: callback_tinymce_init")}b.focus()}),b.on("change",function(){a.setUpdated(!0)})};var d=new this.tinymce.Editor("tinymce-plugin-editor",b,this.tinymce.EditorManager);d.render(),d.show()},b}(d.BasePlugin);return g});