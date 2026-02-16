<?php
get_header();
$cat    = get_queried_object();
$cat_id = isset($cat->term_id) ? (int) $cat->term_id : 0;
// Highlight posts: take 2 latest posts in this category
$highlight_ids = [];
$highlight     = new WP_Query([
    'cat'                    => $cat_id,
    'posts_per_page'         => 2,
    'orderby'                => 'date',
    'order'                  => 'DESC',
    'ignore_sticky_posts'    => true,
    'no_found_rows'          => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
]);
if ($highlight->have_posts()) {
    while ($highlight->have_posts()) {
        $highlight->the_post();
        $highlight_ids[] = get_the_ID();
    }
    wp_reset_postdata();
}
?>
<main class="archive-page py-5 blog-category">
    <div class="container">
        <!-- Header -->
        <div class="mb-4 text-center">
            <h2 class="mb-1 fw-bold">
                <?php echo esc_html(single_cat_title('', false)); ?>
            </h2>
            <?php if (!empty($cat->description)) : ?>
                <p class="text-muted mb-0"><?php echo esc_html($cat->description); ?></p>
            <?php endif; ?>
        </div>
        <!-- Highlight -->
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="mb-0"><?php esc_html_e('Highlight Posts', 'test-wordpress'); ?></h4>
        </div>
        <div class="row g-4 pb-5">
            <?php if (!empty($highlight_ids)) : ?>
                <?php
                // Render highlight from IDs to keep consistent output
                $highlight_render = new WP_Query([
                    'post_type'              => 'post',
                    'post__in'               => $highlight_ids,
                    'orderby'                => 'post__in',
                    'posts_per_page'         => 2,
                    'ignore_sticky_posts'    => true,
                    'no_found_rows'          => true,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                ]);
                while ($highlight_render->have_posts()) :
                    $highlight_render->the_post();
                ?>
                    <div class="col-12 col-md-6">
                        <?php get_template_part('template-parts/thumb-item'); ?>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="col-12">
                    <p class="text-muted mb-0"><?php esc_html_e('No highlight posts found.', 'test-wordpress'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <!-- All Posts -->
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h4 class="mb-0"><?php esc_html_e('All Posts', 'test-wordpress'); ?></h4>
        </div>
        <div class="row g-4">
            <?php
            // Custom query for all posts (exclude highlight)
            $paged     = max(1, (int) get_query_var('paged'));
            $all_posts = new WP_Query([
                'cat'                 => $cat_id,
                'post__not_in'        => $highlight_ids,
                'posts_per_page'      => 6,
                'paged'               => $paged,
                'ignore_sticky_posts' => true,
            ]);
            if ($all_posts->have_posts()) :
                while ($all_posts->have_posts()) :
                    $all_posts->the_post();
            ?>
                    <div class="col-12 col-sm-6 col-lg-4">
                        <?php get_template_part('template-parts/thumb-item'); ?>
                    </div>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="col-12">
                    <p class="text-muted mb-0"><?php esc_html_e('No posts found.', 'test-wordpress'); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <!-- Pagination -->
        <?php
        $pages = paginate_links([
            'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format'    => '?paged=%#%',
            'current'   => $paged,
            'total'     => (int) $all_posts->max_num_pages,
            'type'      => 'array',
            'prev_text' => esc_html__('Previous', 'test-wordpress'),
            'next_text' => esc_html__('Next', 'test-wordpress'),
        ]);
        ?>
        <?php if (is_array($pages)) : ?>
            <nav aria-label="<?php echo esc_attr__('Archive navigation', 'test-wordpress'); ?>" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php
                    foreach ($pages as $page) {
                        if (strpos($page, 'current') !== false) {
                            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . esc_html(wp_strip_all_tags($page)) . '</span></li>';
                        } elseif (strpos($page, 'dots') !== false) {
                            echo '<li class="page-item disabled"><span class="page-link">…</span></li>';
                        } else {
                            echo '<li class="page-item">' . wp_kses_post(str_replace('page-numbers', 'page-link', $page)) . '</li>';
                        }
                    }
                    ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>