jQuery(document).ready(function($) {
    // Create and append modal HTML
    const modalHtml = `
        <div id="aicg-ai-modal" class="aicg-ai-modal">
            <div class="aicg-ai-modal-content">
                <span class="aicg-ai-close">&times;</span>
                <h2>${wp.i18n.__('Generate Content with AI', 'ai-content-generator')}</h2>
                
                <div class="aicg-ai-form">
                    <div class="aicg-ai-field">
                        <label for="aicg-ai-provider">${wp.i18n.__('AI Provider', 'ai-content-generator')}</label>
                        <select id="aicg-ai-provider">
                            <option value="openrouter">${wp.i18n.__('OpenRouter', 'ai-content-generator')}</option>
                            <option value="groq">${wp.i18n.__('Groq', 'ai-content-generator')}</option>
                        </select>
                    </div>

                    <div class="aicg-ai-field">
                        <label for="aicg-ai-type">${wp.i18n.__('Content Type', 'ai-content-generator')}</label>
                        <select id="aicg-ai-type">
                            <option value="post">${wp.i18n.__('Blog Post', 'ai-content-generator')}</option>
                            <option value="page">${wp.i18n.__('Page', 'ai-content-generator')}</option>
                            <option value="product">${wp.i18n.__('Product Description', 'ai-content-generator')}</option>
                        </select>
                    </div>

                    <div class="aicg-ai-field">
                        <label for="aicg-ai-format">${wp.i18n.__('Output Format', 'ai-content-generator')}</label>
                        <select id="aicg-ai-format">
                            <option value="text">${wp.i18n.__('Plain Text', 'ai-content-generator')}</option>
                            <option value="html">${wp.i18n.__('HTML', 'ai-content-generator')}</option>
                        </select>
                    </div>

                    <div class="aicg-ai-field">
                        <label for="aicg-ai-model">${wp.i18n.__('AI Model', 'ai-content-generator')}</label>
                        <select id="aicg-ai-model">
                            ${Object.entries(aicgSettings.models).map(([id, name]) => 
                                `<option value="${id}">${name}</option>`
                            ).join('')}
                        </select>
                    </div>

                    <div class="aicg-ai-field">
                        <label for="aicg-ai-tone">${wp.i18n.__('Content Tone', 'ai-content-generator')}</label>
                        <select id="aicg-ai-tone">
                            <option value="professional">${wp.i18n.__('Professional', 'ai-content-generator')}</option>
                            <option value="casual">${wp.i18n.__('Casual', 'ai-content-generator')}</option>
                            <option value="friendly">${wp.i18n.__('Friendly', 'ai-content-generator')}</option>
                            <option value="authoritative">${wp.i18n.__('Authoritative', 'ai-content-generator')}</option>
                            <option value="academic">${wp.i18n.__('Academic', 'ai-content-generator')}</option>
                            <option value="humorous">${wp.i18n.__('Humorous', 'ai-content-generator')}</option>
                            <option value="persuasive">${wp.i18n.__('Persuasive', 'ai-content-generator')}</option>
                        </select>
                    </div>

                    <div class="aicg-ai-field aicg-ai-field-inline">
                        <label>${wp.i18n.__('Character Count', 'ai-content-generator')}</label>
                        <div class="aicg-ai-field-group">
                            <input type="number" id="aicg-ai-min-chars" placeholder="Min" min="100" max="10000" value="500">
                            <span class="aicg-ai-separator">-</span>
                            <input type="number" id="aicg-ai-max-chars" placeholder="Max" min="100" max="10000" value="2000">
                        </div>
                    </div>

                    <div class="aicg-ai-field">
                        <label for="aicg-ai-prompt">${wp.i18n.__('Content Prompt', 'ai-content-generator')}</label>
                        <textarea id="aicg-ai-prompt" rows="4" placeholder="${
                            wp.i18n.__('Describe the content you want to generate...', 'ai-content-generator')
                        }"></textarea>
                    </div>

                    <div class="aicg-ai-field">
                        <label>
                            <input type="checkbox" id="aicg-ai-seo" checked>
                            ${wp.i18n.__('Optimize for SEO', 'ai-content-generator')}
                        </label>
                    </div>

                    <div class="aicg-ai-actions">
                        <button type="button" id="aicg-ai-generate" class="button button-primary">
                            ${wp.i18n.__('Generate', 'ai-content-generator')}
                        </button>
                        <button type="button" id="aicg-ai-cancel" class="button">
                            ${wp.i18n.__('Cancel', 'ai-content-generator')}
                        </button>
                    </div>
                </div>

                <div class="aicg-ai-loading" style="display: none;">
                    <span class="spinner is-active"></span>
                    ${wp.i18n.__('Generating content...', 'ai-content-generator')}
                </div>

                <div class="aicg-ai-preview" style="display: none;">
                    <h3>${wp.i18n.__('Generated Content', 'ai-content-generator')}</h3>
                    <div class="aicg-ai-preview-content"></div>
                    <div class="aicg-ai-preview-actions">
                        <button type="button" id="aicg-ai-insert" class="button button-primary">
                            ${wp.i18n.__('Insert Content', 'ai-content-generator')}
                        </button>
                        <button type="button" id="aicg-ai-regenerate" class="button">
                            ${wp.i18n.__('Generate Again', 'ai-content-generator')}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('body').append(modalHtml);

    // Cache DOM elements
    const $modal = $('#aicg-ai-modal');
    const $form = $('.aicg-ai-form');
    const $loading = $('.aicg-ai-loading');
    const $preview = $('.aicg-ai-preview');
    const $previewContent = $('.aicg-ai-preview-content');
    const $modelSelect = $('#aicg-ai-model');
    const $providerSelect = $('#aicg-ai-provider');

    // Update models when provider changes
    $providerSelect.on('change', function() {
        const provider = $(this).val();
        
        // Limpa o select de modelos antes de fazer a requisição
        $modelSelect.empty().append('<option value="">' + wp.i18n.__('Loading models...', 'ai-content-generator') + '</option>');
        
        $.ajax({
            url: aicgSettings.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'aicg_get_models',
                nonce: aicgSettings.nonce,
                provider: provider
            },
            success: function(response) {
                if (response.success && response.data) {
                    $modelSelect.empty();
                    Object.entries(response.data).forEach(([id, name]) => {
                        $modelSelect.append(`<option value="${id}">${name}</option>`);
                    });
                }
            },
            error: function() {
                $modelSelect.empty();
                $modelSelect.append(`<option value="">${wp.i18n.__('Failed to load models', 'ai-content-generator')}</option>`);
                alert(wp.i18n.__('Failed to load models. Please try again.', 'ai-content-generator'));
            }
        });
    });

    // Default settings for each format and type
    const formatDefaults = {
        text: {
            post: {
                min: 1000,
                max: 5000,
                placeholder: 'Descreva o tema do post, público-alvo e principais pontos a serem abordados...'
            },
            page: {
                min: 2000,
                max: 8000,
                placeholder: 'Descreva o objetivo da página, público-alvo e principais informações a serem incluídas...'
            },
            product: {
                min: 500,
                max: 3000,
                placeholder: 'Descreva o produto, seus principais benefícios e características técnicas...'
            }
        },
        html: {
            post: {
                min: 1500,
                max: 6000,
                placeholder: 'Descreva o conteúdo desejado para gerar o HTML com tags semânticas apropriadas...'
            },
            page: {
                min: 2500,
                max: 10000,
                placeholder: 'Descreva o conteúdo da página para gerar o HTML com estrutura semântica completa...'
            },
            product: {
                min: 800,
                max: 4000,
                placeholder: 'Descreva o produto para gerar o HTML com marcação estruturada e rica...'
            }
        }
    };

    // Update form based on content type and format
    function updateFormDefaults() {
        const type = $('#aicg-ai-type').val();
        const format = $('#aicg-ai-format').val();
        const defaults = formatDefaults[format][type];
        
        $('#aicg-ai-min-chars').val(defaults.min);
        $('#aicg-ai-max-chars').val(defaults.max);
        $('#aicg-ai-prompt').attr('placeholder', defaults.placeholder);
    }

    $('#aicg-ai-type, #aicg-ai-format').on('change', updateFormDefaults);

    // Show modal when AI button is clicked
    $('#aicg-ai-button').on('click', function() {
        updateFormDefaults();
        $modal.show();
        $form.show();
        $loading.hide();
        $preview.hide();
    });

    // Close modal
    $('.aicg-ai-close, #aicg-ai-cancel').on('click', function() {
        $modal.hide();
    });

    // Generate content
    $('#aicg-ai-generate').on('click', function() {
        const prompt = $('#aicg-ai-prompt').val().trim();
        if (!prompt) {
            alert(wp.i18n.__('Por favor, insira uma descrição do conteúdo desejado', 'ai-content-generator'));
            return;
        }

        const model = $('#aicg-ai-model').val();
        if (!model) {
            alert(wp.i18n.__('Por favor, selecione um modelo de IA', 'ai-content-generator'));
            return;
        }

        const type = $('#aicg-ai-type').val();
        const format = $('#aicg-ai-format').val();
        const tone = $('#aicg-ai-tone').val();
        const minChars = $('#aicg-ai-min-chars').val();
        const maxChars = $('#aicg-ai-max-chars').val();
        const seo = $('#aicg-ai-seo').is(':checked');

        // Validate character count
        if (parseInt(minChars) > parseInt(maxChars)) {
            alert(wp.i18n.__('O número mínimo de caracteres não pode ser maior que o máximo', 'ai-content-generator'));
            return;
        }

        // Make AJAX call to generate content
        $form.hide();
        $loading.show();

        $.ajax({
            url: aicgSettings.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'aicg_generate_content',
                nonce: aicgSettings.nonce,
                prompt: prompt,
                model: model,
                type: type,
                format: format,
                tone: tone,
                min_chars: minChars,
                max_chars: maxChars,
                seo: seo
            },
            success: function(response) {
                if (response.success && response.data) {
                    try {
                        const content = response.data;
                        if (format === 'html') {
                            // Exibe o HTML como texto para visualização
                            $previewContent.text(content);
                        } else {
                            $previewContent.html(content);
                        }
                        $loading.hide();
                        $preview.show();
                    } catch (e) {
                        console.error('Erro ao processar resposta:', e);
                        alert(wp.i18n.__('Erro ao processar o conteúdo gerado. Por favor, tente novamente.', 'ai-content-generator'));
                        $loading.hide();
                        $form.show();
                    }
                } else {
                    const errorMessage = response.data || wp.i18n.__('Erro ao gerar conteúdo. Por favor, tente novamente.', 'ai-content-generator');
                    alert(errorMessage);
                    $loading.hide();
                    $form.show();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Erro na requisição AJAX:', textStatus, errorThrown);
                alert(wp.i18n.__('Falha ao gerar conteúdo. Por favor, verifique sua conexão e tente novamente.', 'ai-content-generator'));
                $loading.hide();
                $form.show();
            }
        });
    });

    // Insert content into editor
    $('#aicg-ai-insert').on('click', function() {
        const format = $('#aicg-ai-format').val();
        let content;
        
        try {
            content = format === 'html' ? $previewContent.text() : $previewContent.html();
            
            if (!content) {
                alert(wp.i18n.__('Nenhum conteúdo para inserir. Por favor, gere o conteúdo primeiro.', 'ai-content-generator'));
                return;
            }

            // Verifica se o editor TinyMCE está disponível
            if (typeof tinyMCE !== 'undefined') {
                const editor = tinyMCE.get('content');
                
                if (format === 'html') {
                    // Se for HTML, insere na aba HTML
                    if (!$('#content-html').hasClass('active')) {
                        $('#content-html').trigger('click');
                    }
                    $('#content').val(content);
                } else {
                    // Se for texto, insere no editor visual
                    if (!$('#content-tmce').hasClass('active')) {
                        $('#content-tmce').trigger('click');
                    }
                    
                    // Aguarda um momento para o editor visual estar pronto
                    setTimeout(function() {
                        const visualEditor = tinyMCE.get('content');
                        if (visualEditor) {
                            visualEditor.setContent(content);
                        } else {
                            $('#content').val(content);
                        }
                    }, 100);
                }
                
                $modal.hide();
            } else {
                // Fallback para o textarea se o TinyMCE não estiver disponível
                $('#content').val(content);
                $modal.hide();
            }
        } catch (e) {
            console.error('Erro ao inserir conteúdo:', e);
            alert(wp.i18n.__('Erro ao inserir o conteúdo. Por favor, tente novamente.', 'ai-content-generator'));
        }
    });

    // Regenerate content
    $('#aicg-ai-regenerate').on('click', function() {
        $preview.hide();
        $form.show();
    });

    // Close modal when clicking outside
    $(window).on('click', function(event) {
        if ($(event.target).is($modal)) {
            $modal.hide();
        }
    });
}); 