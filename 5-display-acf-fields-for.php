<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Display ACF fields for the Score post type on front end
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2025-04-24 20:47:22
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
 * Display enhanced score information with proper ACF field references
 */
add_action('the_content', 'display_enhanced_score_details');

function display_enhanced_score_details($content) {
    // Only run on single score posts
    if (!is_singular('score')) {
        return $content;
    }
    
    global $post;
    $post_id = $post->ID;
    
    // Check if ACF functions exist
    if (!function_exists('get_field')) {
        return $content;
    }
    
    // Get the actual ACF field values
    $score_id = get_field('score_id', $post_id);
    if (empty($score_id)) {
        $score_id = $post_id; // Fallback to post ID
    }
    
    // Get total score
    $total_score = get_field('total_score', $post_id);
    if ($total_score === null || $total_score === '') {
        $total_score = 0;
    }
    
    // Get shooter (user) data
    $user_id = get_field('shooter', $post_id);
    $user_name = 'Unknown';
    if ($user_id) {
        $user_data = get_userdata($user_id);
        if ($user_data) {
            $user_name = $user_data->display_name;
        }
    }
    
    // Get club data
    $club_id = get_field('club', $post_id);
    $club_name = '';
    if ($club_id && is_numeric($club_id)) {
        $club_name = get_the_title($club_id);
        $club_acf_id = get_field('club_id', $club_id);
        if (!empty($club_acf_id)) {
            if (substr($club_acf_id, 0, 1) !== 'C') {
                $club_acf_id = 'C' . $club_acf_id;
            }
            $club_name .= ' (' . $club_acf_id . ')';
        }
    }
    
    // Get tournament data
    $tournament_id = get_field('tournament', $post_id);
    $tournament_name = '';
    if ($tournament_id && is_numeric($tournament_id)) {
        $tournament_name = get_the_title($tournament_id);
    }
    
    // Get round/flight
    $round = get_field('round', $post_id);
    
    // Get score date
    $score_date = get_field('score_date', $post_id);
    $formatted_date = '';
    if (!empty($score_date)) {
        $formatted_date = date('F j, Y', strtotime($score_date));
    }
    
    // Start building output
    $output = '<div class="score-card">
        <div class="score-header">
            <span class="score-label">Score Card</span>
        </div>
        
        <div class="score-related-data">
            <div class="related-col">
                <div class="related-item">
                    <span class="related-label">Archer:</span>
                    <span class="related-value">' . esc_html($user_name) . '</span>
                </div>';
    
    if (!empty($tournament_name)) {
        $output .= '<div class="related-item">
                <span class="related-label">Tournament:</span>
                <span class="related-value">' . esc_html($tournament_name) . '</span>
            </div>';
    }
    
    $output .= '</div>
            <div class="related-col">';
    
    if (!empty($club_name)) {
        $output .= '<div class="related-item">
                <span class="related-label">Club:</span>
                <span class="related-value">' . esc_html($club_name) . '</span>
            </div>';
    }
    
    if (!empty($round)) {
        $output .= '<div class="related-item">
                <span class="related-label">Round/Flight:</span>
                <span class="related-value">' . esc_html($round) . '</span>
            </div>';
    }
    
    $output .= '</div>
            <div class="related-col">';
    
    if (!empty($formatted_date)) {
        $output .= '<div class="related-item">
                <span class="related-label">Date:</span>
                <span class="related-value">' . esc_html($formatted_date) . '</span>
            </div>';
    }
    
    $output .= '<div class="related-item">
                <span class="related-label">Score ID:</span>
                <span class="related-value">' . esc_html($score_id) . '</span>
            </div>
            </div>
        </div>
        
        <div class="score-main-info">
            <div class="total-score-container">
                <span class="total-label">Total Score</span>
                <span class="total-value">' . esc_html($total_score) . '</span>
            </div>
        </div>
        
        <div class="targets-section">
            <div class="targets-header">
                <span class="targets-label">Targets</span>
            </div>
            
            <div class="targets-grid">';
    
    // Display targets with icons - using true_false field values
    $hits = 0;
    $misses = 0;
    
    for ($i = 1; $i <= 30; $i++) {
        $is_hit = get_field('target_' . $i, $post_id);
        
        if ($is_hit) {
            $hits++;
            $icon_class = 'hit';
            $icon = '✓';
            $value_display = '1'; // For true_false fields, we just show 1 for hit
        } else {
            $misses++;
            $icon_class = 'miss';
            $icon = '✗';
            $value_display = '';
        }
        
        $output .= '<div class="target-box ' . $icon_class . '">
                <div class="target-number">' . $i . '</div>
                <div class="target-icon">' . $icon . '</div>
                <div class="target-value">' . $value_display . '</div>
            </div>';
    }
    
    $output .= '</div></div>'; // Close targets-grid and targets-section
    
    // Add score statistics
    if ($hits + $misses > 0) {
        $hit_percentage = round(($hits / ($hits + $misses)) * 100);
        
        $output .= '<div class="score-stats">
            <div class="stat-item">
                <span class="stat-label">Hits</span>
                <span class="stat-value">' . $hits . '</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Misses</span>
                <span class="stat-value">' . $misses . '</span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Hit Rate</span>
                <span class="stat-value">' . $hit_percentage . '%</span>
            </div>
        </div>';
    }
    
    $output .= '</div>'; // Close score-card
    
    // Add the styling
    $output .= '<style>
        .score-card {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        
        .score-header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .score-label {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .score-related-data {
            display: flex;
            justify-content: space-between;
            padding: 20px 25px;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
        }
        
        .related-col {
            flex: 1;
        }
        
        .related-item {
            margin-bottom: 12px;
        }
        
        .related-label {
            font-size: 14px;
            color: #6c757d;
            display: block;
            margin-bottom: 3px;
            font-weight: 600;
        }
        
        .related-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .score-main-info {
            padding: 30px 25px;
            display: flex;
            justify-content: center;
            border-bottom: 1px solid #eee;
        }
        
        .total-score-container {
            text-align: center;
        }
        
        .total-label {
            display: block;
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .total-value {
            font-size: 48px;
            color: #2980b9;
            font-weight: 700;
        }
        
        .targets-section {
            padding: 0 25px 25px;
        }
        
        .targets-header {
            margin: 20px 0;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        
        .targets-label {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .targets-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
        }
        
        .target-box {
            position: relative;
            height: 70px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .target-box:hover {
            transform: translateY(-3px);
        }
        
        .target-box.hit {
            background: rgba(39, 174, 96, 0.1);
            border: 1px solid rgba(39, 174, 96, 0.3);
        }
        
        .target-box.miss {
            background: rgba(231, 76, 60, 0.1);
            border: 1px solid rgba(231, 76, 60, 0.3);
        }
        
        .target-number {
            position: absolute;
            top: 5px;
            left: 5px;
            font-size: 12px;
            color: #95a5a6;
        }
        
        .target-icon {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .hit .target-icon {
            color: #27ae60;
            font-weight: bold;
        }
        
        .miss .target-icon {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .target-value {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .score-stats {
            display: flex;
            justify-content: space-around;
            padding: 25px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-label {
            display: block;
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        @media (max-width: 768px) {
            .score-related-data {
                flex-direction: column;
            }
            
            .related-col {
                margin-bottom: 15px;
            }
            
            .targets-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 480px) {
            .targets-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>';
    
    // Return original content plus our score details
    return $content . $output;
}