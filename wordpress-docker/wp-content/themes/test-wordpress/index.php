<?php get_header(); ?>
<main id="primary" class="site-main blog-index py-5">
    <div class="container">
        <div class="mx-auto">
            <!-- HERO / SWIPER (Fill to 3: Upcoming Events first, then Featured Posts by Tag) -->
            <section class="mb-5">
                <?php
                $today  = current_time('Y-m-d');
                $slides = [];
                // 1) Upcoming Events (priority)
                $event_q = new WP_Query([
                    'post_type'           => 'event',
                    'posts_per_page'      => 3,
                    'ignore_sticky_posts' => true,
                    'meta_key'            => '_event_start',
                    'orderby'             => 'meta_value',
                    'meta_type'           => 'DATE',
                    'order'               => 'ASC',
                    'meta_query'          => [
                        [
                            'key'     => '_event_start',
                            'value'   => $today,
                            'compare' => '>=',
                            'type'    => 'DATE',
                        ],
                    ],
                    'fields'              => 'ids',
                    'no_found_rows'       => true,
                ]);
                if (!empty($event_q->posts)) {
                    foreach ($event_q->posts as $id) {
                        $slides[] = ['id' => (int) $id, 'type' => 'event'];
                        if (count($slides) >= 3) break;
                    }
                }
                // 2) Fill remaining with Featured Posts (Tag: featured)
                if (count($slides) < 3) {
                    $need   = 3 - count($slides);
                    $post_q = new WP_Query([
                        'post_type'           => 'post',
                        'posts_per_page'      => $need,
                        'ignore_sticky_posts' => true,
                        'tag'                 => 'featured',
                        'orderby'             => 'date',
                        'order'               => 'DESC',
                        'fields'              => 'ids',
                        'no_found_rows'       => true,
                    ]);
                    if (!empty($post_q->posts)) {
                        foreach ($post_q->posts as $id) {
                            $slides[] = ['id' => (int) $id, 'type' => 'post'];
                            if (count($slides) >= 3) break;
                        }
                    }
                }
                ?>
                <?php if (!empty($slides)) : ?>
                    <div class="swiper mySwiper mb-5">
                        <div class="swiper-wrapper">
                            <?php foreach ($slides as $s) : ?>
                                <?php
                                $pid   = (int) $s['id'];
                                $type  = $s['type'];
                                $label = ($type === 'event')
                                        ? esc_html__('UPCOMING EVENT', 'test-wordpress')
                                        : esc_html__('FEATURED POST', 'test-wordpress');
                                $title = get_the_title($pid);
                                $link  = get_permalink($pid);
                                // Subtitle: first taxonomy term
                                $subtitle = '';
                                if ($type === 'event') {
                                    $terms = get_the_terms($pid, 'event_category');
                                    if (!empty($terms) && !is_wp_error($terms)) {
                                        $subtitle = $terms[0]->name;
                                    }
                                } else {
                                    $cats = get_the_category($pid);
                                    if (!empty($cats) && !is_wp_error($cats)) {
                                        $subtitle = $cats[0]->name;
                                    }
                                }
                                // Event date line
                                $date_line = '';
                                if ($type === 'event') {
                                    $start = get_post_meta($pid, '_event_start', true);
                                    $end   = get_post_meta($pid, '_event_end', true);
                                    if ($start) {
                                        $date_line = date_i18n('d M Y', strtotime($start));
                                        if ($end) {
                                            $date_line .= ' – ' . date_i18n('d M Y', strtotime($end));
                                        }
                                    }
                                }
                                // Excerpt (ไม่พึ่ง global post)
                                $raw = get_post_field('post_excerpt', $pid);
                                if (!$raw) {
                                    $raw = get_post_field('post_content', $pid);
                                }
                                $excerpt = wp_trim_words(wp_strip_all_tags($raw), 14);
                                // Image
                                if (has_post_thumbnail($pid)) {
                                    $img_html = get_the_post_thumbnail($pid, 'large', [
                                        'class' => 'img-fluid w-100 h-100 object-fit-cover',
                                    ]);
                                } else {
                                    $img_html = '<img src="' . esc_url(get_template_directory_uri() . '/assets/media/no-image.png') . '" class="img-fluid w-100 h-100 object-fit-cover" alt="' . esc_attr($title) . '">';
                                }
                                ?>
                                <div class="swiper-slide">
                                    <a href="<?php echo esc_url($link); ?>" class="d-block text-decoration-none">
                                        <div class="position-relative hero-banner overflow-hidden rounded-4 blog-card">
                                            <?php echo wp_kses_post($img_html); ?>
                                            <div class="swiper-caption text-center position-absolute bottom-0 start-0 w-100 p-4 text-white user-select-none d-flex flex-column align-items-center justify-content-end">
                                                <div class="d-flex gap-2 justify-content-center mb-2">
                                                    <span class="blog-meta-pill is-glass">
                                                        <?php echo esc_html($label); ?>
                                                    </span>
                                                    <?php if (!empty($subtitle)) : ?>
                                                        <span class="blog-meta-pill is-glass-soft">
                                                            <?php echo esc_html($subtitle); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <h3 class="mb-1 fw-bold text-white">
                                                    <?php echo esc_html($title); ?>
                                                </h3>
                                                <?php if (!empty($date_line)) : ?>
                                                    <p class="mb-2 small opacity-75"><?php echo esc_html($date_line); ?></p>
                                                <?php endif; ?>
                                                <p class="mb-0 opacity-75">
                                                    <?php echo esc_html($excerpt); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                <?php else : ?>
                    <div class="p-4 rounded-4 blog-card text-center">
                        <p class="text-muted mb-0"><?php esc_html_e('No featured content yet.', 'test-wordpress'); ?></p>
                    </div>
                <?php endif; ?>
            </section>
            <!-- POST CATEGORIES (TABS) -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0"><?php esc_html_e('Post Categories', 'test-wordpress'); ?></h3>
                </div>
                <?php
                // เบาหน้าแรก: หมวดที่มีโพสต์ + จำกัดจำนวน
                $categories = get_categories([
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                    'hide_empty' => true,
                    'number'     => 6,
                ]);
                if (!empty($categories) && !is_wp_error($categories)) :
                ?>
                    <ul class="nav nav-tabs mb-3" id="postCatsTab" role="tablist">
                        <?php foreach ($categories as $i => $cat) : ?>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link <?php echo $i === 0 ? 'active' : ''; ?>"
                                    id="cat-<?php echo (int) $cat->term_id; ?>-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#cat-<?php echo (int) $cat->term_id; ?>"
                                    type="button"
                                    role="tab"
                                    aria-controls="cat-<?php echo (int) $cat->term_id; ?>"
                                    aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                                    <?php echo esc_html($cat->name); ?>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="tab-content" id="postCatsTabContent">
                        <?php foreach ($categories as $i => $cat) : ?>
                            <div
                                class="tab-pane fade <?php echo $i === 0 ? 'show active' : ''; ?>"
                                id="cat-<?php echo (int) $cat->term_id; ?>"
                                role="tabpanel"
                                aria-labelledby="cat-<?php echo (int) $cat->term_id; ?>-tab">
                                <div class="row g-4 pt-3">
                                    <?php
                                    $posts = new WP_Query([
                                        'post_type'           => 'post',
                                        'cat'                 => (int) $cat->term_id,
                                        'posts_per_page'      => 4,
                                        'ignore_sticky_posts' => true,
                                        'no_found_rows'       => true,
                                    ]);
                                    if ($posts->have_posts()) :
                                        while ($posts->have_posts()) :
                                            $posts->the_post();
                                    ?>
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <?php get_template_part('template-parts/thumb-item'); ?>
                                            </div>
                                    <?php
                                        endwhile;
                                    else :
                                    ?>
                                        <div class="col-12">
                                            <p class="text-muted mb-0"><?php esc_html_e('No posts found.', 'test-wordpress'); ?></p>
                                        </div>
                                    <?php
                                    endif;
                                    wp_reset_postdata();
                                    ?>
                                </div>
                                <div class="text-center mt-4">
                                    <a class="btn btn-outline-primary" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                        <?php
                                        printf(
                                            esc_html__('View more in %s', 'test-wordpress'),
                                            esc_html($cat->name)
                                        );
                                        ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="text-muted mb-0"><?php esc_html_e('No categories found.', 'test-wordpress'); ?></p>
                <?php endif; ?>
            </section>
            <!-- RECENT EVENTS -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0"><?php esc_html_e('Recent Events', 'test-wordpress'); ?></h3>
                    <a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url(get_post_type_archive_link('event')); ?>">
                        <?php esc_html_e('View All Events', 'test-wordpress'); ?>
                    </a>
                </div>
                <?php
                // Upcoming first, fallback to latest past events if none
                $today  = current_time('Y-m-d');
                $events = new WP_Query([
                    'post_type'           => 'event',
                    'posts_per_page'      => 3,
                    'ignore_sticky_posts' => true,
                    'meta_key'            => '_event_start',
                    'orderby'             => 'meta_value',
                    'order'               => 'ASC',
                    'meta_type'           => 'DATE',
                    'meta_query'          => [
                        [
                            'key'     => '_event_start',
                            'value'   => $today,
                            'compare' => '>=',
                            'type'    => 'DATE',
                        ],
                    ],
                    'no_found_rows'       => true,
                ]);
                if (!$events->have_posts()) {
                    wp_reset_postdata();
                    $events = new WP_Query([
                        'post_type'           => 'event',
                        'posts_per_page'      => 3,
                        'ignore_sticky_posts' => true,
                        'meta_key'            => '_event_start',
                        'orderby'             => 'meta_value',
                        'order'               => 'DESC',
                        'meta_type'           => 'DATE',
                        'meta_query'          => [
                            [
                                'key'     => '_event_start',
                                'value'   => $today,
                                'compare' => '<=',
                                'type'    => 'DATE',
                            ],
                        ],
                        'no_found_rows'       => true,
                    ]);
                }
                ?>
                <div class="row g-4">
                    <?php if ($events->have_posts()) : ?>
                        <?php while ($events->have_posts()) : $events->the_post(); ?>
                            <div class="col-12 col-md-4">
                                <?php get_template_part('template-parts/thumb-item-event'); ?>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div class="col-12">
                            <p class="text-muted mb-0"><?php esc_html_e('No events found.', 'test-wordpress'); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php wp_reset_postdata(); ?>
                </div>
            </section>
            <!-- LOCATION -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0"><?php esc_html_e('Location', 'test-wordpress'); ?></h3>
                </div>
                <?php
                $title     = get_theme_mod('loc_bkk_title', esc_html__('Bangkok Office', 'test-wordpress'));
                $address   = get_theme_mod('loc_bkk_address', '');
                $map_url   = get_theme_mod('loc_bkk_map_url', '#');
                $embed_src = get_theme_mod('loc_map_embed_src', '');
                ?>
                <div class="row g-4 align-items-stretch">
                    <div class="col-12 col-lg-6">
                        <div class="card p-4 border-0 rounded-4 blog-card h-100">
                            <h6 class="mb-2 fw-bold"><?php echo esc_html($title); ?></h6>
                            <p class="small text-muted mb-3">
                                <?php echo nl2br(esc_html($address)); ?>
                            </p>
                            <?php if ($map_url && $map_url !== '#') : ?>
                                <a
                                    href="<?php echo esc_url($map_url); ?>"
                                    class="small text-decoration-none fw-semibold"
                                    target="_blank"
                                    rel="noopener">
                                    <?php esc_html_e('View on map →', 'test-wordpress'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="hero-banner rounded-4 overflow-hidden blog-card">
                            <?php if (!empty($embed_src)) : ?>
                                <iframe
                                    src="<?php echo esc_url($embed_src); ?>"
                                    width="100%"
                                    height="100%"
                                    style="border:0;"
                                    allowfullscreen=""
                                    loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            <?php else : ?>
                                <div class="h-100 d-flex align-items-center justify-content-center p-4">
                                    <p class="text-muted text-center mb-0">
                                        <?php esc_html_e('Please set Google Map embed URL in Appearance → Customize → Location', 'test-wordpress'); ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
            <!-- FAQ -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0"><?php esc_html_e('FAQ', 'test-wordpress'); ?></h3>
                </div>
                <?php
                $faq_query = new WP_Query([
                    'post_type'      => 'faq',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'ASC',
                    'no_found_rows'  => true,
                ]);
                ?>
                <?php if ($faq_query->have_posts()) : ?>
                    <div class="accordion rounded-4 overflow-hidden" id="homeFaqAccordion">
                        <?php $i = 0; ?>
                        <?php while ($faq_query->have_posts()) : $faq_query->the_post(); ?>
                            <?php
                            $is_open   = $i === 0 ? 'show' : '';
                            $collapsed = $i === 0 ? '' : 'collapsed';
                            ?>
                            <div class="accordion-item border-0">
                                <h2 class="accordion-header" id="faq-heading-<?php echo (int) $i; ?>">
                                    <button class="accordion-button <?php echo esc_attr($collapsed); ?>"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#faq-collapse-<?php echo (int) $i; ?>"
                                            aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                                            aria-controls="faq-collapse-<?php echo (int) $i; ?>">
                                        <?php the_title(); ?>
                                    </button>
                                </h2>
                                <div id="faq-collapse-<?php echo (int) $i; ?>"
                                    class="accordion-collapse collapse <?php echo esc_attr($is_open); ?>"
                                    aria-labelledby="faq-heading-<?php echo (int) $i; ?>"
                                    data-bs-parent="#homeFaqAccordion">
                                    <div class="accordion-body">
                                        <?php the_content(); ?>
                                    </div>
                                </div>
                            </div>
                            <?php $i++; ?>
                        <?php endwhile; ?>
                    </div>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p class="text-muted mb-0"><?php esc_html_e('No FAQs available.', 'test-wordpress'); ?></p>
                <?php endif; ?>
            </section>
            <!-- CONTACT -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0"><?php esc_html_e('Contact Us', 'test-wordpress'); ?></h3>
                </div>
                <?php
                $c_address  = get_theme_mod('contact_address', '');
                $c_phone    = get_theme_mod('contact_phone', '');
                $c_phoneTel = get_theme_mod('contact_phone_tel', '');
                $c_email    = get_theme_mod('contact_email', '');
                $c_fb       = get_theme_mod('contact_facebook', '#');
                $c_ig       = get_theme_mod('contact_instagram', '#');
                ?>
                <div class="row g-4">
                    <div class="col-12 col-md-4">
                        <div class="h-100 p-4 border-0 rounded-4 text-center blog-card">
                            <h6 class="mb-2 fw-bold"><?php esc_html_e('Address', 'test-wordpress'); ?></h6>
                            <p class="mb-1 small text-muted">
                                <?php echo nl2br(esc_html($c_address)); ?>
                            </p>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="h-100 p-4 border-0 rounded-4 text-center blog-card">
                            <h6 class="mb-2 fw-bold"><?php esc_html_e('Contact', 'test-wordpress'); ?></h6>
                            <?php if (!empty($c_phone) && !empty($c_phoneTel)) : ?>
                                <p class="mb-1 small">
                                    <a href="tel:<?php echo esc_attr($c_phoneTel); ?>" class="text-decoration-none">
                                        <?php echo esc_html($c_phone); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($c_email)) : ?>
                                <p class="mb-1 small">
                                    <a href="mailto:<?php echo esc_attr($c_email); ?>" class="text-decoration-none">
                                        <?php echo esc_html($c_email); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="h-100 p-4 border-0 rounded-4 text-center blog-card">
                            <h6 class="mb-2 fw-bold"><?php esc_html_e('Follow Us', 'test-wordpress'); ?></h6>
                            <div class="d-flex justify-content-center gap-3">
                                <?php if (!empty($c_fb) && $c_fb !== '#') : ?>
                                    <a href="<?php echo esc_url($c_fb); ?>" target="_blank" rel="noopener" class="text-decoration-none">
                                        <?php esc_html_e('Facebook', 'test-wordpress'); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($c_ig) && $c_ig !== '#') : ?>
                                    <a href="<?php echo esc_url($c_ig); ?>" target="_blank" rel="noopener" class="text-decoration-none">
                                        <?php esc_html_e('Instagram', 'test-wordpress'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
<?php get_footer(); ?>