<?php
get_header();
$paged = max(1, (int) get_query_var('paged'));
$args  = [
    'post_type'           => 'post',
    'posts_per_page'      => 6,
    'paged'               => $paged,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
];
$query = new WP_Query($args);
// Make paginate_links work with our custom query
$original_wp_query   = $GLOBALS['wp_query'];
$GLOBALS['wp_query'] = $query;
?>
<main class="archive-blog py-5 blog-index">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold"><?php esc_html_e('All Blogs', 'test-wordpress'); ?></h2>
            </div>
        </div>
        <div class="row">
            <?php if ($query->have_posts()) : ?>
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden blog-card">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="d-block">
                                    <?php
                                    the_post_thumbnail('medium', [
                                        'class' => 'card-img-top object-fit-cover',
                                        'style' => 'height:200px;',
                                    ]);
                                    ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php the_permalink(); ?>" class="d-block">
                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/no-image.png'); ?>"
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
                                    <?php echo esc_html(get_the_date('d M Y')); ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <?php
                                    $cat_list = get_the_category_list(', ');
                                    if ($cat_list) {
                                        // category list contains links; safe output
                                        echo wp_kses_post($cat_list);
                                    } else {
                                        echo '<span class="text-muted">' . esc_html__('Uncategorized', 'test-wordpress') . '</span>';
                                    }
                                    ?>
                                </p>
                            </div>
                            <div class="card-footer bg-white border-0 text-end">
                                <a href="<?php the_permalink(); ?>" class="btn btn-outline-primary btn-sm">
                                    <?php esc_html_e('Read More', 'test-wordpress'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="text-muted"><?php esc_html_e('No posts found.', 'test-wordpress'); ?></p>
            <?php endif; ?>
        </div>
        <?php
        $pages = paginate_links([
            'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'    => '?paged=%#%',
            'current'   => $paged,
            'total'     => (int) $query->max_num_pages,
            'type'      => 'array',
            'prev_text' => esc_html__('Previous', 'test-wordpress'),
            'next_text' => esc_html__('Next', 'test-wordpress'),
        ]);
        ?>
        <?php if (is_array($pages)) : ?>
            <div class="mt-5">
                <nav aria-label="<?php echo esc_attr__('Archive navigation', 'test-wordpress'); ?>">
                    <ul class="pagination justify-content-center">
                        <?php
                        foreach ($pages as $page) {
                            if (strpos($page, 'current') !== false) {
                                echo '<li class="page-item active" aria-current="page"><span class="page-link">' . esc_html(wp_strip_all_tags($page)) . '</span></li>';
                            } elseif (strpos($page, 'dots') !== false) {
                                echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                            } else {
                                // Replace page-numbers class with page-link for Bootstrap
                                echo '<li class="page-item">' . wp_kses_post(str_replace('page-numbers', 'page-link', $page)) . '</li>';
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