<a href="<?php the_permalink(); ?>" class="thumb-item card border-0 w-100 h-100 blog-thumb">
    <div class="ratio ratio-4x3 overflow-hidden blog-thumb-media">
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium_large', [
                'class' => 'img-fluid object-fit-cover w-100 h-100 blog-thumb-img'
            ]); ?>
        <?php else : ?>
            <img
                src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/01.png'); ?>"
                class="img-fluid object-fit-cover w-100 h-100 blog-thumb-img"
                alt="<?php echo esc_attr(get_the_title()); ?>">
        <?php endif; ?>
        <span class="blog-thumb-overlay" aria-hidden="true"></span>
    </div>
    <div class="card-body p-3 p-lg-4">
        <p class="card-title fw-semibold mb-1 blog-thumb-title">
            <?php the_title(); ?>
        </p>
        <p class="card-text small mb-2 blog-thumb-excerpt">
            <?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?>
        </p>
        <div class="d-flex align-items-center justify-content-between">
            <p class="small mb-0 blog-thumb-date">
                <?php echo esc_html(get_the_date('d M Y')); ?>
            </p>
            <?php
            $cats = get_the_category();
            if (!empty($cats)) :
                $max_show = 2;
                $shown    = array_slice($cats, 0, $max_show);
                $extra    = count($cats) - count($shown);
            ?>
                <div class="blog-thumb-cats">
                    <?php foreach ($shown as $c) : ?>
                        <span class="blog-thumb-pill"><?php echo esc_html($c->name); ?></span>
                    <?php endforeach; ?>
                    <?php if ($extra > 0) : ?>
                        <span class="blog-thumb-pill is-muted">+<?php echo esc_html($extra); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</a>