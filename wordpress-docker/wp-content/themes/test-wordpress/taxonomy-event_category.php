<?php
get_header();
$term    = get_queried_object();
$term_id = isset($term->term_id) ? (int) $term->term_id : 0;
// Highlight events (2 items) ordered by start date
$highlight_ids = [];
$highlight     = new WP_Query([
    'post_type'           => 'event',
    'posts_per_page'      => 2,
    'ignore_sticky_posts' => true,
    'meta_key'            => '_event_start',
    'orderby'             => 'meta_value',
    'meta_type'           => 'DATE',
    'order'               => 'ASC',
    'tax_query'           => [
        [
            'taxonomy' => 'event_category',
            'field'    => 'term_id',
            'terms'    => $term_id,
        ]
    ],
]);
if ($highlight->have_posts()) {
    while ($highlight->have_posts()) {
        $highlight->the_post();
        $highlight_ids[] = get_the_ID();
    }
    wp_reset_postdata();
}
$paged = max(1, (int) get_query_var('paged'));
// All events (exclude highlight) - keep 6 per page
$all_events = new WP_Query([
    'post_type'           => 'event',
    'posts_per_page'      => 6,
    'paged'               => $paged,
    'post__not_in'        => $highlight_ids,
    'ignore_sticky_posts' => true,
    'meta_key'            => '_event_start',
    'orderby'             => 'meta_value',
    'meta_type'           => 'DATE',
    'order'               => 'ASC',
    'tax_query'           => [
        [
            'taxonomy' => 'event_category',
            'field'    => 'term_id',
            'terms'    => $term_id,
        ]
    ],
]);
?>
<main class="archive-page py-5 blog-category">
    <div class="container">
        <div class="mb-4 text-center">
            <h2 class="mb-1 fw-bold"><?php single_term_title(); ?></h2>
            <?php if (!empty($term->description)) : ?>
                <p class="text-muted mb-0"><?php echo esc_html($term->description); ?></p>
            <?php endif; ?>
        </div>
        <!-- Highlight -->
        <div class="text-start mb-2">
            <h4 class="mb-0">Highlight Events</h4>
        </div>
        <div class="row g-4 pb-5">
            <?php if (!empty($highlight_ids)) : ?>
                <?php
                $highlight_render = new WP_Query([
                    'post_type'           => 'event',
                    'post__in'            => $highlight_ids,
                    'orderby'             => 'post__in',
                    'posts_per_page'      => 2,
                    'ignore_sticky_posts' => true,
                ]);
                while ($highlight_render->have_posts()) : $highlight_render->the_post();
                ?>
                    <div class="col-12 col-md-6">
                        <?php get_template_part('template-parts/thumb-item-event'); ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="text-muted mb-0">No highlight events found.</p>
                </div>
            <?php endif; ?>
        </div>
        <!-- All -->
        <div class="text-start mb-2">
            <h4 class="mb-0">All Events</h4>
        </div>
        <div class="row g-4">
            <?php if ($all_events->have_posts()) : ?>
                <?php while ($all_events->have_posts()) : $all_events->the_post(); ?>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <?php get_template_part('template-parts/thumb-item-event'); ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="text-muted mb-0">No events found.</p>
                </div>
            <?php endif; ?>
        </div>
        <!-- Pagination -->
        <?php
        $pages = paginate_links([
            'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'    => '?paged=%#%',
            'type'      => 'array',
            'current'   => max(1, (int) get_query_var('paged')),
            'total'     => (int) $all_events->max_num_pages,
            'prev_text' => 'Previous',
            'next_text' => 'Next',
        ]);
        ?>
        <?php if (is_array($pages)) : ?>
            <nav aria-label="Archive navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php
                    foreach ($pages as $page) {
                        if (strpos($page, 'current') !== false) {
                            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . esc_html(wp_strip_all_tags($page)) . '</span></li>';
                        } elseif (strpos($page, 'dots') !== false) {
                            echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                        } else {
                            echo '<li class="page-item">' . str_replace('page-numbers', 'page-link', $page) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>