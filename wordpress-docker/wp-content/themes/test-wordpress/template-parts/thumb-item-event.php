<a
    href="<?php the_permalink(); ?>"
    class="thumb-item card border-0 w-100 h-100 blog-thumb"
    aria-label="<?php echo esc_attr(get_the_title()); ?>"
>
    <div class="ratio ratio-4x3 overflow-hidden blog-thumb-media">
        <?php if (has_post_thumbnail()) : ?>
            <?php
            the_post_thumbnail('medium_large', [
                'class' => 'img-fluid object-fit-cover w-100 h-100 blog-thumb-img',
            ]);
            ?>
        <?php else : ?>
            <img
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/no-image.png'); ?>"
                class="img-fluid object-fit-cover w-100 h-100 blog-thumb-img"
                alt="<?php echo esc_attr(get_the_title()); ?>">
        <?php endif; ?>
        <span class="blog-thumb-overlay" aria-hidden="true"></span>
    </div>
    <div class="card-body p-3 p-lg-4">
        <p class="card-title fw-semibold mb-1 blog-thumb-title">
            <?php echo esc_html(get_the_title()); ?>
        </p>
        <p class="card-text small mb-2 blog-thumb-excerpt">
            <?php
            $excerpt = get_the_excerpt();
            if (!$excerpt) {
                $excerpt = wp_strip_all_tags(get_the_content());
            }
            echo esc_html(wp_trim_words($excerpt, 15));
            ?>
        </p>
        <div class="d-flex align-items-center justify-content-between">
            <p class="small mb-0 blog-thumb-date">
                <?php
                $event_id   = get_the_ID();
                $start      = get_post_meta($event_id, '_event_start', true);
                $end        = get_post_meta($event_id, '_event_end', true);
                $start_text = ($start && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start))
                                ? date_i18n('d M Y', strtotime($start))
                                : '';
                $end_text   = ($end && preg_match('/^\d{4}-\d{2}-\d{2}$/', $end))
                                ? date_i18n('d M Y', strtotime($end))
                                : '';
                if ($start_text) {
                    echo esc_html($start_text);
                    if ($end_text) {
                        echo ' – ' . esc_html($end_text);
                    }
                } else {
                    echo esc_html__('Date TBA', 'test-wordpress');
                }
                ?>
            </p>
            <?php
            $terms = get_the_terms(get_the_ID(), 'event_category');
            if (!empty($terms) && !is_wp_error($terms)) :
                $max_show = 2;
                $shown    = array_slice($terms, 0, $max_show);
                $extra    = count($terms) - count($shown);
            ?>
                <div class="blog-thumb-cats">
                    <?php foreach ($shown as $t) : ?>
                        <span class="blog-thumb-pill"><?php echo esc_html($t->name); ?></span>
                    <?php endforeach; ?>

                    <?php if ($extra > 0) : ?>
                        <span class="blog-thumb-pill is-muted">+<?php echo esc_html($extra); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</a>