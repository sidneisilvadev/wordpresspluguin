(function($) {
    'use strict';

    // Classe para gerenciar estatísticas do editor
    class EditorStats {
        constructor() {
            this.editor = null;
            this.statsContainer = null;
            this.init();
        }

        init() {
            // Remove qualquer container de estatísticas existente
            $('.mce-stats-container').remove();
            
            // Aguarda o TinyMCE estar pronto
            $(document).on('tinymce-editor-setup', (event, editor) => {
                this.editor = editor;
                this.createStatsContainer();
                this.bindEvents();
                this.updateStats();
            });
        }

        createStatsContainer() {
            // Remove container existente se houver
            if (this.statsContainer) {
                this.statsContainer.remove();
            }

            // Cria o container de estatísticas
            this.statsContainer = $('<div/>', {
                class: 'mce-stats-container',
                html: `
                    <span class="mce-stat-item" id="mce-char-count">
                        <i class="dashicons dashicons-editor-textcolor"></i> 
                        Caracteres: <span>0</span>
                    </span>
                    <span class="mce-stat-item" id="mce-word-count">
                        <i class="dashicons dashicons-editor-paragraph"></i> 
                        Palavras: <span>0</span>
                    </span>
                    <span class="mce-stat-item" id="mce-sentence-count">
                        <i class="dashicons dashicons-editor-alignleft"></i> 
                        Frases: <span>0</span>
                    </span>
                    <span class="mce-stat-item" id="mce-token-count">
                        <i class="dashicons dashicons-tag"></i> 
                        Tokens: <span>0</span>
                    </span>
                `
            });

            // Adiciona o container após o editor
            $('#wp-content-editor-container').after(this.statsContainer);
        }

        bindEvents() {
            // Remove eventos anteriores se existirem
            this.editor.off('keyup change');
            
            // Atualiza as estatísticas quando o conteúdo muda
            this.editor.on('keyup change', () => this.updateStats());
        }

        updateStats() {
            const content = this.editor.getContent({format: 'text'});
            
            // Conta caracteres (incluindo espaços)
            const charCount = content.length;
            
            // Conta palavras
            const wordCount = content.trim()
                ? content.trim().split(/\s+/).length
                : 0;
            
            // Conta frases (baseado em pontuação final)
            const sentenceCount = content.trim()
                ? content.split(/[.!?]+\s/).length
                : 0;
            
            // Conta tokens (palavras únicas)
            const tokens = new Set(
                content.toLowerCase()
                    .trim()
                    .split(/\s+/)
                    .filter(word => word.length > 0)
            );
            const tokenCount = tokens.size;

            // Atualiza os contadores
            $('#mce-char-count span').text(charCount);
            $('#mce-word-count span').text(wordCount);
            $('#mce-sentence-count span').text(sentenceCount);
            $('#mce-token-count span').text(tokenCount);
        }
    }

    // Inicializa quando o documento estiver pronto
    $(document).ready(() => {
        // Remove instâncias anteriores se existirem
        $('.mce-stats-container').remove();
        
        // Cria nova instância
        new EditorStats();
    });

})(jQuery); 