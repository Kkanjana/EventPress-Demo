<?php get_header(); ?>
<main class="single-post py-5 blog-single">
    <div class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <article class="mx-auto blog-single-wrap">
                <!-- Hero -->
                <div class="row g-4 align-items-start mb-4">
                    <div class="col-12 col-md-6">
                        <div class="blog-single-thumb overflow-hidden rounded-4">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', [
                                    'class' => 'w-100 img-fluid object-fit-cover blog-single-img',
                                ]); ?>
                            <?php else : ?>
                                <img
                                    src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/01.png'); ?>"
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
                                $cats = get_the_category();
                                if (!empty($cats)) {
                                    $max_show = 2;
                                    $shown    = array_slice($cats, 0, $max_show);
                                    $hidden   = array_slice($cats, $max_show);
                                    // show first 1-2 categories as pills (clickable)
                                    foreach ($shown as $c) {
                                        echo '<a class="blog-meta-pill" href="' . esc_url(get_category_link($c->term_id)) . '">' . esc_html($c->name) . '</a> ';
                                    }
                                    // if there are more, show +N as a pill dropdown
                                    if (!empty($hidden)) {
                                        $dropdown_id = 'catMore-' . get_the_ID(); // unique id per post
                                        echo '<span class="dropdown d-inline-block align-middle">';
                                        echo '  <a  class="blog-meta-pill is-muted dropdown-toggle"
                                                    href="#"
                                                    id="' . esc_attr($dropdown_id) . '"
                                                    role="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                    style="padding:6px 12px; border-radius:999px; text-decoration:none;">
                                                    +' . esc_html(count($hidden)) . '
                                                </a>';
                                        // Small pill-like dropdown menu
                                        echo '  <ul class="dropdown-menu p-2"
                                                    aria-labelledby="' . esc_attr($dropdown_id) . '"
                                                    style="
                                                        min-width: 160px;
                                                        border-radius: 14px;
                                                        border: 1px solid rgba(15,23,42,0.10);
                                                        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                                                    ">';
                                        foreach ($hidden as $c) {
                                            echo '<li>
                                                    <a  class="dropdown-item"
                                                        href="' . esc_url(get_category_link($c->term_id)) . '"
                                                        style="
                                                            border-radius: 999px;
                                                            padding: 8px 12px;
                                                            font-weight: 600;
                                                        ">
                                                        ' . esc_html($c->name) . '
                                                    </a>
                                                </li>';
                                        }
                                        echo '  </ul>';
                                        echo '</span>';
                                    }
                                } else {
                                    echo '<span class="blog-meta-pill is-muted">Uncategorized</span>';
                                }
                                ?>
                            </div>
                            <div class="text-muted">
                                <span><?php echo esc_html(get_the_date('d M Y')); ?></span>
                                <span class="mx-2">•</span>
                                <span><?php echo esc_html(get_bloginfo('name')); ?></span>
                            </div>
                        </div>
                        <!-- Title -->
                        <h1 class="blog-single-title mb-3">
                            <?php the_title(); ?>
                        </h1>
                        <!-- Excerpt (optional, looks more like real blog) -->
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
        <?php endwhile; endif; ?>
        <!-- Related -->
        <section class="mx-auto blog-related-wrap mt-5 pt-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h3 class="mb-0">Related Posts</h3>
            </div>
            <div class="row g-4">
                <?php
                $categories = wp_get_post_categories(get_the_ID());
                $related    = new WP_Query([
                    'category__in'        => $categories,
                    'post__not_in'        => [get_the_ID()],
                    'posts_per_page'      => 3,
                    'ignore_sticky_posts' => true,
                ]);
                if ($related->have_posts()) :
                    while ($related->have_posts()) : $related->the_post();
                ?>
                        <div class="col-12 col-md-4">
                            <div class="blog-related-card h-100">
                                <?php get_template_part('template-parts/thumb-item'); ?>
                            </div>
                        </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                    <div class="col-12">
                        <p class="text-muted mb-0">No related posts found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>
<?php get_footer(); ?>