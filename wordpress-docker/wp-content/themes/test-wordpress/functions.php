<?php
/**
 * functions.php
 * Custom WordPress Theme Functions
 */

/* =========================
 * Theme Setup
 * ========================= */
function test_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 240,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
}
add_action('after_setup_theme', 'test_theme_setup');

/* =========================
 * Enqueue Styles / Scripts
 * ========================= */
function test_theme_enqueue() {
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();
    // Swiper
    $swiper_css_path = $theme_dir . '/assets/library/swiper/swiper-bundle.min.css';
    $swiper_js_path  = $theme_dir . '/assets/library/swiper/swiper-bundle.min.js';
    wp_enqueue_style(
        'swiper',
        $theme_uri . '/assets/library/swiper/swiper-bundle.min.css',
        [],
        file_exists($swiper_css_path) ? filemtime($swiper_css_path) : null
    );
    wp_enqueue_script(
        'swiper',
        $theme_uri . '/assets/library/swiper/swiper-bundle.min.js',
        [],
        file_exists($swiper_js_path) ? filemtime($swiper_js_path) : null,
        true
    );
    // Bootstrap
    $bs_css_path = $theme_dir . '/assets/library/bootstrap/bootstrap.min.css';
    $bs_js_path  = $theme_dir . '/assets/library/bootstrap/bootstrap.bundle.min.js';
    wp_enqueue_style(
        'bootstrap',
        $theme_uri . '/assets/library/bootstrap/bootstrap.min.css',
        [],
        file_exists($bs_css_path) ? filemtime($bs_css_path) : null
    );
    wp_enqueue_script(
        'bootstrap',
        $theme_uri . '/assets/library/bootstrap/bootstrap.bundle.min.js',
        [],
        file_exists($bs_js_path) ? filemtime($bs_js_path) : null,
        true
    );
    // Theme main CSS (style.css)
    $style_path = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        ['bootstrap'],
        file_exists($style_path) ? filemtime($style_path) : null
    );
}
add_action('wp_enqueue_scripts', 'test_theme_enqueue');

/* =========================
 * Swiper Init (Inline via handle)
 * ========================= */
function test_theme_swiper_init_inline() {
    $js = <<<JS
        document.addEventListener('DOMContentLoaded', function() {
            const el = document.querySelector('.mySwiper');
            if (!el || typeof Swiper === 'undefined') return;
            new Swiper('.mySwiper', {
                loop: true,
                autoHeight: true,
                pagination: {
                el: '.swiper-pagination',
                clickable: true
                },
                navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev'
                }
            });
        });
    JS;
    // Ensure it prints after Swiper script
    wp_add_inline_script('swiper', $js, 'after');
}
add_action('wp_enqueue_scripts', 'test_theme_swiper_init_inline', 20);

/* =========================
 * Menus
 * ========================= */
function theme_register_menus() {
    register_nav_menus([
        'primary' => __('Primary Menu', 'test-wordpress'),
    ]);
}
add_action('init', 'theme_register_menus');

/* =========================
 * Main Queries (Blog / Category / Events)
 * ========================= */
function theme_modify_main_queries($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    // Blog home (posts page)
    if ($query->is_home()) {
        $query->set('posts_per_page', 6);
        return;
    }
    // Blog category
    if ($query->is_category()) {
        $query->set('posts_per_page', 6);
        return;
    }
    // Events archive (CPT: event)
    if ($query->is_post_type_archive('event')) {
        $query->set('posts_per_page', 6);
        $query->set('meta_key', '_event_start');
        $query->set('orderby', 'meta_value');
        $query->set('meta_type', 'DATE');
        $query->set('order', 'ASC');
        $query->set('meta_query', [
            [
                'key'     => '_event_start',
                'compare' => 'EXISTS',
                'type'    => 'DATE',
            ],
        ]);
        return;
    }
    // Event category taxonomy archive
    if ($query->is_tax('event_category')) {
        $query->set('posts_per_page', 6);
        $query->set('meta_key', '_event_start');
        $query->set('orderby', 'meta_value');
        $query->set('meta_type', 'DATE');
        $query->set('order', 'ASC');
        $query->set('meta_query', [
            [
                'key'     => '_event_start',
                'compare' => 'EXISTS',
                'type'    => 'DATE',
            ],
        ]);
        return;
    }
}
add_action('pre_get_posts', 'theme_modify_main_queries');

/* =========================
 * Events CPT + Taxonomy
 * ========================= */
