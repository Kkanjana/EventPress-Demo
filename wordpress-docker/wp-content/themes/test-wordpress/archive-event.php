<?php
get_header();
$paged = max(1, (int) get_query_var('paged'));
$args  = [
    'post_type'           => 'event',
    'posts_per_page'      => 6,
    'paged'               => $paged,
    'meta_key'            => '_event_start',
    'orderby'             => 'meta_value',
    'meta_type'           => 'DATE',
    'order'               => 'ASC',
    'ignore_sticky_posts' => true,
];
$query = new WP_Query($args);
// Make paginate_links work with our custom query
$original_wp_query   = $GLOBALS['wp_query'];
$GLOBALS['wp_query'] = $query;
?>
<main class="archive-event py-5 blog-index">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold">All Events</h2>
            </div>
        </div>
        <div class="row">
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <?php
                    $start      = get_post_meta(get_the_ID(), '_event_start', true);
                    $end        = get_post_meta(get_the_ID(), '_event_end', true);
                    $start_text = $start ? date_i18n('d M Y', strtotime($start)) : '';
                    $end_text   = $end ? date_i18n('d M Y', strtotime($end)) : '';
                    $terms      = get_the_terms(get_the_ID(), 'event_category');
                    ?>
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden blog-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="d-block">
                                    <?php the_post_thumbnail('medium', [
                                        'class' => 'card-img-top object-fit-cover',
                                        'style' => 'height:200px;'
                                    ]); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="d-block">
                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/01.png'); ?>"
                                        class="card-img-top object-fit-cover"
                                        style="height:200px;"
                                        alt="<?php echo esc_attr(get_the_title()); ?>">
                                </a>
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title mb-2">
                                    <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                                        <?php the_title(); ?>
                                    </a>
                                </h5>
                                <p class="text-muted small mb-2">
                                    <?php if ($start_text) : ?>
                                        <?php echo esc_html($start_text); ?>
                                        <?php if ($end_text) : ?>
                                            <?php echo ' – ' . esc_html($end_text); ?>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <?php echo esc_html__('Date TBA', 'your-textdomain'); ?>
                                    <?php endif; ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <?php
                                    if (!empty($terms) && !is_wp_error($terms)) {
                                        foreach ($terms as $i => $t) {
                                            echo '<a href="' . esc_url(get_term_link($t)) . '" >' . esc_html($t->name) . '</a>';
                                            if ($i < count($terms) - 1) echo ', ';
                                        }
                                    } else {
                                        echo '<span class="text-white-50">No Category</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-0 text-end">
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
                                    View Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="text-muted">No events found.</p>
            <?php endif; ?>
        </div>
        <?php
        $pages = paginate_links([
            'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'    => '?paged=%#%',
            'current'   => max(1, (int) get_query_var('paged')),
            'total'     => (int) $query->max_num_pages,
            'type'      => 'array',
            'prev_text' => 'Previous',
            'next_text' => 'Next',
        ]);
        ?>
        <?php if (is_array($pages)) : ?>
            <div class="mt-5">
                <nav aria-label="Archive navigation">
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
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
wp_reset_postdata();
$GLOBALS['wp_query'] = $original_wp_query;
get_footer();
?>