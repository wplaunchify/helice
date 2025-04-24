<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Tournament Target Scoring Fields (for ACF Field Group Propagation)
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2025-04-24 20:45:49
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
 * Title: Tournament Target Scoring Fields
 * Description: Creates 30 target hit/miss fields with user, club, and tournament relationships
 * Category: acf
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Register ACF fields when ACF initializes
add_action('acf/init', 'tournament_register_scoring_fields');

function tournament_register_scoring_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    
    // Create an array to hold our fields
    $fields = [];
    
    // Add Score ID field
    $fields[] = [
        'key' => 'field_score_id',
        'label' => 'Score ID',
        'name' => 'score_id',
        'type' => 'text',
        'instructions' => 'Auto-generated unique ID',
        'required' => 0,
        'readonly' => 1,
        'wrapper' => [
            'width' => '20',
            'class' => 'score-id-field',
            'id' => '',
        ],
    ];
    
    // Add User (Shooter) relationship field
    $fields[] = [
        'key' => 'field_shooter',
        'label' => 'Shooter',
        'name' => 'shooter',
        'type' => 'user',
        'instructions' => 'Select the user who is shooting',
        'required' => 1,
        'role' => '', // Leave empty to allow all roles
        'multiple' => 0,
        'allow_null' => 0,
        'wrapper' => [
            'width' => '30',
            'class' => 'shooter-field',
            'id' => '',
        ],
    ];
    
    // Add Tournament relationship field
    $fields[] = [
        'key' => 'field_tournament',
        'label' => 'Tournament',
        'name' => 'tournament',
        'type' => 'post_object',
        'instructions' => 'Select the tournament this score belongs to',
        'required' => 1,
        'post_type' => ['tournament'], // Change this if your tournament post type has a different name
        'multiple' => 0,
        'allow_null' => 0,
        'return_format' => 'id',
        'wrapper' => [
            'width' => '25',
            'class' => 'tournament-field',
            'id' => '',
        ],
    ];
    
    // Add Crank field
    $fields[] = [
        'key' => 'field_crank',
        'label' => 'Crank',
        'name' => 'crank',
        'type' => 'text',
        'instructions' => 'Tournament crank (optional)',
        'required' => 0,
        'wrapper' => [
            'width' => '25',
            'class' => 'crank-field',
            'id' => '',
        ],
    ];
    
    // Add Club relationship field
    $fields[] = [
        'key' => 'field_club',
        'label' => 'Club',
        'name' => 'club',
        'type' => 'post_object',
        'instructions' => 'Select the club where shooting took place',
        'required' => 1,
        'post_type' => ['club'], // Change this if your club post type has a different name
        'multiple' => 0,
        'allow_null' => 0,
        'return_format' => 'id',
        'wrapper' => [
            'width' => '30',
            'class' => 'club-field',
            'id' => '',
        ],
    ];
    
    // Add Date field
    $fields[] = [
        'key' => 'field_score_date',
        'label' => 'Date',
        'name' => 'score_date',
        'type' => 'date_picker',
        'instructions' => 'Date when shooting took place',
        'required' => 1,
        'display_format' => 'm/d/Y',
        'return_format' => 'Y-m-d',
        'wrapper' => [
            'width' => '30',
            'class' => 'date-field',
            'id' => '',
        ],
    ];
    
    // Add Total Score field
    $fields[] = [
        'key' => 'field_total_score',
        'label' => 'Total Score',
        'name' => 'total_score',
        'type' => 'number',
        'instructions' => 'Auto-calculated total',
        'required' => 0,
        'readonly' => 1,
        'default_value' => 0,
        'wrapper' => [
            'width' => '20',
            'class' => 'total-score-field',
            'id' => '',
        ],
    ];
    
    // Add a message field for a separator
    $fields[] = [
        'key' => 'field_separator',
        'label' => 'Targets',
        'name' => '',
        'type' => 'message',
        'message' => 'Mark hits and misses for all 30 targets below',
        'new_lines' => 'wpautop',
        'wrapper' => [
            'width' => '100',
            'class' => 'targets-header',
            'id' => '',
        ],
    ];
    
    // Generate all 30 target fields
    for ($i = 1; $i <= 30; $i++) {
        $fields[] = [
            'key' => 'field_target_' . $i,
            'label' => 'Target ' . $i,
            'name' => 'target_' . $i,
            'type' => 'true_false',
            'instructions' => '',
            'required' => 0,
            'ui' => 1,
            'ui_on_text' => 'Hit',
            'ui_off_text' => 'Miss',
            'wrapper' => [
                'width' => '10',
                'class' => 'target-field',
                'id' => '',
            ],
        ];
    }
    
    // Register the entire field group
    acf_add_local_field_group([
        'key' => 'group_tournament_scores',
        'title' => 'Tournament Scores',
        'fields' => $fields,
        'location' => [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'score',
                ],
            ],
        ],
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => [
            'the_content',
            'excerpt',
            'discussion',
            'comments',
            'revisions',
            'slug',
            'author',
            'format',
            'featured_image',
            'categories',
            'tags',
            'send-trackbacks',
        ],
    ]);
}

