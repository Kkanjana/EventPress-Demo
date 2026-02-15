<?php
get_header();
?>

<main id="primary" class="site-main blog-index py-5">

    <div class="container">
        <div class="mx-auto">

            <!-- HERO / SWIPER -->
            <section class="mb-5">
                <div class="swiper mySwiper mb-5">
                    <div class="swiper-wrapper">

                        <div class="swiper-slide">
                            <div class="position-relative ratio ratio-16x9 overflow-hidden rounded-4 blog-card">
                                <div class="d-none d-md-block">
                                    <video class="w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
                                        <source src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/placeholder_vdo.mp4'); ?>" type="video/mp4">
                                    </video>
                                </div>
                                <div class="d-block d-md-none">
                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/01.png'); ?>"
                                        class="img-fluid w-100 h-100 object-fit-cover"
                                        alt="">
                                </div>

                                <div class="swiper-caption text-center position-absolute bottom-0 start-0 w-100 p-4 text-white user-select-none d-flex flex-column align-items-center justify-content-end">
                                    <h3 class="mb-1 fw-bold">Slide 1</h3>
                                    <p class="mb-0 opacity-75">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide">
                            <div class="position-relative ratio ratio-16x9 overflow-hidden rounded-4 blog-card">
                                <div class="d-none d-md-block">
                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/02.png'); ?>"
                                        class="img-fluid w-100 h-100 object-fit-cover"
                                        alt="">
                                </div>
                                <div class="d-block d-md-none">
                                    <video class="w-100 h-100 object-fit-cover" autoplay muted loop playsinline>
                                        <source src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/placeholder_vdo.mp4'); ?>" type="video/mp4">
                                    </video>
                                </div>

                                <div class="swiper-caption text-center position-absolute bottom-0 start-0 w-100 p-4 text-white user-select-none d-flex flex-column align-items-center justify-content-end">
                                    <h3 class="mb-1 fw-bold">Slide 2</h3>
                                    <p class="mb-0 opacity-75">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                </div>
                            </div>
                        </div>

                        <div class="swiper-slide">
                            <div class="position-relative ratio ratio-16x9 overflow-hidden rounded-4 blog-card">
                                <div class="d-none d-md-block">
                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/03.png'); ?>"
                                        class="img-fluid w-100 h-100 object-fit-cover"
                                        alt="">
                                </div>
                                <div class="d-block d-md-none">
                                    <img
                                        src="<?php echo esc_url(get_template_directory_uri() . '/assets/media/04.png'); ?>"
                                        class="img-fluid w-100 h-100 object-fit-cover"
                                        alt="">
                                </div>

                                <div class="swiper-caption text-center position-absolute bottom-0 start-0 w-100 p-4 text-white user-select-none d-flex flex-column align-items-center justify-content-end">
                                    <h3 class="mb-1 fw-bold">Slide 3</h3>
                                    <p class="mb-0 opacity-75">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
            </section>

            <!-- POST CATEGORIES (TABS) -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0">Post Categories</h3>
                </div>

                <?php
                $categories = get_categories(['orderby' => 'name', 'order' => 'ASC']);
                if (!empty($categories)) :
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
                                    ]);

                                    if ($posts->have_posts()) :
                                        while ($posts->have_posts()) : $posts->the_post();
                                    ?>
                                            <div class="col-12 col-sm-6 col-lg-3">
                                                <?php get_template_part('template-parts/thumb-item'); ?>
                                            </div>
                                    <?php
                                        endwhile;
                                    else :
                                    ?>
                                        <div class="col-12">
                                            <p class="text-muted mb-0">No posts found.</p>
                                        </div>
                                    <?php
                                    endif;
                                    wp_reset_postdata();
                                    ?>
                                </div>

                                <div class="text-center mt-4">
                                    <a class="btn btn-outline-primary" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
                                        View more in <?php echo esc_html($cat->name); ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="text-muted mb-0">No categories found.</p>
                <?php endif; ?>
            </section>

            <!-- RECENT EVENTS -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0">Recent Events</h3>
                    <a class="btn btn-outline-primary btn-sm" href="<?php echo esc_url(get_post_type_archive_link('event')); ?>">
                        View All Events
                    </a>
                </div>

                <div class="row g-4">
                    <?php
                    $events = new WP_Query([
                        'post_type'           => 'event',
                        'posts_per_page'      => 3,
                        'meta_key'            => '_event_start',
                        'orderby'             => 'meta_value',
                        'order'               => 'ASC',
                        'meta_type'           => 'DATE',
                        'ignore_sticky_posts' => true,
                    ]);

                    if ($events->have_posts()) :
                        while ($events->have_posts()) : $events->the_post();
                    ?>
                            <div class="col-12 col-md-4">
                                <?php get_template_part('template-parts/thumb-item-event'); ?>
                            </div>
                    <?php
                        endwhile;
                    else :
                    ?>
                        <div class="col-12">
                            <p class="text-muted mb-0">No events found.</p>
                        </div>
                    <?php
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>
            </section>

            <!-- LOCATION -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0">Location</h3>
                </div>

                <div class="row g-4 align-items-stretch">
                    <div class="col-12 col-lg-6">
                        <div class="d-flex flex-column gap-3 h-100">
                            <div class="card p-3 border-0 rounded-4 blog-card">
                                <h6 class="mb-1 fw-bold">Bangkok Office</h6>
                                <p class="small text-muted mb-2">Address</p>
                                <a href="#" class="small text-decoration-none" style="color: var(--primary-color);">
                                    View on map
                                </a>
                            </div>

                            <div class="card p-3 border-0 rounded-4 blog-card">
                                <h6 class="mb-1 fw-bold">Chiang Mai Office</h6>
                                <p class="small text-muted mb-2">Address</p>
                                <a href="#" class="small text-decoration-none" style="color: var(--primary-color);">
                                    View on map
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="ratio ratio-16x9 h-100 rounded-4 overflow-hidden blog-card">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.57560607019!2d100.53903597509613!3d13.744124897471206!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x311d617707559737%3A0x1df4ed57b0a68f56!2sPlaimanas%20%3A%3A*2A!5e0!3m2!1sen!2sth!4v1770285986911!5m2!1sen!2sth"
                                width="600"
                                height="450"
                                style="border:0;"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FAQ (placeholder - can be converted to repeater later) -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0">FAQ</h3>
                </div>

                <div class="accordion rounded-4 overflow-hidden" id="accordionExample">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Accordion Item #1
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.</p>
                                <ul>
                                    <li>list 1</li>
                                    <li>list 2</li>
                                    <li>list 3</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Accordion Item #2
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.</p>
                                <ul>
                                    <li>list 1</li>
                                    <li>list 2</li>
                                    <li>list 3</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Accordion Item #3
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit.</p>
                                <ul>
                                    <li>list 1</li>
                                    <li>list 2</li>
                                    <li>list 3</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTACT (placeholder) -->
            <section class="mb-5">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="mb-0">Contact Us</h3>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-12 col-md-4">
                        <div class="h-100 p-4 border-0 rounded-4 text-center blog-card">
                            <h6 class="mb-2 fw-bold">Address</h6>
                            <p class="mb-1 small text-muted">
                                123 ถนนสุขุมวิท<br>
                                เขตวัฒนา กรุงเทพฯ 10110
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="h-100 p-4 border-0 rounded-4 text-center blog-card">
                            <h6 class="mb-2 fw-bold">Contact</h6>
                            <p class="mb-1 small">
                                <a href="tel:+6612345678" class="text-decoration-none" style="color: var(--primary-color);">
                                    +66 12 345 678
                                </a>
                            </p>
                            <p class="mb-1 small">
                                <a href="mailto:info@example.com" class="text-decoration-none" style="color: var(--primary-color);">
                                    info@example.com
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="h-100 p-4 border-0 rounded-4 text-center blog-card">
                            <h6 class="mb-2 fw-bold">Follow Us</h6>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Facebook</a>
                                <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Instagram</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-4 border-0 rounded-4 blog-card">
                    <h5 class="fw-bold mb-3">Contact Form</h5>
                    <form>
                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Your name">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Company</label>
                                <input type="text" class="form-control" placeholder="Your Company">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" placeholder="Your phone">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Your email">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" rows="4" placeholder="Your message"></textarea>
                            </div>

                            <div class="col-12">
                                <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#submitSuccessModal">
                                    Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal fade" id="submitSuccessModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-center">
                            <div class="modal-header border-0">
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body pb-4">
                                <h4 class="mb-2">Thank You!</h4>
                                <p class="text-muted mb-0">
                                    Your message has been successfully sent.<br>
                                    We will get back to you shortly.
                                </p>
                            </div>
                            <div class="modal-footer border-0 justify-content-center">
                                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </div>
    </div>

</main>

<?php
get_footer();
?>