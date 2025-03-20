<?php
/**
 * AI Content Generation Class
 *
 * @package AIContentGenerator
 */

if (!defined('WPINC')) {
    die;
}

class AICG_Content {
    /**
     * API keys
     */
    private $openrouter_api_key;
    private $groq_api_key;
    private $api_provider;

    /**
     * Constructor
     */
    public function __construct() {
        $this->openrouter_api_key = get_option('aicg_openrouter_api_key', '');
        $this->groq_api_key = get_option('aicg_groq_api_key', '');
        $this->api_provider = get_option('aicg_ai_provider', 'openrouter');
        
        // Add menu item to settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add AI button to editor
        add_action('media_buttons', array($this, 'add_ai_button'), 15);
        
        // Add AJAX handlers
        add_action('wp_ajax_aicg_generate_content', array($this, 'ajax_generate_content'));
        add_action('wp_ajax_aicg_save_api_settings', array($this, 'ajax_save_api_settings'));
        add_action('wp_ajax_aicg_get_models', array($this, 'ajax_get_models'));
        
        // Add required scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('aicg_options', 'aicg_openrouter_api_key');
        register_setting('aicg_options', 'aicg_groq_api_key');
        
        add_settings_section(
            'aicg_ai_settings',
            __('AI Content Settings', 'ai-content-generator'),
            array($this, 'render_settings_section'),
            'ai-content-generator'
        );

        add_settings_field(
            'aicg_openrouter_api_key',
            __('OpenRouter API Key', 'ai-content-generator'),
            array($this, 'render_openrouter_api_key_field'),
            'ai-content-generator',
            'aicg_ai_settings'
        );

        add_settings_field(
            'aicg_groq_api_key',
            __('Groq API Key', 'ai-content-generator'),
            array($this, 'render_groq_api_key_field'),
            'ai-content-generator',
            'aicg_ai_settings'
        );
    }

    /**
     * Render settings section
     */
    public function render_settings_section() {
        echo '<p>' . __('Configure your AI content generation settings below.', 'ai-content-generator') . '</p>';
    }

    /**
     * Render OpenRouter API key field
     */
    public function render_openrouter_api_key_field() {
        ?>
        <input type="text" 
               id="aicg_openrouter_api_key" 
               name="aicg_openrouter_api_key" 
               value="<?php echo esc_attr($this->openrouter_api_key); ?>" 
               class="regular-text"
        />
        <p class="description">
            <?php _e('Enter your OpenRouter API key. Get one at openrouter.ai', 'ai-content-generator'); ?>
        </p>
        <?php
    }

    /**
     * Render Groq API key field
     */
    public function render_groq_api_key_field() {
        ?>
        <input type="text" 
               id="aicg_groq_api_key" 
               name="aicg_groq_api_key" 
               value="<?php echo esc_attr($this->groq_api_key); ?>" 
               class="regular-text"
        />
        <p class="description">
            <?php _e('Enter your Groq API key. Get one at groq.com', 'ai-content-generator'); ?>
        </p>
        <?php
    }

