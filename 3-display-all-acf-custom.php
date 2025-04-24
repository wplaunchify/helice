<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Display all ACF custom fields for tournament
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2025-04-24 19:02:11
* @is_valid: 
* @updated_by: 
* @priority: 10
* @run_at: all
* @load_as_file: 
* @condition: {"status":"no","run_if":"assertive","items":[[]]}
*/
?>
<?php if (!defined("ABSPATH")) { return;} // <Internal Doc End> ?>
<?php
/**
 * Display all ACF custom fields for tournament post type
 * For development purposes only
 */
add_action('the_content', 'debug_display_tournament_acf_fields');

function debug_display_tournament_acf_fields($content) {
    // Only run on single tournament posts
    if (!is_singular('tournament') || !function_exists('get_fields')) {
        return $content;
    }
    
    global $post;
    
    // Get all ACF fields for this post
    $fields = get_fields($post->ID);
    
    // If no fields found
    if (!$fields) {
        return $content . '<div style="margin-top:30px;padding:15px;background:#f5f5f5;border:1px solid #ddd;"><p>No ACF fields found for this tournament.</p></div>';
    }
    
    // Start output
    $output = '<div style="margin-top:30px;padding:15px;background:#f5f5f5;border:1px solid #ddd;">';
    $output .= '<h3>Available ACF Fields for Tournament</h3>';
    $output .= '<ul style="list-style:disc;margin-left:20px;">';
    
    // List each field and its value
    foreach ($fields as $key => $value) {
        $output .= '<li><strong>' . esc_html($key) . ':</strong> ';
        
        if (is_array($value) || is_object($value)) {
            $output .= '<em>(Complex value)</em>';
        } else if (empty($value) && $value !== 0) {
            $output .= '<em>(Empty)</em>';
        } else {
            $output .= esc_html(substr((string)$value, 0, 100));
            if (strlen((string)$value) > 100) {
                $output .= '...';
            }
        }
        
        $output .= '</li>';
    }
    
    $output .= '</ul>';
    $output .= '<p><small>Debug view - remove before production</small></p>';
    $output .= '</div>';
    
    // Return original content plus our debug info
    return $content . $output;
}