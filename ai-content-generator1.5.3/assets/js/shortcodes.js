(function() {
    tinymce.create('tinymce.plugins.AICGShortcodes', {
        init: function(editor, url) {
            // Button shortcode
            editor.addButton('aicg_button', {
                title: 'Insert Button',
                icon: 'icon dashicons-button',
                onclick: function() {
                    editor.windowManager.open({
                        title: 'Insert Button',
                        body: [
                            {
                                type: 'listbox',
                                name: 'type',
                                label: 'Button Type',
                                values: [
                                    {text: 'Primary', value: 'primary'},
                                    {text: 'Secondary', value: 'secondary'},
                                    {text: 'Success', value: 'success'},
                                    {text: 'Warning', value: 'warning'},
                                    {text: 'Danger', value: 'danger'}
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'size',
                                label: 'Button Size',
                                values: [
                                    {text: 'Small', value: 'small'},
                                    {text: 'Medium', value: 'medium'},
                                    {text: 'Large', value: 'large'}
                                ]
                            },
                            {
                                type: 'textbox',
                                name: 'text',
                                label: 'Button Text'
                            },
                            {
                                type: 'textbox',
                                name: 'url',
                                label: 'Button URL'
                            },
                            {
                                type: 'listbox',
                                name: 'target',
                                label: 'Open in',
                                values: [
                                    {text: 'Same Window', value: '_self'},
                                    {text: 'New Window', value: '_blank'}
                                ]
                            }
                        ],
                        onsubmit: function(e) {
                            editor.insertContent(
                                '[aicg_button type="' + e.data.type + 
                                '" size="' + e.data.size +
                                '" url="' + e.data.url + 
                                '" target="' + e.data.target + 
                                '"]' + e.data.text + '[/aicg_button]'
                            );
                        }
                    });
                }
            });

            // Columns shortcode
            editor.addButton('aicg_columns', {
                title: 'Insert Columns',
                icon: 'icon dashicons-columns',
                onclick: function() {
                    editor.windowManager.open({
                        title: 'Insert Columns',
                        body: [
                            {
                                type: 'listbox',
                                name: 'columns',
                                label: 'Number of Columns',
                                values: [
                                    {text: '2 Columns', value: '2'},
                                    {text: '3 Columns', value: '3'},
                                    {text: '4 Columns', value: '4'}
                                ]
                            },
                            {
                                type: 'listbox',
                                name: 'gap',
                                label: 'Column Gap',
                                values: [
                                    {text: 'Small', value: 'small'},
                                    {text: 'Normal', value: 'normal'},
                                    {text: 'Large', value: 'large'}
                                ]
                            }
                        ],
                        onsubmit: function(e) {
                            var content = '';
                            for (var i = 0; i < parseInt(e.data.columns); i++) {
                                content += '<p>Column ' + (i + 1) + ' content</p>';
                            }
                            editor.insertContent(
                                '[aicg_columns columns="' + e.data.columns +
                                '" gap="' + e.data.gap + '"]' +
                                content +
                                '[/aicg_columns]'
                            );
                        }
                    });
                }
            });

            // Alert shortcode
            editor.addButton('aicg_alert', {
                title: 'Insert Alert',
                icon: 'icon dashicons-warning',
                onclick: function() {
                    editor.windowManager.open({
                        title: 'Insert Alert',
                        body: [
                            {
                                type: 'listbox',
                                name: 'type',
                                label: 'Alert Type',
                                values: [
                                    {text: 'Info', value: 'info'},
                                    {text: 'Success', value: 'success'},
                                    {text: 'Warning', value: 'warning'},
                                    {text: 'Danger', value: 'danger'}
                                ]
                            },
                            {
                                type: 'textbox',
                                name: 'content',
                                label: 'Alert Content',
                                multiline: true,
                                minWidth: 300,
                                minHeight: 100
                            },
                            {
                                type: 'checkbox',
                                name: 'dismissible',
                                label: 'Dismissible',
                                checked: false
                            }
                        ],
                        onsubmit: function(e) {
                            editor.insertContent(
                                '[aicg_alert type="' + e.data.type + 
                                '" dismissible="' + (e.data.dismissible ? 'yes' : 'no') + 
                                '"]' + e.data.content + '[/aicg_alert]'
                            );
                        }
                    });
                }
            });

            // Tooltip shortcode
            editor.addButton('aicg_tooltip', {
                title: 'Insert Tooltip',
                icon: 'icon dashicons-info',
                onclick: function() {
                    editor.windowManager.open({
                        title: 'Insert Tooltip',
                        body: [
                            {
                                type: 'textbox',
                                name: 'content',
                                label: 'Text Content'
                            },
                            {
                                type: 'textbox',
                                name: 'tooltip',
                                label: 'Tooltip Text'
                            },
                            {
                                type: 'listbox',
                                name: 'position',
                                label: 'Tooltip Position',
                                values: [
                                    {text: 'Top', value: 'top'},
                                    {text: 'Right', value: 'right'},
                                    {text: 'Bottom', value: 'bottom'},
                                    {text: 'Left', value: 'left'}
                                ]
                            }
                        ],
                        onsubmit: function(e) {
                            editor.insertContent(
                                '[aicg_tooltip text="' + e.data.tooltip + 
                                '" position="' + e.data.position + 
                                '"]' + e.data.content + '[/aicg_tooltip]'
                            );
                        }
                    });
                }
            });
        },
        
        createControl: function(n, cm) {
            return null;
        },
        
        getInfo: function() {
            return {
                longname: 'AI Content Generator Shortcodes',
                author: 'AI Content Generator',
                version: '1.0'
            };
        }
    });
    
    tinymce.PluginManager.add('aicg_shortcodes', tinymce.plugins.AICGShortcodes);
})(); 