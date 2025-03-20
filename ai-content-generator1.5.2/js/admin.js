jQuery(document).ready(function($) {
    // Elementos da interface
    const $generateBtn = $('#aicg-generate');
    const $generateOutlineBtn = $('#aicg-generate-outline');
    const $generateFaqBtn = $('#aicg-generate-faq');
    const $generateKeywordsBtn = $('#aicg-generate-keywords');
    const $optimizeSeoBtn = $('#aicg-optimize-seo');
    const $clearBtn = $('#aicg-clear');
    const $tabs = $('.aicg-tab');
    const $result = $('#aicg-result');
    const $outlineResult = $('#aicg-outline-result');
    const $faqResult = $('#aicg-faq-result');
    const $temperatureInput = $('#aicg-temperature');
    const $temperatureValue = $('.aicg-temperature-value');
    
    // Atualiza o valor da temperatura
    $temperatureInput.on('input', function() {
        $temperatureValue.text($(this).val());
    });
    
    // Gerencia as tabs
    $tabs.on('click', function() {
        const tab = $(this).data('tab');
        
        $tabs.removeClass('active');
        $(this).addClass('active');
        
        $('.aicg-tab-content').removeClass('active');
        $(`#aicg-tab-${tab}`).addClass('active');
    });
    
    // Limpa os resultados
    $clearBtn.on('click', function() {
        $result.empty();
        $outlineResult.empty();
        $faqResult.empty();
    });
    
    // Função para mostrar mensagens
    function showMessage(container, type, message) {
        const $message = $('<div>')
            .addClass(`aicg-message ${type}`)
            .text(message);
        
        container.empty().append($message);
    }
    
    // Função para mostrar/esconder loading
    function toggleLoading(element, show) {
        if (show) {
            element.addClass('aicg-loading');
        } else {
            element.removeClass('aicg-loading');
        }
    }
    
    // Gera conteúdo principal
    $generateBtn.on('click', function() {
        const topic = $('#aicg-topic').val();
        const prompt = $('#aicg-prompt').val();
        const model = $('#aicg-model').val();
        const temperature = $('#aicg-temperature').val();
        const tone = $('#aicg-tone').val();
        
        if (!topic) {
            showMessage($result, 'error', AICG.i18n.noTopic);
            return;
        }
        
        toggleLoading($generateBtn, true);
        
        $.ajax({
            url: AICG.ajaxurl,
            type: 'POST',
            data: {
                action: 'aicg_generate_content',
                nonce: AICG.nonce,
                topic: topic,
                prompt: prompt,
                model: model,
                temperature: temperature,
                tone: tone
            },
            success: function(response) {
                if (response.success) {
                    $result.html(response.data);
                } else {
                    showMessage($result, 'error', response.data);
                }
            },
            error: function() {
                showMessage($result, 'error', AICG.i18n.ajaxError);
            },
            complete: function() {
                toggleLoading($generateBtn, false);
            }
        });
    });
    
    // Gera esboço
    $generateOutlineBtn.on('click', function() {
        const topic = $('#aicg-topic').val();
        const sections = $('#aicg-sections').val();
        
        if (!topic) {
            showMessage($outlineResult, 'error', AICG.i18n.noTopic);
            return;
        }
        
        toggleLoading($generateOutlineBtn, true);
        
        $.ajax({
            url: AICG.ajaxurl,
            type: 'POST',
            data: {
                action: 'aicg_generate_outline',
                nonce: AICG.nonce,
                topic: topic,
                sections: sections
            },
            success: function(response) {
                if (response.success) {
                    $outlineResult.html(response.data);
                } else {
                    showMessage($outlineResult, 'error', response.data);
                }
            },
            error: function() {
                showMessage($outlineResult, 'error', AICG.i18n.ajaxError);
            },
            complete: function() {
                toggleLoading($generateOutlineBtn, false);
            }
        });
    });
    
    // Gera FAQ
    $generateFaqBtn.on('click', function() {
        const topic = $('#aicg-topic').val();
        const count = $('#aicg-faq-count').val();
        
        if (!topic) {
            showMessage($faqResult, 'error', AICG.i18n.noTopic);
            return;
        }
        
        toggleLoading($generateFaqBtn, true);
        
        $.ajax({
            url: AICG.ajaxurl,
            type: 'POST',
            data: {
                action: 'aicg_generate_faq',
                nonce: AICG.nonce,
                topic: topic,
                count: count
            },
            success: function(response) {
                if (response.success) {
                    $faqResult.html(response.data);
                } else {
                    showMessage($faqResult, 'error', response.data);
                }
            },
            error: function() {
                showMessage($faqResult, 'error', AICG.i18n.ajaxError);
            },
            complete: function() {
                toggleLoading($generateFaqBtn, false);
            }
        });
    });
    
    // Gera palavras-chave
    $generateKeywordsBtn.on('click', function() {
        const topic = $('#aicg-topic').val();
        
        if (!topic) {
            showMessage($result, 'error', AICG.i18n.noTopic);
            return;
        }
        
        toggleLoading($generateKeywordsBtn, true);
        
        $.ajax({
            url: AICG.ajaxurl,
            type: 'POST',
            data: {
                action: 'aicg_generate_keywords',
                nonce: AICG.nonce,
                topic: topic
            },
            success: function(response) {
                if (response.success) {
                    $('#aicg-keywords').val(response.data);
                } else {
                    showMessage($result, 'error', response.data);
                }
            },
            error: function() {
                showMessage($result, 'error', AICG.i18n.ajaxError);
            },
            complete: function() {
                toggleLoading($generateKeywordsBtn, false);
            }
        });
    });
    
    // Otimiza para SEO
    $optimizeSeoBtn.on('click', function() {
        const content = $result.html();
        const keywords = $('#aicg-keywords').val();
        
        if (!content) {
            showMessage($result, 'error', AICG.i18n.noContent);
            return;
        }
        
        if (!keywords) {
            showMessage($result, 'error', AICG.i18n.noKeywords);
            return;
        }
        
        toggleLoading($optimizeSeoBtn, true);
        
        $.ajax({
            url: AICG.ajaxurl,
            type: 'POST',
            data: {
                action: 'aicg_optimize_seo',
                nonce: AICG.nonce,
                content: content,
                keywords: keywords
            },
            success: function(response) {
                if (response.success) {
                    $result.html(response.data);
                } else {
                    showMessage($result, 'error', response.data);
                }
            },
            error: function() {
                showMessage($result, 'error', AICG.i18n.ajaxError);
            },
            complete: function() {
                toggleLoading($optimizeSeoBtn, false);
            }
        });
    });
    
    // Atualiza o valor inicial da temperatura
    $temperatureValue.text($temperatureInput.val());
}); 