// Disable Gutenberg editor for Score post type
add_filter('use_block_editor_for_post_type', 'tournament_disable_gutenberg_for_scores', 10, 2);

function tournament_disable_gutenberg_for_scores($use_block_editor, $post_type) {
    if ($post_type === 'score') {
        return false; // Disable Gutenberg for Score post type
    }
    return $use_block_editor;
}

// Hide title field in classic editor
add_action('admin_head', 'tournament_hide_title_field_for_scores');

function tournament_hide_title_field_for_scores() {
    global $post, $pagenow, $typenow;
    
    if (!is_admin() || ($pagenow != 'post.php' && $pagenow != 'post-new.php')) {
        return;
    }
    
    if ($typenow != 'score') {
        return;
    }
    
    echo '<style>
        /* Hide the title field in the classic editor */
        #titlediv {
            display: none !important;
        }
        
        /* Style improvements for the score editor */
        .acf-fields {
            background: #f9f9f9;
            border-radius: 5px;
            padding: 15px !important;
        }
        
        .acf-field {
            border-top: none !important;
        }
        
        /* Style the header fields */
        .score-id-field, .shooter-field, .tournament-field, .club-field, .crank-field, .date-field, .total-score-field {
            background: #fff;
            padding: 15px !important;
            margin-bottom: 15px !important;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .acf-field[data-name="score_id"] .acf-label label,
        .acf-field[data-name="total_score"] .acf-label label {
            font-weight: bold;
            color: #0073aa;
        }
        
        /* Style the separator */
        .targets-header {
            background: #0073aa;
            color: white;
            border-radius: 4px;
            padding: 10px 15px !important;
            margin: 20px 0 15px !important;
        }
        
        .targets-header .acf-label label {
            color: white;
            font-size: 16px;
            font-weight: bold;
        }
        
        /* Make targets appear in clear rows of 10 with better styling */
        .acf-fields > .acf-field[data-name^="target_"] {
            background: white;
            border: 1px solid #eee !important;
            border-radius: 3px;
            padding: 10px 8px !important;
            margin: 5px !important;
            transition: all 0.2s ease;
        }
        
        .acf-fields > .acf-field[data-name^="target_"]:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Add visual separation between rows of 10 */
        .acf-fields > .acf-field[data-name="target_10"],
        .acf-fields > .acf-field[data-name="target_20"] {
            margin-bottom: 20px !important;
        }
        
        /* Add dividers between rows */
        .acf-fields > .acf-field[data-name="target_10"] + .acf-field,
        .acf-fields > .acf-field[data-name="target_20"] + .acf-field {
            clear: both;
            position: relative;
        }
        
        .acf-fields > .acf-field[data-name="target_10"] + .acf-field::before,
        .acf-fields > .acf-field[data-name="target_20"] + .acf-field::before {
            content: "";
            display: block;
            width: calc(100% - 20px);
            height: 1px;
            background: #ddd;
            position: absolute;
            top: -15px;
            left: 10px;
        }
        
        /* Make the target fields look nicer */
        .acf-field[data-name^="target_"] .acf-label label {
            font-size: 13px;
            font-weight: normal;
        }
        
        /* Center the hit/miss toggle */
        .acf-field[data-name^="target_"] .acf-input {
            display: flex;
            justify-content: center;
        }
        
        /* Style the toggle switch */
        .acf-switch {
            border-color: #ccc !important;
        }
        
        .acf-switch.-on {
            background: #4CAF50 !important;
            border-color: #4CAF50 !important;
        }
        
        .acf-switch.-on .acf-switch-slider {
            border-color: #4CAF50 !important;
        }
        
        /* Make room at top for publish box */
        #poststuff {
            padding-top: 20px;
        }
    </style>';
}

