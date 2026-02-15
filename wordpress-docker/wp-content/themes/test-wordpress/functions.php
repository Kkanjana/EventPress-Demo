<?php
// Theme Supports
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

// Enqueue Styles / Scripts
function test_theme_enqueue() {
    $theme_uri  = get_template_directory_uri();
    $theme_dir  = get_template_directory();
    // Swiper
    $swiper_css = $theme_dir . '/assets/library/swiper/swiper-bundle.min.css';
    $swiper_js  = $theme_dir . '/assets/library/swiper/swiper-bundle.min.js';
    wp_enqueue_style(
        'swiper',
        $theme_uri . '/assets/library/swiper/swiper-bundle.min.css',
        [],
        file_exists($swiper_css) ? filemtime($swiper_css) : null
    );
    wp_enqueue_script(
        'swiper',
        $theme_uri . '/assets/library/swiper/swiper-bundle.min.js',
        [],
        file_exists($swiper_js) ? filemtime($swiper_js) : null,
        true
    );
    // Bootstrap
    $bs_css = $theme_dir . '/assets/library/bootstrap/bootstrap.min.css';
    $bs_js  = $theme_dir . '/assets/library/bootstrap/bootstrap.bundle.min.js';
    wp_enqueue_style(
        'bootstrap',
        $theme_uri . '/assets/library/bootstrap/bootstrap.min.css',
        [],
        file_exists($bs_css) ? filemtime($bs_css) : null
    );
    wp_enqueue_script(
        'bootstrap',
        $theme_uri . '/assets/library/bootstrap/bootstrap.bundle.min.js',
        [],
        file_exists($bs_js) ? filemtime($bs_js) : null,
        true
    );
    // Theme main CSS (style.css)
    wp_enqueue_style(
        'theme-style',
        get_stylesheet_uri(),
        ['bootstrap'],
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'test_theme_enqueue');

// Swiper Init (inline)
function test_theme_swiper_init() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.querySelector('.mySwiper');
        if (!el || typeof Swiper === 'undefined') return;
        new Swiper('.mySwiper', {
            loop: true,
            autoHeight: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'test_theme_swiper_init', 100);

// Menus
function theme_register_menus() {
    register_nav_menus([
        'primary' => 'Primary Menu',
    ]);
}
add_action('init', 'theme_register_menus');

// Blog + Category + Events Archives Query
function theme_modify_main_queries($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    // Blog home
    if ($query->is_home()) {
        $query->set('posts_per_page', 6);
        return;
    }
    // Blog category
    if ($query->is_category()) {
        $query->set('posts_per_page', 6);
        return;
    }
    // Events archive
    if (is_post_type_archive('event')) {
        $query->set('posts_per_page', 6);
        $query->set('meta_key', '_event_start');
        $query->set('orderby', 'meta_value');
        $query->set('order', 'ASC');
        $query->set('meta_query', [
            [
                'key'     => '_event_start',
                'compare' => 'EXISTS',
            ],
        ]);
        return;
    }
    // Event category taxonomy archive
    if (is_tax('event_category')) {
        $query->set('posts_per_page', 6);
        $query->set('meta_key', '_event_start');
        $query->set('orderby', 'meta_value');
        $query->set('order', 'ASC');
        $query->set('meta_query', [
            [
                'key'     => '_event_start',
                'compare' => 'EXISTS',
            ],
        ]);
        return;
    }
}
add_action('pre_get_posts', 'theme_modify_main_queries');

// Events CPT + Taxonomy
function theme_register_event_cpt() {
    $labels = [
        'name'               => 'Events',
        'singular_name'      => 'Event',
        'menu_name'          => 'Events',
        'add_new'            => 'Add New Event',
        'add_new_item'       => 'Add New Event',
        'edit_item'          => 'Edit Event',
        'new_item'           => 'New Event',
        'view_item'          => 'View Event',
        'view_items'         => 'View Events',
        'search_items'       => 'Search Events',
        'not_found'          => 'No events found',
        'not_found_in_trash' => 'No events found in Trash',
    ];
    $args = [
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-calendar-alt',
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt'],
        'rewrite'       => ['slug' => 'events'],
        'show_in_rest'  => true,
    ];
    register_post_type('event', $args);
}
add_action('init', 'theme_register_event_cpt');

function theme_register_event_taxonomy() {
    $labels = [
        'name'          => 'Event Categories',
        'singular_name' => 'Event Category',
        'menu_name'     => 'Event Categories',
        'search_items'  => 'Search Event Categories',
        'all_items'     => 'All Event Categories',
        'edit_item'     => 'Edit Event Category',
        'update_item'   => 'Update Event Category',
        'add_new_item'  => 'Add New Event Category',
        'new_item_name' => 'New Event Category Name',
    ];
    $args = [
        'labels'        => $labels,
        'hierarchical'  => true,
        'public'        => true,
        'rewrite'       => ['slug' => 'event-category'],
        'show_in_rest'  => true,
    ];
    register_taxonomy('event_category', ['event'], $args);
}
add_action('init', 'theme_register_event_taxonomy');

// Event Meta Boxes (Start / End Date)
function event_add_meta_boxes() {
    add_meta_box(
        'event_dates',
        'Event Dates',
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
        <label for="event_start"><strong>Start Date</strong></label><br>
        <input id="event_start" type="date" name="event_start" value="<?php echo esc_attr($start); ?>" />
    </p>
    <p>
        <label for="event_end"><strong>End Date</strong></label><br>
        <input id="event_end" type="date" name="event_end" value="<?php echo esc_attr($end); ?>" />
    </p>
    <?php
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
    // Save start
    if (isset($_POST['event_start'])) {
        $start = sanitize_text_field(wp_unslash($_POST['event_start']));
        update_post_meta($post_id, '_event_start', $start);
    }
    // Save end
    if (isset($_POST['event_end'])) {
        $end = sanitize_text_field(wp_unslash($_POST['event_end']));
        update_post_meta($post_id, '_event_end', $end);
    }
}
add_action('save_post', 'event_save_meta');

// Bootstrap 5 Nav Walker
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
    // IMPORTANT: Set $args->has_children correctly
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
        $classes[]     = 'menu-item-' . $item->ID;
        $class_names   = ' class="' . esc_attr(implode(' ', array_filter($classes))) . '"';
        $item_id       = apply_filters('nav_menu_item_id', '', $item, $args);
        $item_id       = strlen($item_id) ? ' id="' . esc_attr($item_id) . '"' : '';
        $output       .= $indent . '<li' . $item_id . $class_names . '>';
        $atts          = [];
        $atts['href']  = !empty($item->url) ? $item->url : '';
        // Link classes
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
            if ($value === '') continue;
            $attributes .= ' ' . $attr . '="' . esc_attr($value) . '"';
        }
        $title        = apply_filters('the_title', $item->title, $item->ID);
        $item_output  = $args->before ?? '';
        $item_output .= "<a{$attributes}>";
        $item_output .= ($args->link_before ?? '') . $title . ($args->link_after ?? '');
        $item_output .= "</a>";
        $item_output .= $args->after ?? '';
        $output      .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

function theme_tax_search_enabled($query) {
    return ($query instanceof WP_Query)
        && !is_admin()
        && $query->get('tax_search')
        && $query->get('s');
}

function theme_tax_search_join($join, $query) {
    if (!theme_tax_search_enabled($query)) return $join;
    global $wpdb;
    // Join term tables (once)
    if (strpos($join, 'term_relationships') === false) {
        $join .= " LEFT JOIN {$wpdb->term_relationships} AS tr 
                   ON ({$wpdb->posts}.ID = tr.object_id) ";
        $join .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt 
                   ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
        $join .= " LEFT JOIN {$wpdb->terms} AS t 
                   ON (tt.term_id = t.term_id) ";
    }
    return $join;
}

function theme_tax_search_where($where, $query) {
    if (!theme_tax_search_enabled($query)) return $where;
    global $wpdb;
    $s     = $query->get('s');
    $like  = '%' . $wpdb->esc_like($s) . '%';
    // Get taxonomies defined in query
    $taxes = $query->get('tax_search_taxonomies');
    if (empty($taxes) || !is_array($taxes)) {
        $taxes = ['category', 'event_category'];
    }
    $tax_in = "('" . implode("','", array_map('esc_sql', $taxes)) . "')";
    // Add OR condition for term name match
    $where .= $wpdb->prepare(
        " OR (tt.taxonomy IN $tax_in AND t.name LIKE %s)",
        $like
    );
    return $where;
}

function theme_tax_search_groupby($groupby, $query) {
    if (!theme_tax_search_enabled($query)) return $groupby;
    global $wpdb;
    // Prevent duplicate results due to JOIN
    $post_id = "{$wpdb->posts}.ID";
    if (empty($groupby)) return $post_id;
    if (strpos($groupby, $post_id) === false) {
        return $groupby . ", $post_id";
    }
    return $groupby;
}
add_filter('posts_join', 'theme_tax_search_join', 10, 2);
add_filter('posts_where', 'theme_tax_search_where', 10, 2);
add_filter('posts_groupby', 'theme_tax_search_groupby', 10, 2);
?>