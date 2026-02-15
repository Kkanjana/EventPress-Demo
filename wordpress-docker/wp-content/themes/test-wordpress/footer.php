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
                    Post Category
                </button>
                <h6 class="text-uppercase fw-bold mb-2 d-none d-md-block">
                    Post Category
                </h6>
                <ul id="footerPostCat" class="list-unstyled collapse d-md-block mb-0 pt-2">
                    <?php
                    $categories = get_categories([
                        'orderby' => 'name',
                        'order'   => 'ASC',
                    ]);
                    if (!empty($categories)) :
                        foreach ($categories as $cat) :
                    ?>
                            <li class="mb-2">
                                <a
                                    href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
                                    class="text-white text-decoration-none footer-link">
                                    <?php echo esc_html($cat->name); ?>
                                </a>
                            </li>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <li><span class="text-white-50">No Post Category</span></li>
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
                    Event Category
                </button>
                <h6 class="text-uppercase fw-bold mb-2 d-none d-md-block">
                    Event Category
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
                    ?>
                            <li class="mb-2">
                                <a
                                    href="<?php echo esc_url(get_term_link($event_cat)); ?>"
                                    class="text-white text-decoration-none footer-link">
                                    <?php echo esc_html($event_cat->name); ?>
                                </a>
                            </li>
                    <?php
                        endforeach;
                    else :
                    ?>
                        <li><span class="text-white-50">No Event Category</span></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- Copyright -->
    <div class="container text-center text-md-end footer-bottom">
        <p class="mb-0 small">
            &copy; <?php echo esc_html(date('Y')); ?> <?php bloginfo('name'); ?>. All rights reserved.
        </p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>