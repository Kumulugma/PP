<?php get_header(); ?>

<main class="main_content" style="padding-top: 5rem;">
    <div class="container">
        <div class="row subpage_header">
            <div class="col-12">
                <?php the_breadcrumb(); ?>
                <h1 class="h3 entry-title">
                    <?php 
                        $title = str_replace('Kategoria: ', '', get_the_archive_title());
                        $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                        echo ($paged > 1) ? $title . " - strona " . $paged : $title;
                    ?>
                </h1>
            </div>
        </div>

        <?php 
                $paged = get_query_var('paged') ? get_query_var('paged') : 1;
                $args = array(
                    'posts_per_page' => 12,
                    'paged' => $paged
                );
                $custom_query = new WP_Query($args);
        
        if (have_posts()) : ?>
            <div class="row post_listing">
                <?php while (have_posts()) : the_post(); ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <article class="post_item">
                            <a href="<?php the_permalink(); ?>" class="post_thumbnail" rel="nofollow">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'img-fluid']); ?>
                                <?php endif; ?>
                            </a>
                                <div class="post__information">
                                    <div class="post_meta py-2">
                                        <small>Dodano: <?php echo get_the_date('j F Y'); ?> | Kategoria: <?php the_category(', '); ?></small>
                                    </div>
                                    <p class="h5 post_title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <p class="post_excerpt">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                    </p>
                                    <a href="<?php the_permalink(); ?>" class="read_more" rel="nofollow">Czytaj więcej</a>
                                </div>
                        </article>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="pagination pagination__blog">
                <?php 
                echo paginate_links(array(
                    'total' => $custom_query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'mid_size' => 2,
                    'prev_text' => __('&laquo; Poprzednie', 'textdomain'),
                    'next_text' => __('Następne &raquo;', 'textdomain'),
                )); 
                ?>
            </div>

            <?php wp_reset_postdata(); ?>
        <?php endif; ?>
    </div>
</main>

<?php get_template_part('template-parts/questions'); ?>
<?php get_footer(); ?>