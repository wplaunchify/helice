<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Display all ACF custom fields for clubs post type
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2025-04-24 19:53:36
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
 * Display ACF custom fields for Club post type
 */
add_action('the_content', 'display_club_custom_fields');

function display_club_custom_fields($content) {
    // Only run on single club posts
    if (!is_singular('club')) {
        return $content;
    }
    
    global $post;
    $post_id = $post->ID;
    
    // Try to get ACF fields if the function exists
    $fields = array();
    if (function_exists('get_fields')) {
        $fields = get_fields($post_id);
    }
    
    // Try post meta as fallback
    if (empty($fields)) {
        $all_meta = get_post_meta($post_id);
        foreach ($all_meta as $key => $values) {
            if (substr($key, 0, 1) !== '_') { // Skip ACF internal fields
                $fields[$key] = $values[0];
            }
        }
    }
    
    // If no fields found
    if (empty($fields)) {
        return $content;
    }
    
    // Start building output
    $output = '<div class="club-details">
        <div class="club-header">
            <h2>Club Information</h2>
        </div>
        <div class="club-info">';
    
    // Show Club ID with C prefix if it exists
    if (isset($fields['club_id'])) {
        $club_id = $fields['club_id'];
        // Add 'C' prefix if it doesn't already have one
        if (substr($club_id, 0, 1) !== 'C') {
            $club_id = 'C' . $club_id;
        }
        
        $output .= '<div class="info-row">
            <span class="info-label">Club ID:</span>
            <span class="info-value">' . esc_html($club_id) . '</span>
        </div>';
    }
    
    // Display other common club fields
    $common_fields = array(
        'club_name' => 'Name',
        'club_location' => 'Location',
        'club_address' => 'Address',
        'club_city' => 'City',
        'club_state' => 'State',
        'club_zip' => 'Zip Code',
        'club_phone' => 'Phone',
        'club_email' => 'Email',
        'club_website' => 'Website',
        'club_contact' => 'Contact Person',
        'club_description' => 'Description'
    );
    
    foreach ($common_fields as $field_key => $field_label) {
        if (isset($fields[$field_key]) && !empty($fields[$field_key])) {
            $value = $fields[$field_key];
            
            // Handle website/URL fields
            if ($field_key === 'club_website' && !empty($value)) {
                // Add http:// if missing
                if (strpos($value, 'http') !== 0) {
                    $value = 'https://' . $value;
                }
                $value = '<a href="' . esc_url($value) . '" target="_blank">' . esc_html($value) . '</a>';
            }
            // Handle email fields
            else if ($field_key === 'club_email' && !empty($value)) {
                $value = '<a href="mailto:' . esc_attr($value) . '">' . esc_html($value) . '</a>';
            }
            // Regular field
            else {
                $value = wp_kses_post($value);
            }
            
            // Special case for description - give it more space
            if ($field_key === 'club_description') {
                $output .= '<div class="club-description">
                    <h3>' . esc_html($field_label) . '</h3>
                    <div class="description-content">' . $value . '</div>
                </div>';
            } else {
                $output .= '<div class="info-row">
                    <span class="info-label">' . esc_html($field_label) . ':</span>
                    <span class="info-value">' . $value . '</span>
                </div>';
            }
        }
    }
    
    // Add any additional club fields that weren't specifically listed
    $shown_fields = array_merge(array('club_id'), array_keys($common_fields));
    foreach ($fields as $key => $value) {
        if (!in_array($key, $shown_fields) && !empty($value)) {
            // Format field label by removing prefix and capitalizing
            $label = str_replace('_', ' ', $key);
            $label = str_replace('club ', '', $label);
            $label = ucwords($label);
            
            // Don't display ACF internal fields
            if (substr($key, 0, 1) === '_') {
                continue;
            }
            
            // Format the value based on what it is
            if (is_array($value)) {
                $value = '<em>(Complex data)</em>';
            } else {
                $value = esc_html($value);
            }
            
            $output .= '<div class="info-row">
                <span class="info-label">' . esc_html($label) . ':</span>
                <span class="info-value">' . $value . '</span>
            </div>';
        }
    }
    
    $output .= '</div></div>'; // Close club-info and club-details
    
    // Add some styling
    $output .= '<style>
        .club-details {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        
        .club-header {
            background: #2c3e50;
            color: white;
            padding: 20px 25px;
        }
        
        .club-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        
        .club-info {
            padding: 25px;
        }
        
        .info-row {
            margin-bottom: 15px;
            display: flex;
            border-bottom: 1px solid #f1f1f1;
            padding-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: #2c3e50;
            width: 150px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #34495e;
            flex-grow: 1;
        }
        
        .info-value a {
            color: #3498db;
            text-decoration: none;
        }
        
        .info-value a:hover {
            text-decoration: underline;
        }
        
        .club-description {
            margin-top: 25px;
        }
        
        .club-description h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 10px;
        }
        
        .description-content {
            line-height: 1.6;
            color: #34495e;
        }
        
        .description-content p {
            margin-bottom: 15px;
        }
        
        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>';
    
    // Return original content plus our club details
    return $content . $output;
}