<?php get_header(); ?>
    <main class="main_content">
        <div class="container">
            <div class="row subpage_header">
                <div class="col-12">
                    <?php the_breadcrumb(); ?>
                    <h1 class="h3  entry-title" itemprop="name"><?php
                        $allsearch = new WP_Query(
                            array(
                                'posts_per_page' => -1,
                                's'              => $s,
                            )
                        );
                        $key       = esc_html($s, 1);
                        $count     = $allsearch->post_count;
                        _e('');
                        echo $count . ' ';
                        wp_reset_query();
                        ?> <?php _e('wyników dla', 'polski-podarek'); ?> <strong><?php echo $key; ?></strong></h1>
                </div>
            </div>

            <div class="row">
<!---->
<!--                --><?php //do_action( 'woocommerce_ordering' ); ?>


                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                    <?php if (get_post_type() == 'product') { ?>

                        <?php wc_get_template_part('content', 'product'); ?>

                    <?php } else { ?>

                    <?php } ?>

                <?php endwhile; ?>

                <?php else : ?>
                    <article class="col-12" id="post-not-found">
                        <header class="text-center">
                            <h2><?php _e('Nic nie znaleziono', 'polski-podarek'); ?></h2>
                        </header>
                        <footer>
                        </footer>
                    </article>
                <?php endif; ?>
            </div>
        </div>
    </main>
<?php get_footer(); ?>