function theme_register_event_cpt() {
    $labels = [
        'name'               => __('Events', 'test-wordpress'),
        'singular_name'      => __('Event', 'test-wordpress'),
        'menu_name'          => __('Events', 'test-wordpress'),
        'add_new'            => __('Add New Event', 'test-wordpress'),
        'add_new_item'       => __('Add New Event', 'test-wordpress'),
        'edit_item'          => __('Edit Event', 'test-wordpress'),
        'new_item'           => __('New Event', 'test-wordpress'),
        'view_item'          => __('View Event', 'test-wordpress'),
        'view_items'         => __('View Events', 'test-wordpress'),
        'search_items'       => __('Search Events', 'test-wordpress'),
        'not_found'          => __('No events found', 'test-wordpress'),
        'not_found_in_trash' => __('No events found in Trash', 'test-wordpress'),
    ];
    $args = [
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-calendar-alt',
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
        // allow Tag "featured" on events too if you want (optional)
        'taxonomies'   => ['post_tag'],
        'rewrite'      => ['slug' => 'events'],
        'show_in_rest' => true,
    ];
    register_post_type('event', $args);
}
add_action('init', 'theme_register_event_cpt');

function theme_register_event_taxonomy() {
    $labels = [
        'name'          => __('Event Categories', 'test-wordpress'),
        'singular_name' => __('Event Category', 'test-wordpress'),
        'menu_name'     => __('Event Categories', 'test-wordpress'),
        'search_items'  => __('Search Event Categories', 'test-wordpress'),
        'all_items'     => __('All Event Categories', 'test-wordpress'),
        'edit_item'     => __('Edit Event Category', 'test-wordpress'),
        'update_item'   => __('Update Event Category', 'test-wordpress'),
        'add_new_item'  => __('Add New Event Category', 'test-wordpress'),
        'new_item_name' => __('New Event Category Name', 'test-wordpress'),
    ];
    $args = [
        'labels'       => $labels,
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['slug' => 'event-category'],
        'show_in_rest' => true,
    ];
    register_taxonomy('event_category', ['event'], $args);
}
add_action('init', 'theme_register_event_taxonomy');

/* =========================
 * Event Meta Boxes (Start / End Date)
 * ========================= */
