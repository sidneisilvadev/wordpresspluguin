<?php
/**
 * Shortcodes Class
 *
 * @package AIContentGenerator
 */

if (!defined('WPINC')) {
    die;
}

class AICG_Shortcodes {
    /**
     * Initialize shortcodes
     */
    public static function init() {
        // Register shortcodes
        add_shortcode('aicg_button', array(__CLASS__, 'button_shortcode'));
        add_shortcode('aicg_columns', array(__CLASS__, 'columns_shortcode'));
        add_shortcode('aicg_alert', array(__CLASS__, 'alert_shortcode'));
        add_shortcode('aicg_tooltip', array(__CLASS__, 'tooltip_shortcode'));
        
        // Add TinyMCE buttons
        add_action('init', array(__CLASS__, 'add_shortcode_buttons'));
    }
    
    /**
     * Button shortcode
     */
    public static function button_shortcode($atts, $content = null) {
        $atts = shortcode_atts(array(
            'type' => 'primary',
            'size' => 'medium',
            'url' => '#',
            'target' => '_self'
        ), $atts);
        
        $classes = array('aicg-button');
        $classes[] = 'aicg-button-' . sanitize_html_class($atts['type']);
        $classes[] = 'aicg-button-' . sanitize_html_class($atts['size']);
        
        return sprintf(
            '<a href="%s" class="%s" target="%s">%s</a>',
            esc_url($atts['url']),
            esc_attr(implode(' ', $classes)),
            esc_attr($atts['target']),
            wp_kses_post($content)
        );
    }
    
    /**
     * Columns shortcode
     */
    public static function columns_shortcode($atts, $content = null) {
        $atts = shortcode_atts(array(
            'columns' => '2',
            'gap' => 'normal'
        ), $atts);
        
        $classes = array('aicg-columns');
        $classes[] = 'aicg-columns-' . absint($atts['columns']);
        $classes[] = 'aicg-columns-gap-' . sanitize_html_class($atts['gap']);
        
        return sprintf(
            '<div class="%s">%s</div>',
            esc_attr(implode(' ', $classes)),
            do_shortcode($content)
        );
    }
    
    /**
     * Alert shortcode
     */
    public static function alert_shortcode($atts, $content = null) {
        $atts = shortcode_atts(array(
            'type' => 'info',
            'dismissible' => 'no'
        ), $atts);
        
        $classes = array('aicg-alert');
        $classes[] = 'aicg-alert-' . sanitize_html_class($atts['type']);
        
        if ($atts['dismissible'] === 'yes') {
            $classes[] = 'aicg-alert-dismissible';
            $dismiss_button = '<button type="button" class="aicg-alert-dismiss">&times;</button>';
        } else {
            $dismiss_button = '';
        }
        
        return sprintf(
            '<div class="%s">%s%s</div>',
            esc_attr(implode(' ', $classes)),
            wp_kses_post($content),
            $dismiss_button
        );
    }
    
    /**
     * Tooltip shortcode
     */
    public static function tooltip_shortcode($atts, $content = null) {
        $atts = shortcode_atts(array(
            'text' => '',
            'position' => 'top'
        ), $atts);
        
        $classes = array('aicg-tooltip');
        $classes[] = 'aicg-tooltip-' . sanitize_html_class($atts['position']);
        
        return sprintf(
            '<span class="%s" data-tooltip="%s">%s</span>',
            esc_attr(implode(' ', $classes)),
            esc_attr($atts['text']),
            wp_kses_post($content)
        );
    }
    
    /**
     * Add TinyMCE buttons
     */
    public static function add_shortcode_buttons() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        
        if (get_user_option('rich_editing') !== 'true') {
            return;
        }
        
        add_filter('mce_external_plugins', array(__CLASS__, 'add_shortcode_plugin'));
        add_filter('mce_buttons', array(__CLASS__, 'register_shortcode_buttons'));
    }
    
    /**
     * Register TinyMCE plugin for shortcodes
     */
    public static function add_shortcode_plugin($plugin_array) {
        $plugin_array['aicg_shortcodes'] = AICG_PLUGIN_URL . 'assets/js/shortcodes.js';
        return $plugin_array;
    }
    
    /**
     * Register shortcode buttons
     */
    public static function register_shortcode_buttons($buttons) {
        array_push($buttons, 'aicg_button', 'aicg_columns', 'aicg_alert', 'aicg_tooltip');
        return $buttons;
    }
}

// Initialize shortcodes
AICG_Shortcodes::init(); 