(function() {
    tinymce.create('tinymce.plugins.AICGEditorStyles', {
        init : function(ed, url) {
            // Font Family Dropdown
            ed.addButton('aicg_font_family', {
                type: 'listbox',
                text: 'Fonte',
                tooltip: 'Escolha a fonte',
                className: 'aicg-style-dropdown',
                values: [
                    {text: 'Arial', value: 'Arial'},
                    {text: 'Times New Roman', value: 'Times New Roman'},
                    {text: 'Helvetica', value: 'Helvetica'},
                    {text: 'Georgia', value: 'Georgia'},
                    {text: 'Verdana', value: 'Verdana'},
                    {text: 'Roboto', value: 'Roboto'}
                ],
                onselect: function(e) {
                    var value = e.control.settings.value;
                    ed.execCommand('FontName', false, value);
                }
            });

            // Font Size Input
            ed.addButton('aicg_font_size', {
                type: 'listbox',
                text: 'Tamanho',
                tooltip: 'Tamanho da fonte',
                className: 'aicg-style-dropdown',
                values: Array.from({length: 100}, (_, i) => ({
                    text: (i + 1) + 'px',
                    value: (i + 1) + 'px'
                })),
                onselect: function(e) {
                    var value = e.control.settings.value;
                    ed.execCommand('FontSize', false, value);
                }
            });

            // Text Color Picker
            ed.addButton('aicg_text_color', {
                type: 'colorbutton',
                text: 'Cor',
                tooltip: 'Cor do texto',
                className: 'aicg-color-picker',
                onselect: function(e) {
                    ed.execCommand('ForeColor', false, e.value);
                }
            });
        },
        
        createControl : function(n, cm) {
            return null;
        }
    });

    tinymce.PluginManager.add('aicg_editor_styles', tinymce.plugins.AICGEditorStyles);
})();