    /**
     * Get available AI models based on provider
     */
    private function get_available_models() {
        if ($this->api_provider === 'groq') {
            try {
                if (empty($this->groq_api_key)) {
                    return array(
                        'llama2-70b-4096' => 'Llama2 70B',
                        'mixtral-8x7b-32768' => 'Mixtral 8x7B',
                        'gemma-7b-it' => 'Gemma 7B'
                    );
                }

                $response = wp_remote_get('https://api.groq.com/openai/v1/models', array(
                    'headers' => array(
                        'Authorization' => 'Bearer ' . $this->groq_api_key,
                        'Content-Type' => 'application/json'
                    ),
                    'timeout' => 15,
                    'sslverify' => true
                ));

                if (is_wp_error($response)) {
                    error_log('Groq API Error: ' . $response->get_error_message());
                    throw new Exception($response->get_error_message());
                }

                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code !== 200) {
                    throw new Exception('Failed to fetch Groq models. Status code: ' . $response_code);
                }

                $body = json_decode(wp_remote_retrieve_body($response), true);
                
                if (!isset($body['data']) || !is_array($body['data'])) {
                    throw new Exception('Invalid response format from Groq API');
                }

                $models = array();
                foreach ($body['data'] as $model) {
                    if (isset($model['id'])) {
                        // Formata o nome do modelo para exibição
                        $display_name = ucfirst(str_replace(array('-', '_'), ' ', $model['id']));
                        $models[$model['id']] = $display_name;
                    }
                }

                if (empty($models)) {
                    throw new Exception('No models found in Groq API response');
                }

                return $models;

            } catch (Exception $e) {
                error_log('Error fetching Groq models: ' . $e->getMessage());
                // Retorna lista padrão em caso de erro
                return array(
                    'llama2-70b-4096' => 'Llama2 70B',
                    'mixtral-8x7b-32768' => 'Mixtral 8x7B',
                    'gemma-7b-it' => 'Gemma 7B'
                );
            }
        }

