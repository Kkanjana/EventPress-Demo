<?php
get_header();
$search_term = get_search_query();
// Separate pagination for each section
$post_page   = isset($_GET['postpage']) ? max(1, (int) $_GET['postpage']) : 1;
$event_page  = isset($_GET['eventpage']) ? max(1, (int) $_GET['eventpage']) : 1;
// Posts query (search includes blog category name via tax_search)
$post_query  = new WP_Query([
    'post_type'             => 'post',
    's'                     => $search_term,
    'posts_per_page'        => 3,
    'paged'                 => $post_page,
    'ignore_sticky_posts'   => true,
    'tax_search'            => true,
    'tax_search_taxonomies' => ['category'],
]);
// Events query (search includes event_category name via tax_search)
$event_query = new WP_Query([
    'post_type'             => 'event',
    's'                     => $search_term,
    'posts_per_page'        => 3,
    'paged'                 => $event_page,
    'meta_key'              => '_event_start',
    'orderby'               => 'meta_value',
    'meta_type'             => 'DATE',
    'order'                 => 'ASC',
    'ignore_sticky_posts'   => true,
    'tax_search'            => true,
    'tax_search_taxonomies' => ['event_category'],
]);
/**
 * Render Bootstrap pagination for custom "postpage/eventpage" query parameter
 */
function theme_render_bootstrap_pagination($total_pages, $current_page, $page_param, $search_term) {
    if ((int) $total_pages <= 1) {
        return;
    }
    // Use WordPress search link as base (respects permalink structure)
    $base_url = get_search_link($search_term);
    $pages    = paginate_links([
        'base'      => add_query_arg([
            $page_param => '%#%',
        ], $base_url),
        'format'    => '',
        'current'   => (int) $current_page,
        'total'     => (int) $total_pages,
        'type'      => 'array',
        'prev_text' => esc_html__('Previous', 'test-wordpress'),
        'next_text' => esc_html__('Next', 'test-wordpress'),
    ]);
    if (!is_array($pages)) {
        return;
    }
    echo '<nav aria-label="' . esc_attr__('Archive navigation', 'test-wordpress') . '" class="mt-4">';
    echo '<ul class="pagination justify-content-center">';
    foreach ($pages as $page) {
        if (strpos($page, 'current') !== false) {
            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . esc_html(wp_strip_all_tags($page)) . '</span></li>';
        } elseif (strpos($page, 'dots') !== false) {
            echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
        } else {
            echo '<li class="page-item">' . wp_kses_post(str_replace('page-numbers', 'page-link', $page)) . '</li>';
        }
    }
    echo '</ul>';
    echo '</nav>';
}
?>
<main class="archive-page py-5 blog-index">
    <div class="container">
        <div class="mb-4 text-center">
            <h2 class="mb-1 fw-bold"><?php esc_html_e('Search Results', 'test-wordpress'); ?></h2>
            <?php if (!empty($search_term)) : ?>
                <p class="text-muted mb-0">
                    <?php esc_html_e('You searched for:', 'test-wordpress'); ?>
                    <strong><?php echo esc_html($search_term); ?></strong>
                </p>
            <?php else : ?>
                <p class="text-muted mb-0">
                    <?php esc_html_e('Please enter a keyword to search.', 'test-wordpress'); ?>
                </p>
            <?php endif; ?>
        </div>
        <!-- Result Posts -->
        <section class="py-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0"><?php esc_html_e('Result Posts', 'test-wordpress'); ?></h4>
                <?php if ($post_query->found_posts) : ?>
                    <span class="text-muted small">
                        <?php echo esc_html($post_query->found_posts); ?>
                        <?php esc_html_e('items', 'test-wordpress'); ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="row g-4">
                <?php if ($post_query->have_posts()) : ?>
                    <?php while ($post_query->have_posts()) : $post_query->the_post(); ?>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <?php get_template_part('template-parts/thumb-item'); ?>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="col-12">
                        <p class="text-muted mb-0"><?php esc_html_e('No posts found.', 'test-wordpress'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            theme_render_bootstrap_pagination(
                (int) $post_query->max_num_pages,
                (int) $post_page,
                'postpage',
                $search_term
            );
            ?>
        </section>
        <!-- Result Events -->
        <section class="py-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0"><?php esc_html_e('Result Events', 'test-wordpress'); ?></h4>
                <?php if ($event_query->found_posts) : ?>
                    <span class="text-muted small">
                        <?php echo esc_html($event_query->found_posts); ?>
                        <?php esc_html_e('items', 'test-wordpress'); ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="row g-4">
                <?php if ($event_query->have_posts()) : ?>
                    <?php while ($event_query->have_posts()) : $event_query->the_post(); ?>
                        <div class="col-12 col-sm-6 col-lg-4">
                            <?php get_template_part('template-parts/thumb-item-event'); ?>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                <?php else : ?>
                    <div class="col-12">
                        <p class="text-muted mb-0"><?php esc_html_e('No events found.', 'test-wordpress'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php
            theme_render_bootstrap_pagination(
                (int) $event_query->max_num_pages,
                (int) $event_page,
                'eventpage',
                $search_term
            );
            ?>
        </section>
    </div>
</main>
<?php get_footer(); ?>