// Set up auto-incrementing score ID starting at 1000
add_action('wp_insert_post', 'tournament_set_score_post_title', 10, 3);

function tournament_set_score_post_title($post_id, $post, $update) {
    // Only run for new score posts
    if ($post->post_type != 'score') {
        return;
    }
    
    // Skip if this is an update and the title is already a number >= 1000
    if ($update && is_numeric($post->post_title) && intval($post->post_title) >= 1000) {
        return;
    }
    
    // Get the current highest score ID
    $args = array(
        'post_type' => 'score',
        'posts_per_page' => 1,
        'orderby' => 'title',
        'order' => 'DESC',
        'post_status' => array('publish', 'draft', 'trash', 'pending', 'future', 'private'),
        'meta_query' => array(
            array(
                'key' => '_wp_trash_meta_status',
                'compare' => 'NOT EXISTS'
            )
        )
    );
    
    $latest_score = get_posts($args);
    
    if (!empty($latest_score) && is_numeric($latest_score[0]->post_title)) {
        $latest_id = intval($latest_score[0]->post_title);
        $next_id = max(1000, $latest_id + 1);
    } else {
        $next_id = 1000; // Starting ID
    }
    
    // Update the post title to the new ID
    wp_update_post(array(
        'ID' => $post_id,
        'post_title' => (string) $next_id,
    ));
    
    // Also set the score_id field value
    update_field('score_id', $next_id, $post_id);
}

// Add JavaScript to display the post title in the Score ID field and calculate total
add_action('acf/input/admin_footer', 'tournament_score_calculation_script');

function tournament_score_calculation_script() {
    // Only add the script on the score post type edit screens
    global $post;
    if (!$post || 'score' !== $post->post_type) {
        return;
    }
    ?>
    <script type="text/javascript">
    (function($) {
        if (typeof acf === 'undefined') return;
        
        acf.addAction('ready', function() {
            // Make sure the Score ID field shows the post title
            var postTitle = "<?php echo esc_js($post->post_title); ?>";
            $('input[name="acf[score_id]"]').val(postTitle);
            
            // Update total on load and when any true/false field changes
            calculateTotal();
            
            $('input[name^="acf[target_"]').on('change', function() {
                calculateTotal();
                
                // Add visual feedback when toggling targets
                var $field = $(this).closest('.acf-field');
                $field.css('transition', 'background-color 0.3s');
                
                if ($(this).prop('checked')) {
                    $field.css('background-color', '#e8f5e9');
                    setTimeout(function() {
                        $field.css('background-color', '#fff');
                    }, 500);
                } else {
                    $field.css('background-color', '#ffebee');
                    setTimeout(function() {
                        $field.css('background-color', '#fff');
                    }, 500);
                }
            });
            
            function calculateTotal() {
                var total = 0;
                
                // Count all checked boxes
                for (var i = 1; i <= 30; i++) {
                    if ($('input[name="acf[target_' + i + ']"]').prop('checked')) {
                        total++;
                    }
                }
                
                // Update total field
                $('input[name="acf[total_score]"]').val(total);
            }
        });
    })(jQuery);
    </script>
    <?php
}

