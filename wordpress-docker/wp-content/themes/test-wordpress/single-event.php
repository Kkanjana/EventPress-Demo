<?php get_header(); ?>
<main class="single-post py-5 blog-single">
    <div class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <?php
            // Read meta AFTER the_post() to ensure correct ID
            $event_id   = get_the_ID();
            $start      = get_post_meta($event_id, '_event_start', true);
            $end        = get_post_meta($event_id, '_event_end', true);
            $start_text = ($start && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start))
                            ? date_i18n('d M Y', strtotime($start))
                            : '';
            $end_text   = ($end && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end))
                            ? date_i18n('d M Y', strtotime($end))
                            : '';
            $terms      = get_the_terms($event_id, 'event_category');
            $term_ids   = [];
            if (!empty($terms) && !is_wp_error($terms)) {
                foreach ($terms as $t) {
                    $term_ids[] = (int) $t->term_id;
                }
            }
            ?>
            <article class="mx-auto blog-single-wrap">
                <!-- Hero -->
                <div class="row g-4 align-items-start mb-4">
                    <div class="col-12 col-md-6">
                        <div class="blog-single-thumb overflow-hidden rounded-4">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php
                                the_post_thumbnail('large', [
                                    'class' => 'w-100 img-fluid object-fit-cover blog-single-img',
                                ]);
                                ?>
                            <?php else : ?>
                                <img
                                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/no-image.png'); ?>"
                                    class="w-100 img-fluid object-fit-cover blog-single-img"
                                    alt="<?php echo esc_attr(get_the_title()); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <!-- Meta -->
                        <div class="blog-single-meta mb-3 small">
                            <div class="mb-2">
                                <?php
                                // Event categories as pills: show 2 + dropdown (+N)
                                if (!empty($terms) && !is_wp_error($terms)) {
                                    $max_show = 2;
                                    $shown    = array_slice($terms, 0, $max_show);
                                    $hidden   = array_slice($terms, $max_show);
                                    foreach ($shown as $t) {
                                        $term_link = get_term_link($t);
                                        if (is_wp_error($term_link)) {
                                            continue;
                                        }
                                        echo '<a class="blog-meta-pill" href="' . esc_url($term_link) . '">' . esc_html($t->name) . '</a> ';
                                    }
                                    if (!empty($hidden)) {
                                        $dropdown_id = 'eventCatMore-' . $event_id;
                                        echo '<span class="dropdown d-inline-block align-middle">';
                                        echo '  <a  class="blog-meta-pill is-muted dropdown-toggle"
                                                    href="#"
                                                    id="' . esc_attr($dropdown_id) . '"
                                                    role="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                    aria-label="' . esc_attr__('More categories', 'test-wordpress') . '"
                                                    style="padding:6px 12px; border-radius:999px; text-decoration:none;">
                                                    +' . esc_html(count($hidden)) . '
                                                </a>';
                                        echo '  <ul class="dropdown-menu p-2"
                                                    aria-labelledby="' . esc_attr($dropdown_id) . '"
                                                    style="
                                                        min-width: 160px;
                                                        border-radius: 14px;
                                                        border: 1px solid rgba(15,23,42,0.10);
                                                        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                                                    ">';
                                        foreach ($hidden as $t) {
                                            $term_link = get_term_link($t);
                                            if (is_wp_error($term_link)) {
                                                continue;
                                            }
                                            echo '<li>
                                                    <a  class="dropdown-item"
                                                        href="' . esc_url($term_link) . '"
                                                        style="border-radius:999px; padding:8px 12px; font-weight:600;">
                                                        ' . esc_html($t->name) . '
                                                    </a>
                                                </li>';
                                        }
                                        echo '  </ul>';
                                        echo '</span>';
                                    }
                                } else {
                                    echo '<span class="blog-meta-pill is-muted">' . esc_html__('No Category', 'test-wordpress') . '</span>';
                                }
                                ?>
                            </div>
                            <div class="text-muted">
                                <?php if ($start_text) : ?>
                                    <span><?php echo esc_html($start_text); ?></span>
                                    <?php if ($end_text) : ?>
                                        <span class="mx-2">–</span><span><?php echo esc_html($end_text); ?></span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span><?php echo esc_html__('Date TBA', 'test-wordpress'); ?></span>
                                <?php endif; ?>
                                <span class="mx-2">•</span>
                                <span><?php echo esc_html(get_bloginfo('name')); ?></span>
                            </div>
                        </div>
                        <!-- Title -->
                        <h1 class="blog-single-title mb-3">
                            <?php the_title(); ?>
                        </h1>
                        <!-- Excerpt (optional) -->
                        <?php if (has_excerpt()) : ?>
                            <p class="blog-single-excerpt mb-0">
                                <?php echo esc_html(get_the_excerpt()); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Content -->
                <div class="blog-single-content">
                    <?php the_content(); ?>
                </div>
            </article>
            <!-- Related Events -->
            <section class="mx-auto blog-related-wrap mt-5 pt-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0"><?php esc_html_e('Related Events', 'test-wordpress'); ?></h3>
                </div>
                <div class="row g-4">
                    <?php
                    $related_args = [
                        'post_type'              => 'event',
                        'posts_per_page'         => 3,
                        'post__not_in'           => [$event_id],
                        'ignore_sticky_posts'    => true,
                        'no_found_rows'          => true,
                        'update_post_meta_cache' => false,
                        'update_post_term_cache' => false,
                    ];
                    if (!empty($term_ids)) {
                        $related_args['tax_query'] = [
                            [
                                'taxonomy' => 'event_category',
                                'field'    => 'term_id',
                                'terms'    => $term_ids,
                            ],
                        ];
                    }
                    $related = new WP_Query($related_args);
                    if ($related->have_posts()) :
                        while ($related->have_posts()) : $related->the_post();
                    ?>
                            <div class="col-12 col-md-4">
                                <div class="blog-related-card h-100">
                                    <?php get_template_part('template-parts/thumb-item-event'); ?>
                                </div>
                            </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                        <div class="col-12">
                            <p class="text-muted mb-0"><?php esc_html_e('No related events.', 'test-wordpress'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        <?php endwhile; endif; ?>
    </div>
</main>
<?php get_footer(); ?>