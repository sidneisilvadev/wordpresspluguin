<?php
/**
 * Classe para gerenciar templates de conteúdo
 */
class MCE_Templates {
    /**
     * Inicializa os templates
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_templates_page'));
        add_action('admin_init', array(__CLASS__, 'register_template_settings'));
        add_action('media_buttons', array(__CLASS__, 'add_template_button'));
        add_action('admin_footer', array(__CLASS__, 'add_template_modal'));
    }

    /**
     * Adiciona página de templates
     */
    public static function add_templates_page() {
        add_submenu_page(
            'options-general.php',
            __('Content Templates', 'my-classic-editor'),
            __('Content Templates', 'my-classic-editor'),
            'manage_options',
            'mce-templates',
            array(__CLASS__, 'render_templates_page')
        );
    }

    /**
     * Renderiza página de templates
     */
    public static function render_templates_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Content Templates', 'my-classic-editor'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('mce_templates');
                do_settings_sections('mce-templates');
                submit_button(__('Save Templates', 'my-classic-editor'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Registra configurações de templates
     */
    public static function register_template_settings() {
        register_setting('mce_templates', 'mce_content_templates', array(
            'type' => 'array',
            'sanitize_callback' => array(__CLASS__, 'sanitize_templates')
        ));

        add_settings_section(
            'mce_templates_section',
            __('Manage Templates', 'my-classic-editor'),
            array(__CLASS__, 'templates_section_cb'),
            'mce-templates'
        );

        // Templates predefinidos
        $default_templates = array(
            'blog-post' => array(
                'name' => __('Blog Post Template', 'my-classic-editor'),
                'content' => "<h2>Introduction</h2>\n<p>Your opening paragraph here...</p>\n\n<h2>Main Points</h2>\n<ul>\n<li>Key point 1</li>\n<li>Key point 2</li>\n<li>Key point 3</li>\n</ul>\n\n<h2>Detailed Discussion</h2>\n<p>Expand on your main points here...</p>\n\n<h2>Conclusion</h2>\n<p>Summarize your main points and call to action...</p>\n\n<p><em>Featured Image: Add an engaging image here</em></p>"
            ),
            'product-page' => array(
                'name' => __('Product Page Template', 'my-classic-editor'),
                'content' => "<h1>Product Name</h1>\n\n<div class='product-intro'>\n<p><strong>Quick Overview:</strong> Brief product description...</p>\n</div>\n\n<h2>Key Features</h2>\n<ul>\n<li>Feature 1</li>\n<li>Feature 2</li>\n<li>Feature 3</li>\n</ul>\n\n<h2>Product Details</h2>\n<p>Detailed description of the product...</p>\n\n<h2>Specifications</h2>\n<table>\n<tr><th>Spec</th><th>Detail</th></tr>\n<tr><td>Size</td><td>Enter size</td></tr>\n<tr><td>Weight</td><td>Enter weight</td></tr>\n</table>\n\n<h2>How to Use</h2>\n<ol>\n<li>Step 1</li>\n<li>Step 2</li>\n<li>Step 3</li>\n</ol>"
            ),
            'event-page' => array(
                'name' => __('Event Page Template', 'my-classic-editor'),
                'content' => "<h1>Event Name</h1>\n\n<p class='event-date'><strong>Date:</strong> [Enter Date]</p>\n<p class='event-time'><strong>Time:</strong> [Enter Time]</p>\n<p class='event-location'><strong>Location:</strong> [Enter Location]</p>\n\n<h2>About This Event</h2>\n<p>Event description here...</p>\n\n<h2>Schedule</h2>\n<ul>\n<li>Time - Activity 1</li>\n<li>Time - Activity 2</li>\n<li>Time - Activity 3</li>\n</ul>\n\n<h2>What to Bring</h2>\n<ul>\n<li>Item 1</li>\n<li>Item 2</li>\n<li>Item 3</li>\n</ul>\n\n<h2>Registration</h2>\n<p>Registration details here...</p>"
            ),
            'faq-page' => array(
                'name' => __('FAQ Page Template', 'my-classic-editor'),
                'content' => "<h1>Frequently Asked Questions</h1>\n\n<div class='faq-item'>\n<h3>Question 1?</h3>\n<p>Answer to question 1...</p>\n</div>\n\n<div class='faq-item'>\n<h3>Question 2?</h3>\n<p>Answer to question 2...</p>\n</div>\n\n<div class='faq-item'>\n<h3>Question 3?</h3>\n<p>Answer to question 3...</p>\n</div>"
            )
        );

        // Adiciona templates ao banco de dados se não existirem
        if (!get_option('mce_content_templates')) {
            update_option('mce_content_templates', $default_templates);
        }
    }

    /**
     * Callback da seção de templates
     */
    public static function templates_section_cb() {
        $templates = get_option('mce_content_templates', array());
        foreach ($templates as $id => $template) {
            ?>
            <div class="template-item">
                <h3><?php echo esc_html($template['name']); ?></h3>
                <textarea name="mce_content_templates[<?php echo esc_attr($id); ?>][content]" rows="10" cols="50" class="large-text"><?php echo esc_textarea($template['content']); ?></textarea>
                <input type="hidden" name="mce_content_templates[<?php echo esc_attr($id); ?>][name]" value="<?php echo esc_attr($template['name']); ?>">
            </div>
            <?php
        }
    }

    /**
     * Sanitiza os templates
     */
    public static function sanitize_templates($templates) {
        if (!is_array($templates)) {
            return array();
        }

        foreach ($templates as $id => $template) {
            $templates[$id]['name'] = sanitize_text_field($template['name']);
            $templates[$id]['content'] = wp_kses_post($template['content']);
        }

        return $templates;
    }

    /**
     * Adiciona botão de template ao editor
     */
    public static function add_template_button() {
        echo '<a href="#" id="insert-template-button" class="button"><span class="dashicons dashicons-layout"></span> ' . __('Insert Template', 'my-classic-editor') . '</a>';
    }

    /**
     * Adiciona modal de templates
     */
    public static function add_template_modal() {
        $screen = get_current_screen();
        if (!in_array($screen->base, array('post', 'page'))) {
            return;
        }

        $templates = get_option('mce_content_templates', array());
        ?>
        <div id="template-modal" style="display:none;">
            <div class="template-modal-content">
                <h2><?php _e('Choose a Template', 'my-classic-editor'); ?></h2>
                <div class="template-list">
                    <?php foreach ($templates as $id => $template) : ?>
                        <div class="template-choice" data-template="<?php echo esc_attr($id); ?>">
                            <h3><?php echo esc_html($template['name']); ?></h3>
                            <button class="button insert-template" data-template="<?php echo esc_attr($id); ?>">
                                <?php _e('Insert', 'my-classic-editor'); ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <style>
            #template-modal {
                position: fixed;
                z-index: 100000;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.7);
            }
            .template-modal-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: #fff;
                padding: 20px;
                border-radius: 5px;
                max-width: 500px;
                width: 90%;
            }
            .template-list {
                max-height: 400px;
                overflow-y: auto;
            }
            .template-choice {
                padding: 10px;
                border-bottom: 1px solid #ddd;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .template-choice:last-child {
                border-bottom: none;
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            var templates = <?php echo json_encode($templates); ?>;
            
            $('#insert-template-button').click(function(e) {
                e.preventDefault();
                $('#template-modal').show();
            });

            $('#template-modal').click(function(e) {
                if (e.target === this) {
                    $(this).hide();
                }
            });

            $('.insert-template').click(function() {
                var templateId = $(this).data('template');
                var content = templates[templateId].content;
                
                if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                    tinymce.activeEditor.setContent(content);
                } else {
                    $('#content').val(content);
                }
                
                $('#template-modal').hide();
            });
        });
        </script>
        <?php
    }
}

// Inicializa a classe
MCE_Templates::init(); 