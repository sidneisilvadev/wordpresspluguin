(function($) {
    'use strict';

    class AutoSave {
        constructor() {
            this.interval = 60000; // 1 minuto
            this.isDirty = false;
            this.lastSaved = new Date();
            this.init();
        }

        init() {
            this.createStatusBar();
            this.bindEvents();
            this.startAutoSave();
        }

        createStatusBar() {
            this.statusBar = $('<div/>', {
                class: 'mce-autosave-status',
                html: `
                    <span class="status-text">Último salvamento: Agora</span>
                    <label class="autosave-toggle">
                        <input type="checkbox" checked> 
                        Auto-save ativo
                    </label>
                `
            });

            $('#post').append(this.statusBar);
        }

        bindEvents() {
            if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                tinymce.activeEditor.on('change', () => {
                    this.isDirty = true;
                });
            }

            $('#content').on('change keyup', () => {
                this.isDirty = true;
            });

            $('.autosave-toggle input').on('change', (e) => {
                if (e.target.checked) {
                    this.startAutoSave();
                } else {
                    this.stopAutoSave();
                }
            });
        }

        startAutoSave() {
            this.autoSaveInterval = setInterval(() => {
                this.saveContent();
            }, this.interval);
        }

        stopAutoSave() {
            clearInterval(this.autoSaveInterval);
            this.updateStatus('Auto-save desativado');
        }

        saveContent() {
            if (!this.isDirty) {
                return;
            }

            const content = tinymce.activeEditor 
                ? tinymce.activeEditor.getContent()
                : $('#content').val();

            const postData = {
                post_ID: $('#post_ID').val(),
                content: content,
                action: 'mce_auto_save',
                _ajax_nonce: mceAutoSave.nonce
            };

            $.post(ajaxurl, postData)
                .done(() => {
                    this.isDirty = false;
                    this.lastSaved = new Date();
                    this.updateStatus('Salvo automaticamente');
                })
                .fail(() => {
                    this.updateStatus('Erro ao salvar', true);
                });
        }

        updateStatus(message, isError = false) {
            const time = new Date().toLocaleTimeString();
            const status = isError 
                ? `<span class="error">${message}</span>` 
                : message;
            
            this.statusBar.find('.status-text').html(`${status} às ${time}`);
        }
    }

    // Inicializa quando o documento estiver pronto
    $(document).ready(() => {
        new AutoSave();
    });

})(jQuery); 