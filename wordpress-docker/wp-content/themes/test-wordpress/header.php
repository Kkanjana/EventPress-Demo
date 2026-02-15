<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
    <header id="site-header" class="site-header shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
            <div class="container">
                <!-- Brand -->
                <a class="navbar-brand fw-bold text-dark d-flex align-items-center" href="<?php echo esc_url(home_url('/')); ?>">
                    <?php
                    if (function_exists('the_custom_logo') && has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        bloginfo('name');
                    }
                    ?>
                </a>
                <!-- Mobile Toggle -->
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNav"
                    aria-controls="navbarNav"
                    aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Menu + Search -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php if (has_nav_menu('primary')) : ?>
                        <?php
                        wp_nav_menu([
                            'theme_location' => 'primary',
                            'container'      => false,
                            'menu_class'     => 'navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-4',
                            'fallback_cb'    => false,
                            'depth'          => 2,
                            'walker'         => new Bootstrap_5_WP_Nav_Menu_Walker(),
                        ]);
                        ?>
                    <?php else : ?>
                        <!-- Fallback: show pages if menu not assigned yet -->
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 gap-lg-4">
                            <?php wp_list_pages([
                                'title_li' => '',
                                'depth'    => 1,
                            ]); ?>
                        </ul>
                    <?php endif; ?>
                    <!-- Search (no button) -->
                    <form
                        class="d-flex ms-lg-4 mt-3 mt-lg-0"
                        role="search"
                        method="get"
                        action="<?php echo esc_url(home_url('/')); ?>">
                        <label class="visually-hidden" for="header-search">
                            <?php esc_html_e('Search', 'your-textdomain'); ?>
                        </label>
                        <input
                            id="header-search"
                            type="search"
                            name="s"
                            class="form-control rounded-pill px-3"
                            placeholder="<?php echo esc_attr__('Search...', 'your-textdomain'); ?>"
                            value="<?php echo esc_attr(get_search_query()); ?>">
                    </form>
                </div>
            </div>
        </nav>
    </header>