function event_add_meta_boxes() {
    add_meta_box(
        'event_dates',
        __('Event Dates', 'test-wordpress'),
        'event_dates_callback',
        'event',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'event_add_meta_boxes');

function event_dates_callback($post) {
    $start = get_post_meta($post->ID, '_event_start', true);
    $end   = get_post_meta($post->ID, '_event_end', true);
    wp_nonce_field('save_event_dates', 'event_dates_nonce');
    ?>
    <p>
        <label for="event_start"><strong><?php esc_html_e('Start Date', 'test-wordpress'); ?></strong></label><br>
        <input id="event_start" type="date" name="event_start" value="<?php echo esc_attr($start); ?>" />
    </p>
    <p>
        <label for="event_end"><strong><?php esc_html_e('End Date', 'test-wordpress'); ?></strong></label><br>
        <input id="event_end" type="date" name="event_end" value="<?php echo esc_attr($end); ?>" />
    </p>
    <?php
}

// Validate Y-m-d date
function theme_is_valid_ymd_date($date) {
    if (empty($date) || !is_string($date)) {
        return false;
    }
    $date = trim($date);
    $dt   = DateTime::createFromFormat('Y-m-d', $date);
    return $dt && $dt->format('Y-m-d') === $date;
}

function event_save_meta($post_id) {
    // Only for event post type
    if (get_post_type($post_id) !== 'event') {
        return;
    }
    // Nonce
    if (!isset($_POST['event_dates_nonce']) || !wp_verify_nonce($_POST['event_dates_nonce'], 'save_event_dates')) {
        return;
    }
    // Autosave / revisions
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (wp_is_post_revision($post_id)) {
        return;
    }
    // Capability
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    $start_raw = isset($_POST['event_start']) ? sanitize_text_field(wp_unslash($_POST['event_start'])) : '';
    $end_raw   = isset($_POST['event_end']) ? sanitize_text_field(wp_unslash($_POST['event_end'])) : '';
    $start     = theme_is_valid_ymd_date($start_raw) ? $start_raw : '';
    $end       = theme_is_valid_ymd_date($end_raw) ? $end_raw : '';
    // If both valid but end < start, clear end (safe fallback)
    if ($start && $end && strtotime($end) < strtotime($start)) {
        $end = '';
    }
    // Save / delete meta cleanly
    if ($start) {
        update_post_meta($post_id, '_event_start', $start);
    } else {
        delete_post_meta($post_id, '_event_start');
    }
    if ($end) {
        update_post_meta($post_id, '_event_end', $end);
    } else {
        delete_post_meta($post_id, '_event_end');
    }
}
add_action('save_post', 'event_save_meta');

/* =========================
 * Bootstrap 5 Nav Walker
 * ========================= */
class Bootstrap_5_WP_Nav_Menu_Walker extends Walker_Nav_Menu {
    private $current_item;
    private $dropdown_menu_alignment_values = [
        'dropdown-menu-start',
        'dropdown-menu-end',
        'dropdown-menu-sm-start',
        'dropdown-menu-sm-end',
        'dropdown-menu-md-start',
        'dropdown-menu-md-end',
        'dropdown-menu-lg-start',
        'dropdown-menu-lg-end',
        'dropdown-menu-xl-start',
        'dropdown-menu-xl-end',
    ];

    public function display_element($element, &$children_elements, $max_depth, $depth, $args, &$output) {
        if (isset($args[0]) && is_object($args[0])) {
            $args[0]->has_children = !empty($children_elements[$element->ID]);
        }
        parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
    }

    public function start_lvl(&$output, $depth = 0, $args = null) {
        $dropdown_menu_class = '';
        if (!empty($this->current_item) && !empty($this->current_item->classes)) {
            foreach ($this->current_item->classes as $class) {
                if (in_array($class, $this->dropdown_menu_alignment_values, true)) {
                    $dropdown_menu_class = $class;
                    break;
                }
            }
        }
        $indent  = str_repeat("\t", $depth);
        $submenu = ($depth > 0) ? ' sub-menu' : '';
        $output .= "\n$indent<ul class=\"dropdown-menu $submenu $dropdown_menu_class depth_$depth\">\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $this->current_item = $item;
        $indent    = ($depth) ? str_repeat("\t", $depth) : '';
        $classes   = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'nav-item';
        if (!empty($args->has_children)) {
            $classes[] = 'dropdown';
        }
        if (!empty($item->current) || !empty($item->current_item_ancestor)) {
            $classes[] = 'active';
        }
        $classes[]    = 'menu-item-' . $item->ID;
        $class_names  = ' class="' . esc_attr(implode(' ', array_filter($classes))) . '"';
        $item_id      = apply_filters('nav_menu_item_id', '', $item, $args);
        $item_id      = strlen($item_id) ? ' id="' . esc_attr($item_id) . '"' : '';
        $output      .= $indent . '<li' . $item_id . $class_names . '>';
        $atts         = [];
        $atts['href'] = !empty($item->url) ? esc_url($item->url) : '';
        if (!empty($args->has_children)) {
            $atts['class']          = 'nav-link dropdown-toggle';
            $atts['data-bs-toggle'] = 'dropdown';
            $atts['aria-expanded']  = 'false';
            $atts['role']           = 'button';
        } else {
            $atts['class'] = 'nav-link';
        }
        $attributes = '';
        foreach ($atts as $attr => $value) {
            if ($value === '') {
                continue;
            }
            $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
        }
        $title        = esc_html(apply_filters('the_title', $item->title, $item->ID));
        $item_output  = $args->before ?? '';
        $item_output .= "<a{$attributes}>";
        $item_output .= ($args->link_before ?? '') . $title . ($args->link_after ?? '');
        $item_output .= "</a>";
        $item_output .= $args->after ?? '';
        $output      .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/* =========================
 * Taxonomy Search (safe-ish)
 * NOTE: Requires setting 'tax_search' => true on your WP_Query.
 * ========================= */
function theme_tax_search_enabled($query) {
    return ($query instanceof WP_Query)
        && !is_admin()
        && $query->get('tax_search')
        && $query->get('s');
}

function theme_tax_search_join($join, $query) {
    if (!theme_tax_search_enabled($query)) {
        return $join;
    }
    global $wpdb;
    // Join term tables once
    if (strpos($join, 'term_relationships') === false) {
        $join .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id) ";
        $join .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
        $join .= " LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id) ";
    }
    return $join;
}

function theme_tax_search_where($where, $query) {
    if (!theme_tax_search_enabled($query)) {
        return $where;
    }
    global $wpdb;
    $s      = $query->get('s');
    $like   = '%' . $wpdb->esc_like($s) . '%';
    $taxes  = $query->get('tax_search_taxonomies');
    if (empty($taxes) || !is_array($taxes)) {
        $taxes = ['category', 'event_category'];
    }
    // sanitize taxonomy slugs
    $taxes  = array_map('sanitize_key', $taxes);
    if (empty($taxes)) {
        return $where;
    }
    $tax_in = "('" . implode("','", array_map('esc_sql', $taxes)) . "')";
    // Add an OR group in a way that is less likely to explode the logic.
    // (Still appended, but grouped)
    $where .= $wpdb->prepare(
        " OR (tt.taxonomy IN $tax_in AND t.name LIKE %s)",
        $like
    );
    return $where;
}

function theme_tax_search_groupby($groupby, $query) {
    if (!theme_tax_search_enabled($query)) {
        return $groupby;
    }
    global $wpdb;
    $post_id = "{$wpdb->posts}.ID";
    if (empty($groupby)) {
        return $post_id;
    }
    if (strpos($groupby, $post_id) === false) {
        return $groupby . ", $post_id";
    }
    return $groupby;
}

add_filter('posts_join', 'theme_tax_search_join', 10, 2);
add_filter('posts_where', 'theme_tax_search_where', 10, 2);
add_filter('posts_groupby', 'theme_tax_search_groupby', 10, 2);

/* =========================
 * Location settings (Bangkok)
 * ========================= */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('theme_locations', [
        'title'    => __('Location', 'test-wordpress'),
        'priority' => 40,
    ]);
    $wp_customize->add_setting('loc_bkk_title', [
        'default'           => 'Bangkok Office',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('loc_bkk_title', [
        'label'   => __('Office Title', 'test-wordpress'),
        'section' => 'theme_locations',
        'type'    => 'text',
    ]);
    $wp_customize->add_setting('loc_bkk_address', [
        'default'           => '123 ถนนสุขุมวิท เขตวัฒนา กรุงเทพฯ 10110',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('loc_bkk_address', [
        'label'   => __('Office Address', 'test-wordpress'),
        'section' => 'theme_locations',
        'type'    => 'textarea',
    ]);
    $wp_customize->add_setting('loc_bkk_map_url', [
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('loc_bkk_map_url', [
        'label'   => __('Map Link URL', 'test-wordpress'),
        'section' => 'theme_locations',
        'type'    => 'url',
    ]);
    $wp_customize->add_setting('loc_map_embed_src', [
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('loc_map_embed_src', [
        'label'       => __('Google Map Embed SRC (iframe src only)', 'test-wordpress'),
        'description' => __('Paste only the src from the iframe embed code.', 'test-wordpress'),
        'section'     => 'theme_locations',
        'type'        => 'url',
    ]);
});

/* =========================
 * FAQ Post Type
 * ========================= */
add_action('init', function () {
    register_post_type('faq', [
        'labels' => [
            'name'          => __('FAQs', 'test-wordpress'),
            'singular_name' => __('FAQ', 'test-wordpress'),
            'add_new_item'  => __('Add New FAQ', 'test-wordpress'),
            'edit_item'     => __('Edit FAQ', 'test-wordpress'),
        ],
        'public'       => true,
        'has_archive'  => false,
        'menu_icon'    => 'dashicons-editor-help',
        'supports'     => ['title', 'editor', 'page-attributes'],
        'show_in_rest' => true,
    ]);
});

/* =========================
 * Contact settings (Customizer)
 * ========================= */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_section('theme_contact', [
        'title'    => __('Contact', 'test-wordpress'),
        'priority' => 41,
    ]);
    $wp_customize->add_setting('contact_address', [
        'default'           => "123 ถนนสุขุมวิท\nเขต/อำเภอ ... กรุงเทพฯ 10110",
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('contact_address', [
        'label'   => __('Address', 'test-wordpress'),
        'section' => 'theme_contact',
        'type'    => 'textarea',
    ]);
    $wp_customize->add_setting('contact_phone', [
        'default'           => '+66 12 345 678',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('contact_phone', [
        'label'   => __('Phone (Display)', 'test-wordpress'),
        'section' => 'theme_contact',
        'type'    => 'text',
    ]);
    $wp_customize->add_setting('contact_phone_tel', [
        'default'           => '+6612345678',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('contact_phone_tel', [
        'label'       => __('Phone (Tel link)', 'test-wordpress'),
        'description' => __('Example: +6612345678 (no spaces)', 'test-wordpress'),
        'section'     => 'theme_contact',
        'type'        => 'text',
    ]);
    $wp_customize->add_setting('contact_email', [
        'default'           => 'info@example.com',
        'sanitize_callback' => 'sanitize_email',
    ]);
    $wp_customize->add_control('contact_email', [
        'label'   => __('Email', 'test-wordpress'),
        'section' => 'theme_contact',
        'type'    => 'text',
    ]);
    $wp_customize->add_setting('contact_facebook', [
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('contact_facebook', [
        'label'   => __('Facebook URL', 'test-wordpress'),
        'section' => 'theme_contact',
        'type'    => 'url',
    ]);
    $wp_customize->add_setting('contact_instagram', [
        'default'           => '#',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('contact_instagram', [
        'label'   => __('Instagram URL', 'test-wordpress'),
        'section' => 'theme_contact',
        'type'    => 'url',
    ]);
});
?>