<footer id="colophon" class="site-footer text-white pt-5 pb-3">
    <div class="container mb-4">
        <div class="row">
            <!-- Post Categories -->
            <div class="col-12 col-md-6 mb-4 mb-md-0">
                <button
                    class="btn btn-link text-decoration-none text-white text-uppercase fw-bold p-0 mb-2 d-md-none"
                    data-bs-toggle="collapse"
                    data-bs-target="#footerPostCat"
                    aria-expanded="false"
                    aria-controls="footerPostCat">
                    <?php esc_html_e('Post Category', 'test-wordpress'); ?>
                </button>
                <h6 class="text-uppercase fw-bold mb-2 d-none d-md-block">
                    <?php esc_html_e('Post Category', 'test-wordpress'); ?>
                </h6>
                <ul id="footerPostCat" class="list-unstyled collapse d-md-block mb-0 pt-2">
                    <?php
                    $categories = get_categories([
                        'orderby' => 'name',
                        'order'   => 'ASC',
                    ]);
                    if (!empty($categories) && !is_wp_error($categories)) :
                        foreach ($categories as $cat) :
                            $cat_link = get_category_link($cat->term_id);
                            if (is_wp_error($cat_link)) {
                                continue;
                            }
                    ?>
                            <li class="mb-2">
                                <a
                                    href="<?php echo esc_url($cat_link); ?>"
                                    class="text-white text-decoration-none footer-link">
                                    <?php echo esc_html($cat->name); ?>
                                </a>
                            </li>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <li>
                            <span class="text-white-50">
                                <?php esc_html_e('No Post Category', 'test-wordpress'); ?>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Event Categories -->
            <div class="col-12 col-md-6">
                <button
                    class="btn btn-link text-decoration-none text-white text-uppercase fw-bold p-0 mb-2 d-md-none"
                    data-bs-toggle="collapse"
                    data-bs-target="#footerEventCat"
                    aria-expanded="false"
                    aria-controls="footerEventCat">
                    <?php esc_html_e('Event Category', 'test-wordpress'); ?>
                </button>
                <h6 class="text-uppercase fw-bold mb-2 d-none d-md-block">
                    <?php esc_html_e('Event Category', 'test-wordpress'); ?>
                </h6>
                <ul id="footerEventCat" class="list-unstyled collapse d-md-block mb-0 pt-2">
                    <?php
                    $event_cats = get_terms([
                        'taxonomy'   => 'event_category',
                        'hide_empty' => false,
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                    ]);
                    if (!empty($event_cats) && !is_wp_error($event_cats)) :
                        foreach ($event_cats as $event_cat) :
                            $term_link = get_term_link($event_cat);
                            if (is_wp_error($term_link)) {
                                continue;
                            }
                    ?>
                            <li class="mb-2">
                                <a
                                    href="<?php echo esc_url($term_link); ?>"
                                    class="text-white text-decoration-none footer-link">
                                    <?php echo esc_html($event_cat->name); ?>
                                </a>
                            </li>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <li>
                            <span class="text-white-50">
                                <?php esc_html_e('No Event Category', 'test-wordpress'); ?>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- Copyright -->
    <div class="container text-center text-md-end footer-bottom">
        <p class="mb-0 small">
            &copy; <?php echo esc_html(wp_date('Y')); ?>
            <?php echo esc_html(get_bloginfo('name')); ?>.
            <?php esc_html_e('All rights reserved.', 'test-wordpress'); ?>
        </p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