// Register shortcode to display score information
add_shortcode('display_score', 'tournament_display_score_shortcode');

function tournament_display_score_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts, 'display_score');
    
    if (empty($atts['id'])) {
        return '<p>Error: Score ID is required.</p>';
    }
    
    $score_posts = get_posts(array(
        'post_type' => 'score',
        'posts_per_page' => 1,
        'title' => $atts['id'],
        'post_status' => 'publish',
    ));
    
    if (empty($score_posts)) {
        return '<p>Error: Score not found.</p>';
    }
    
    $score_id = $score_posts[0]->ID;
    
    // Get score details
    $score_number = get_the_title($score_id);
    $shooter_id = get_field('shooter', $score_id);
    $shooter_name = '';
    if ($shooter_id) {
        $user_data = get_userdata($shooter_id);
        if ($user_data) {
            $shooter_name = $user_data->display_name;
        }
    }
    
    $tournament_id = get_field('tournament', $score_id);
    $tournament_name = $tournament_id ? get_the_title($tournament_id) : '';
    
    $club_id = get_field('club', $score_id);
    $club_name = $club_id ? get_the_title($club_id) : '';
    
    $crank = get_field('crank', $score_id);
    $score_date = get_field('score_date', $score_id);
    $total_score = get_field('total_score', $score_id);
    
    // Get all targets
    $targets = array();
    for ($i = 1; $i <= 30; $i++) {
        $targets[$i] = get_field('target_' . $i, $score_id) ? true : false;
    }
    
    // Build HTML output
    $output = '<div class="tournament-score-display">';
    
    // Score header
    $output .= '<div class="score-header">';
    $output .= '<h2>Score #' . esc_html($score_number) . '</h2>';
    $output .= '<div class="score-meta">';
    $output .= '<p><strong>Shooter:</strong> ' . esc_html($shooter_name) . '</p>';
    $output .= '<p><strong>Tournament:</strong> ' . esc_html($tournament_name) . '</p>';
    $output .= '<p><strong>Club:</strong> ' . esc_html($club_name) . '</p>';
    if (!empty($crank)) {
        $output .= '<p><strong>Crank:</strong> ' . esc_html($crank) . '</p>';
    }
    $output .= '<p><strong>Date:</strong> ' . esc_html($score_date) . '</p>';
    $output .= '<p class="total-score"><strong>Total Score:</strong> <span>' . esc_html($total_score) . '/30</span></p>';
    $output .= '</div>'; // .score-meta
    $output .= '</div>'; // .score-header
    
    // Target grid
    $output .= '<div class="target-grid">';
    for ($i = 1; $i <= 30; $i++) {
        $status_class = $targets[$i] ? 'hit' : 'miss';
        $status_text = $targets[$i] ? 'Hit' : 'Miss';
        
        $output .= '<div class="target-box ' . $status_class . '">';
        $output .= '<span class="target-number">' . $i . '</span>';
        $output .= '<span class="target-status">' . $status_text . '</span>';
        $output .= '</div>';
        
        // Add row break after every 10 targets
        if ($i % 10 === 0 && $i < 30) {
            $output .= '<div class="target-row-break"></div>';
        }
    }
    $output .= '</div>'; // .target-grid
    
    $output .= '</div>'; // .tournament-score-display
    
    // Add CSS for the display
    $output .= '
    <style>
        .tournament-score-display {
            max-width: 900px;
            margin: 0 auto;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .score-header {
            border-bottom: 2px solid #0073aa;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .score-header h2 {
            color: #0073aa;
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .score-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .score-meta p {
            margin: 0;
            flex: 1 1 200px;
        }
        
        .total-score {
            font-size: 1.2em;
        }
        
        .total-score span {
            color: #0073aa;
            font-weight: bold;
        }
        
        .target-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        
        .target-box {
            width: 60px;
            height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            border-radius: 4px;
            position: relative;
            border: 1px solid #ddd;
            background: white;
        }
        
        .target-box.hit {
            background: #e8f5e9;
            border-color: #4CAF50;
        }
        
        .target-box.miss {
            background: #ffebee;
            border-color: #f44336;
        }
        
        .target-number {
            font-weight: bold;
            font-size: 16px;
        }
        
        .target-status {
            font-size: 12px;
            margin-top: 5px;
        }
        
        .target-box.hit .target-status {
            color: #2E7D32;
        }
        
        .target-box.miss .target-status {
            color: #C62828;
        }
        
        .target-row-break {
            flex-basis: 100%;
            height: 1px;
            background: #ddd;
            margin: 10px 0;
        }
    </style>
    ';
    
    return $output;
}

// Add custom admin columns for Score list view
add_filter('manage_score_posts_columns', 'tournament_set_custom_score_columns');
function tournament_set_custom_score_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = 'Score ID';
    $new_columns['shooter'] = 'Shooter';
    $new_columns['tournament'] = 'Tournament';
    $new_columns['club'] = 'Club';
    $new_columns['crank'] = 'Crank';
    $new_columns['score'] = 'Score';
    $new_columns['score_date'] = 'Date';
    return $new_columns;
}

