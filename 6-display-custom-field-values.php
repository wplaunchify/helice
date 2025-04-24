<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Display custom field values for Race post type
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2025-04-24 19:41:44
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
 * Display custom field values for Race post type
 */
add_action('the_content', 'display_race_custom_fields');

function display_race_custom_fields($content) {
    // Only run on single race posts
    if (!is_singular('race')) {
        return $content;
    }
    
    global $post;
    $post_id = $post->ID;
    
    // Get the field values - using both get_post_meta and get_field (ACF)
    $fields = array();
    
    // Common race fields - adjust these to match your actual field names
    $possible_fields = array(
        'race_date' => 'Race Date',
        'race_type' => 'Race Type',
        'race_location' => 'Location',
        'race_distance' => 'Distance',
        'race_category' => 'Category',
        'race_status' => 'Status',
        'race_organizer' => 'Organizer',
        'race_fee' => 'Entry Fee',
        'registration_deadline' => 'Registration Deadline',
        'max_participants' => 'Maximum Participants',
        'current_participants' => 'Current Participants',
        'race_description' => 'Description',
        'race_rules' => 'Rules',
    );
    
    // Get all values
    foreach ($possible_fields as $field_key => $field_label) {
        // Try ACF first if available
        if (function_exists('get_field')) {
            $value = get_field($field_key, $post_id);
            if (!empty($value)) {
                $fields[$field_label] = $value;
                continue;
            }
        }
        
        // Fallback to regular post meta
        $value = get_post_meta($post_id, $field_key, true);
        if (!empty($value)) {
            $fields[$field_label] = $value;
        }
    }
    
    // If no fields found
    if (empty($fields)) {
        return $content;
    }
    
    // Start building stylish output
    $output = '<div class="race-details-container">
        <div class="race-details">
            <div class="race-header">
                <h2>Race Details</h2>
            </div>
            <div class="race-info-grid">';
    
    // Display main details
    $main_fields = array('Race Date', 'Race Type', 'Location', 'Distance', 'Category', 'Status');
    foreach ($main_fields as $label) {
        if (isset($fields[$label])) {
            $value = $fields[$label];
            
            // Format date if it's a date field
            if ($label == 'Race Date' && strtotime($value)) {
                $value = date('F j, Y', strtotime($value));
            }
            
            // Format deadline if it's a date
            if ($label == 'Registration Deadline' && strtotime($value)) {
                $value = date('F j, Y', strtotime($value));
            }
            
            // Format values for display
            if (is_array($value) && isset($value['label'])) {
                // Handle select/radio ACF fields
                $value = $value['label'];
            } else if (is_array($value)) {
                // Handle multi-select/checkbox fields
                $value = implode(', ', $value);
            }
            
            $output .= '<div class="race-info-item">
                <div class="info-label">' . esc_html($label) . '</div>
                <div class="info-value">' . esc_html($value) . '</div>
            </div>';
        }
    }
    
    $output .= '</div>'; // Close race-info-grid
    
    // Registration info section
    $reg_fields = array('Entry Fee', 'Registration Deadline', 'Maximum Participants', 'Current Participants');
    $has_reg_fields = false;
    
    foreach ($reg_fields as $label) {
        if (isset($fields[$label])) {
            $has_reg_fields = true;
            break;
        }
    }
    
    if ($has_reg_fields) {
        $output .= '<div class="race-section">
            <h3>Registration Information</h3>
            <div class="race-info-grid">';
        
        foreach ($reg_fields as $label) {
            if (isset($fields[$label])) {
                $value = $fields[$label];
                
                // Format fee if needed
                if ($label == 'Entry Fee' && is_numeric($value)) {
                    $value = '$' . number_format($value, 2);
                }
                
                // Format date if it's a date field
                if ($label == 'Registration Deadline' && strtotime($value)) {
                    $value = date('F j, Y', strtotime($value));
                }
                
                $output .= '<div class="race-info-item">
                    <div class="info-label">' . esc_html($label) . '</div>
                    <div class="info-value">' . esc_html($value) . '</div>
                </div>';
            }
        }
        
        $output .= '</div></div>'; // Close race-info-grid and race-section
    }
    
    // Details section for longer text
    $details_fields = array('Description', 'Rules');
    $has_details = false;
    
    foreach ($details_fields as $label) {
        if (isset($fields['Race ' . $label]) && !empty($fields['Race ' . $label])) {
            $has_details = true;
            break;
        }
    }
    
    if ($has_details) {
        foreach ($details_fields as $label) {
            $full_label = 'Race ' . $label;
            if (isset($fields[$full_label]) && !empty($fields[$full_label])) {
                $output .= '<div class="race-section">
                    <h3>' . esc_html($label) . '</h3>
                    <div class="race-text-content">' . 
                        wp_kses_post($fields[$full_label]) . 
                    '</div>
                </div>';
            }
        }
    }
    
    // Display any other fields not already shown
    $other_fields = array_diff(array_keys($fields), 
                              array_merge($main_fields, $reg_fields, 
                                         array_map(function($item) { return 'Race ' . $item; }, $details_fields)));
    
    if (!empty($other_fields)) {
        $output .= '<div class="race-section">
            <h3>Additional Information</h3>
            <div class="race-info-grid">';
        
        foreach ($other_fields as $label) {
            $value = $fields[$label];
            
            // Format the value based on type
            if (is_array($value)) {
                if (isset($value['label'])) {
                    $value = $value['label']; 
                } else {
                    $value = implode(', ', $value);
                }
            }
            
            $output .= '<div class="race-info-item">
                <div class="info-label">' . esc_html($label) . '</div>
                <div class="info-value">' . esc_html($value) . '</div>
            </div>';
        }
        
        $output .= '</div></div>'; // Close race-info-grid and race-section
    }
    
    $output .= '</div></div>'; // Close race-details and race-details-container
    
    // Add the styling
    $output .= '<style>
        .race-details-container {
            max-width: 950px;
            margin: 40px auto;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        
        .race-details {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .race-header {
            background: #3498db;
            color: white;
            padding: 25px 30px;
        }
        
        .race-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        
        .race-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 30px;
        }
        
        .race-info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 6px;
            font-weight: 600;
        }
        
        .info-value {
            font-size: 18px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .race-section {
            padding: 0 30px 30px;
            border-top: 1px solid #ecf0f1;
            margin-top: 10px;
        }
        
        .race-section h3 {
            font-size: 20px;
            color: #2c3e50;
            font-weight: 600;
            margin: 25px 0 15px;
        }
        
        .race-text-content {
            color: #34495e;
            line-height: 1.6;
        }
        
        .race-text-content p {
            margin-bottom: 15px;
        }
        
        @media (max-width: 768px) {
            .race-info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>';
    
    // Return original content plus our race details
    return $content . $output;
}