        // OpenRouter models
        try {
            if (empty($this->openrouter_api_key)) {
                throw new Exception('OpenRouter API key is not configured');
            }

            $response = wp_remote_get('https://openrouter.ai/api/v1/models', array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->openrouter_api_key,
                    'HTTP-Referer' => get_site_url(),
                    'X-Title' => get_bloginfo('name'),
                    'Content-Type' => 'application/json'
                ),
                'timeout' => 15,
                'sslverify' => true
            ));

            if (is_wp_error($response)) {
                error_log('OpenRouter API Error: ' . $response->get_error_message());
                throw new Exception($response->get_error_message());
            }

            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                throw new Exception('Failed to fetch OpenRouter models. Status code: ' . $response_code);
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            if (!isset($body['data']) || !is_array($body['data'])) {
                throw new Exception('Invalid response format from OpenRouter API');
            }

            $models = array();
            foreach ($body['data'] as $model) {
                if (isset($model['id']) && isset($model['name'])) {
                    // Adiciona apenas modelos gratuitos
                    if (strpos($model['id'], ':free') !== false) {
                        $models[$model['id']] = $model['name'];
                    }
                }
            }

            if (empty($models)) {
                throw new Exception('No free models found in OpenRouter API response');
            }

            return $models;

        } catch (Exception $e) {
            error_log('Error fetching OpenRouter models: ' . $e->getMessage());
            // Retorna lista padrão em caso de erro
            return array(
                'cognitivecomputations/dolphin3.0-r1-mistral-24b:free' => 'Dolphin 3.0 R1 Mistral 24B',
                'mistralai/mistral-small-24b-instruct-2501:free' => 'Mistral Small 24B Instruct',
                'deepseek/deepseek-r1-distill-llama-70b:free' => 'DeepSeek R1 Distill LLaMA 70B',
                'mistralai/mistral-7b-instruct:free' => 'Mistral: Mistral 7B Instruct',
                'openchat/openchat-7b:free' => 'OpenChat 3.5 7B'
            );
        }
    }

    /**
     * Generate content using selected API
     */
    private function generate_content($prompt, $model, $type = 'post', $format = 'text', $tone = 'professional', $min_chars = 500, $max_chars = 2000) {
        try {
            if ($this->api_provider === 'groq') {
                return $this->generate_content_groq($prompt, $model, $type, $format, $tone, $min_chars, $max_chars);
            }
            
            return $this->generate_content_openrouter($prompt, $model, $type, $format, $tone, $min_chars, $max_chars);
        } catch (Exception $e) {
            error_log('AI Content Generation Error: ' . $e->getMessage());
            return new WP_Error('generation_error', $e->getMessage());
        }
    }

    /**
     * Generate content using Groq API
     */
    private function generate_content_groq($prompt, $model, $type, $format, $tone, $min_chars, $max_chars) {
        if (empty($this->groq_api_key)) {
            return new WP_Error('no_api_key', __('Groq API key is not configured', 'ai-content-generator'));
        }

        $url = 'https://api.groq.com/openai/v1/chat/completions';
        
        $type_prompt = $this->get_type_prompt($type, $format, $min_chars, $max_chars);
        
        $system_prompt = sprintf(
            "Você é um %s especializado em criar conteúdo para WordPress. " .
            "Escreva em um tom %s, mantendo o conteúdo entre %d e %d caracteres.\n\n" .
            "Use esta estrutura específica:\n%s",
            $format === 'html' ? 'desenvolvedor web profissional' : 'redator profissional',
            $tone,
            $min_chars,
            $max_chars,
            $type_prompt
        );
        
        $body = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => $system_prompt
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->groq_api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($body),
            'timeout' => 60,
            'sslverify' => true
        ));

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $error_message = isset($body['error']['message']) ? $body['error']['message'] : 'Unknown error';
            throw new Exception(sprintf('Groq API returned error %d: %s', $response_code, $error_message));
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['choices'][0]['message']['content'])) {
            throw new Exception('Invalid response from Groq API');
        }

        return $body['choices'][0]['message']['content'];
    }

    /**
     * Generate content using OpenRouter API
     */
    private function generate_content_openrouter($prompt, $model, $type, $format, $tone, $min_chars, $max_chars) {
        if (empty($this->openrouter_api_key)) {
            return new WP_Error('no_api_key', __('OpenRouter API key is not configured', 'ai-content-generator'));
        }

        $url = 'https://openrouter.ai/api/v1/chat/completions';
        
        $type_prompt = $this->get_type_prompt($type, $format, $min_chars, $max_chars);
        
        $system_prompt = sprintf(
            "Você é um %s especializado em criar conteúdo para WordPress. " .
            "Escreva em um tom %s, mantendo o conteúdo entre %d e %d caracteres.\n\n" .
            "Use esta estrutura específica:\n%s",
            $format === 'html' ? 'desenvolvedor web profissional' : 'redator profissional',
            $tone,
            $min_chars,
            $max_chars,
            $type_prompt
        );
        
        $body = array(
            'model' => $model,
            'messages' => array(
                array(
                    'role' => 'system',
                    'content' => $system_prompt
                ),
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->openrouter_api_key,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => get_site_url(),
                'X-Title' => 'My Classic Editor WordPress Plugin'
            ),
            'body' => json_encode($body),
            'timeout' => 60,
            'sslverify' => true
        ));

        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $error_message = isset($body['error']['message']) ? $body['error']['message'] : 'Unknown error';
            throw new Exception(sprintf('OpenRouter API returned error %d: %s', $response_code, $error_message));
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['choices'][0]['message']['content'])) {
            throw new Exception('Invalid response from OpenRouter API');
        }

        return $body['choices'][0]['message']['content'];
    }

    /**
     * Get content type specific prompt
     */
    private function get_type_prompt($type, $format, $min_chars, $max_chars) {
        if ($format === 'html') {
            $prompts = array(
                'post' => "Você é um redator profissional especializado em criar conteúdo para WordPress. Gere um post em HTML seguindo estas instruções:\n\n" .
                    "1. Use tags HTML semânticas apropriadas\n" .
                    "2. Crie um título principal atraente usando <h1>\n" .
                    "3. Divida o conteúdo em seções com subtítulos usando <h2>\n" .
                    "4. Use parágrafos <p> para o texto principal\n" .
                    "5. Utilize listas <ul> ou <ol> quando apropriado\n" .
                    "6. Mantenha o conteúdo entre {$min_chars} e {$max_chars} caracteres\n" .
                    "7. NÃO inclua os marcadores do prompt no texto gerado\n" .
                    "8. NÃO use asteriscos ou outros marcadores de formatação\n\n" .
                    "Gere o conteúdo diretamente, sem mencionar estas instruções.",

                'page' => "Você é um redator profissional especializado em criar páginas para WordPress. Gere uma página em HTML seguindo estas instruções:\n\n" .
                    "1. Use tags HTML semânticas apropriadas\n" .
                    "2. Crie um título principal usando <h1>\n" .
                    "3. Inclua uma introdução clara usando <p>\n" .
                    "4. Divida o conteúdo em seções com <h2>\n" .
                    "5. Use <p> para parágrafos e <ul>/<ol> para listas\n" .
                    "6. Mantenha o conteúdo entre {$min_chars} e {$max_chars} caracteres\n" .
                    "7. NÃO inclua os marcadores do prompt no texto gerado\n" .
                    "8. NÃO use asteriscos ou outros marcadores de formatação\n\n" .
                    "Gere o conteúdo diretamente, sem mencionar estas instruções.",

                'product' => "Você é um redator profissional especializado em descrições de produtos. Gere uma descrição em HTML seguindo estas instruções:\n\n" .
                    "1. Use tags HTML semânticas apropriadas\n" .
                    "2. Crie um título do produto usando <h1>\n" .
                    "3. Adicione um subtítulo atraente com <h2>\n" .
                    "4. Use <p> para a descrição principal\n" .
                    "5. Liste características com <ul>\n" .
                    "6. Inclua especificações técnicas em uma lista <ul>\n" .
                    "7. Mantenha o conteúdo entre {$min_chars} e {$max_chars} caracteres\n" .
                    "8. NÃO inclua os marcadores do prompt no texto gerado\n" .
                    "9. NÃO use asteriscos ou outros marcadores de formatação\n\n" .
                    "Gere o conteúdo diretamente, sem mencionar estas instruções."
            );
        } else {
            $prompts = array(
                'post' => "Você é um redator profissional especializado em criar conteúdo para WordPress. Gere um post seguindo estas instruções:\n\n" .
                    "1. Crie um título principal atraente\n" .
                    "2. Adicione um subtítulo complementar\n" .
                    "3. Escreva uma introdução envolvente\n" .
                    "4. Desenvolva o conteúdo principal com subtópicos\n" .
                    "5. Conclua com uma chamada para ação\n" .
                    "6. Mantenha o conteúdo entre {$min_chars} e {$max_chars} caracteres\n" .
                    "7. NÃO inclua os marcadores do prompt no texto gerado\n" .
                    "8. NÃO use asteriscos ou outros marcadores de formatação\n\n" .
                    "Gere o conteúdo diretamente, sem mencionar estas instruções.",

                'page' => "Você é um redator profissional especializado em criar páginas para WordPress. Gere uma página seguindo estas instruções:\n\n" .
                    "1. Crie um título principal claro\n" .
                    "2. Escreva uma introdução informativa\n" .
                    "3. Desenvolva o conteúdo em seções bem definidas\n" .
                    "4. Use subtítulos para organizar o conteúdo\n" .
                    "5. Conclua com informações relevantes\n" .
                    "6. Mantenha o conteúdo entre {$min_chars} e {$max_chars} caracteres\n" .
                    "7. NÃO inclua os marcadores do prompt no texto gerado\n" .
                    "8. NÃO use asteriscos ou outros marcadores de formatação\n\n" .
                    "Gere o conteúdo diretamente, sem mencionar estas instruções.",

                'product' => "Você é um redator profissional especializado em descrições de produtos. Gere uma descrição seguindo estas instruções:\n\n" .
                    "1. Crie um nome/título atraente para o produto\n" .
                    "2. Adicione uma frase de destaque impactante\n" .
                    "3. Escreva uma descrição geral do produto\n" .
                    "4. Liste os principais benefícios e características\n" .
                    "5. Inclua especificações técnicas relevantes\n" .
                    "6. Mantenha o conteúdo entre {$min_chars} e {$max_chars} caracteres\n" .
                    "7. NÃO inclua os marcadores do prompt no texto gerado\n" .
                    "8. NÃO use asteriscos ou outros marcadores de formatação\n\n" .
                    "Gere o conteúdo diretamente, sem mencionar estas instruções."
            );
        }

        return isset($prompts[$type]) ? $prompts[$type] : $prompts['post'];
    }

    /**
     * AJAX handler for content generation
     */
    public function ajax_generate_content() {
        try {
            check_ajax_referer('aicg_ai_nonce', 'nonce');

            if (!current_user_can('edit_posts')) {
                wp_send_json_error(__('Permission denied', 'ai-content-generator'));
                return;
            }

            $required_fields = array('prompt', 'model', 'type', 'format');
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    wp_send_json_error(sprintf(
                        __('Missing required field: %s', 'ai-content-generator'),
                        $field
                    ));
                    return;
                }
            }

            $prompt = sanitize_textarea_field($_POST['prompt']);
            $model = sanitize_text_field($_POST['model']);
            // Determina o provedor com base no modelo selecionado
            $this->api_provider = strpos($model, ':free') !== false ? 'openrouter' : 'groq';
            
            $type = sanitize_text_field($_POST['type']);
            $format = sanitize_text_field($_POST['format']);
            $tone = sanitize_text_field($_POST['tone']);
            $min_chars = absint($_POST['min_chars']);
            $max_chars = absint($_POST['max_chars']);
            $seo = isset($_POST['seo']) ? (bool)$_POST['seo'] : false;

            // Adiciona instruções de SEO ao prompt se necessário
            if ($seo) {
                $prompt .= "\n\nOtimize o conteúdo para SEO, incluindo meta descrição, palavras-chave relevantes e estrutura adequada de headings.";
            }

            $result = $this->generate_content($prompt, $model, $type, $format, $tone, $min_chars, $max_chars);

            if (is_wp_error($result)) {
                wp_send_json_error($result->get_error_message());
                return;
            }

            wp_send_json_success($result);

        } catch (Exception $e) {
            error_log('Content Generation Error: ' . $e->getMessage());
            wp_send_json_error(sprintf(
                __('Error processing request: %s', 'ai-content-generator'),
                $e->getMessage()
            ));
        }
    }

    /**
     * AJAX handler for getting available models
     */
    public function ajax_get_models() {
        check_ajax_referer('aicg_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Permission denied', 'ai-content-generator'));
            return;
        }

        $provider = isset($_POST['provider']) ? sanitize_text_field($_POST['provider']) : 'openrouter';
        $this->api_provider = $provider;

        wp_send_json_success($this->get_available_models());
    }

    /**
     * AJAX handler for saving API settings
     */
    public function ajax_save_api_settings() {
        check_ajax_referer('aicg_ai_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied', 'ai-content-generator'));
            return;
        }

        $provider = isset($_POST['provider']) ? sanitize_text_field($_POST['provider']) : '';
        $openrouter_key = isset($_POST['openrouter_key']) ? sanitize_text_field($_POST['openrouter_key']) : '';
        $groq_key = isset($_POST['groq_key']) ? sanitize_text_field($_POST['groq_key']) : '';

        update_option('aicg_ai_provider', $provider);
        update_option('aicg_openrouter_api_key', $openrouter_key);
        update_option('aicg_groq_api_key', $groq_key);

        wp_send_json_success();
    }

    /**
     * Enqueue required assets
     */
    public function enqueue_assets($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        wp_enqueue_style(
            'aicg-ai-styles',
            AICG_PLUGIN_URL . 'assets/css/ai-content.css',
            array(),
            AICG_VERSION
        );

        wp_enqueue_script(
            'aicg-ai-script',
            AICG_PLUGIN_URL . 'assets/js/ai-content.js',
            array('jquery'),
            AICG_VERSION,
            true
        );

        wp_localize_script('aicg-ai-script', 'aicgSettings', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aicg_ai_nonce'),
            'models' => $this->get_available_models()
        ));
    }

    /**
     * Add AI button to editor
     */
    public function add_ai_button() {
        global $post;
        if (!$post) return;
        
        echo '<button type="button" id="aicg-ai-button" class="button">';
        echo '<span class="dashicons dashicons-admin-customizer"></span> ';
        echo __('Generate with AI', 'ai-content-generator');
        echo '</button>';
    }
}

// Initialize the class
new AICG_Content(); 