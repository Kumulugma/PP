<?php get_header(); ?>

    <main class="section" role="main" id="search_content">
        <div class="container_mid">
            <div class="row">
                <div class="col-24">
                    <h1 class="h3"><?php
                        $allsearch = new WP_Query(
                            array(
                                'posts_per_page' => - 1,
                                's'              => $s,
                            )
                        );
                        $key       = esc_html( $s, 1 );
                        $count     = $allsearch->post_count;
                        _e( '' );
                        echo $count . ' ';
                        wp_reset_query();
                        ?> <?php _e('wyników dla','polski-podarek'); ?> <strong><?php echo $key; ?></strong></h1>
                </div>

                <div class="col-24" id="product_list">
                    <!--                <div class="col-24" id="search_results">-->
                    <div class="row">
                        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                        <?php if (get_post_type() == 'product') { ?>

                        <div class="col-12 col-sm-8 col-md-8 col-lg-6">

                            <?php if ($product->is_in_stock()) { ?>
                            <div class="product_box is-in-stock">
                                <?php } else { ?>
                                <div class="product_box out-of-stock">
                                    <span class="oos-info">Produkt niedostępny</span>
                                    <?php } ?>

                                    <a href="<?php the_permalink();?>">
                                        <div class="overlay">
                                            <h3 class="h6"><?php the_title(); ?></h3>
                                        </div>
                                    </a>
                                    <div class="pb_img">
                                        <?php
                                        $imageID = $product->get_image_id();
                                        $imageSizeName = "product_thumb";
                                        $img = wp_get_attachment_image_src($imageID, $imageSizeName);
                                        ?>

                                        <img src="<?php echo reset($img); ?>" class="img-fluid" alt="<?= _e($product->get_name()); ?>">
                                    </div>
                                    <div class="pb_info">
                                        <h3 class="h6"><?= _e($product->get_name()); ?></h3>
                                        <p><?=$product->get_price()?> zł</p>
                                    </div>
                                    <a href="/produkt/<?=$product->get_slug()?>" class="text_link"><?php _e('Szczegóły','polski-podarek'); ?></a>
                                </div>
                            </div>

                            <?php } else { ?>


                            <?php } ?>


                            <?php endwhile; ?>


                            <?php else : ?>

                                <article class="col-24" id="post-not-found">
                                    <header class="text-center">
                                        <h2><?php _e('Nic nie znaleziono','polski-podarek'); ?></h2>
                                    </header>
                                    <footer>
                                    </footer>
                                </article>

                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
    </main>


<?php get_footer(); ?>