/// <reference path="../../../../../../../../../typings/index.d.ts" />

import * as $ from 'jquery';
import * as es6Promise from 'es6-promise';
import * as Modal from 'ekyna-modal';
import Dispatcher from '../../dispatcher';

import {BasePlugin} from '../base-plugin';
import {ContainerManager, SelectionEvent, ElementData} from '../../document-manager';

es6Promise.polyfill();
let Promise = es6Promise.Promise;

/**
 * BackgroundPlugin
 * @todo use CamanJS (http://camanjs.com/guides/)
 */
class BackgroundPlugin extends BasePlugin {
    modal:Ekyna.Modal;

    edit() {
        super.edit();

        let id = (<ElementData>this.$element.data('cms')).id;
        if (!id) {
            throw 'Invalid block id';
        }

        this.modal = new Modal();
        this.modal.load({
            url: ContainerManager.generateUrl(this.$element, 'ekyna_cms_editor_container_edit'),
            method: 'GET'
        });

        $(this.modal).on('ekyna.modal.response', (e:Ekyna.ModalResponseEvent) => {
            if (e.contentType == 'json') {
                e.preventDefault();

                if (e.content.hasOwnProperty('containers')) {
                    ContainerManager.parse(e.content.containers);

                    let event:SelectionEvent = new SelectionEvent();
                    event.$element = this.$element;
                    Dispatcher.trigger('document_manager.select', event);
                }
            }
        });
    }

    destroy():Promise<any> {
        return this
            .save()
            .then(() => {
                if (this.modal) {
                    this.modal.close();
                    this.modal = null;
                }

                return super.destroy();
            });
    }

    preventDocumentSelection ($target:JQuery):boolean {
        return false;
    }
}

export = BackgroundPlugin;