// Populate custom admin columns
add_action('manage_score_posts_custom_column', 'tournament_custom_score_column_content', 10, 2);
function tournament_custom_score_column_content($column, $post_id) {
    switch ($column) {
        case 'shooter':
            $user_id = get_field('shooter', $post_id);
            if ($user_id) {
                $user = get_userdata($user_id);
                if ($user) {
                    echo esc_html($user->display_name);
                }
            }
            break;
        case 'tournament':
            $tournament_id = get_field('tournament', $post_id);
            if ($tournament_id) {
                echo esc_html(get_the_title($tournament_id));
            }
            break;
        case 'club':
            $club_id = get_field('club', $post_id);
            if ($club_id) {
                echo esc_html(get_the_title($club_id));
            }
            break;
        case 'crank':
            $crank = get_field('crank', $post_id);
            echo $crank ? esc_html($crank) : '—';
            break;
        case 'score':
            $score = get_field('total_score', $post_id);
            echo '<span style="font-weight:bold;' . ($score > 15 ? 'color:#2E7D32;' : '') . '">';
            echo $score !== '' ? esc_html($score . '/30') : '0/30';
            echo '</span>';
            break;
        case 'score_date':
            $date = get_field('score_date', $post_id);
            echo $date ? esc_html($date) : '—';
            break;
    }
}

// Make the custom columns sortable
add_filter('manage_edit-score_sortable_columns', 'tournament_make_score_columns_sortable');
function tournament_make_score_columns_sortable($columns) {
    $columns['shooter'] = 'shooter';
    $columns['tournament'] = 'tournament';
    $columns['club'] = 'club';
    $columns['crank'] = 'crank';
    $columns['score'] = 'total_score';
    $columns['score_date'] = 'score_date';
    return $columns;
}

// Handle sorting by custom columns
add_action('pre_get_posts', 'tournament_score_custom_orderby');
function tournament_score_custom_orderby($query) {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'score') {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    $meta_keys = array(
        'shooter' => 'shooter',
        'tournament' => 'tournament',
        'club' => 'club',
        'crank' => 'crank',
        'total_score' => 'total_score',
        'score_date' => 'score_date'
    );
    
    if (array_key_exists($orderby, $meta_keys)) {
        $query->set('meta_key', $meta_keys[$orderby]);
        $query->set('orderby', 'meta_value');
        
        // Use numeric ordering for score
        if ($orderby === 'total_score') {
            $query->set('orderby', 'meta_value_num');
        }
        
        // Use date ordering for score_date
        if ($orderby === 'score_date') {
            $query->set('orderby', 'meta_value');
            $query->set('meta_type', 'DATE');
        }
    }
}