<?php
/* Template Name: Aktualności */
get_header(); ?>

<?php get_template_part('template-parts/breadcrumbs'); ?>

<?php
$loop = new WP_Query(array(
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'posts_per_page' => 0,
)); ?>

    <main class="main_content">
        <div class="container">
            <div class="row">
                <div class="contaienr">
                    <div class="col-12">
                        <?php the_breadcrumb(); ?>
                        <h1 class="h3  entry-title" itemprop="name"><?php the_title(); ?></h1>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12" id="cat_list">
                    <ul class="list-unstyled">
                        <?php wp_list_categories( array(
                            'title_li' => false,
                        ) ); ?>
                    </ul>
                </div>
                <div class="col-12">
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="row">
                <?php
                while ($loop->have_posts()) : $loop->the_post(); ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 blog_item">
                        <a href="<?php the_permalink_rss(); ?>" title="<?php the_title(); ?>">
                            <?php the_post_thumbnail('medium', ['class' => 'img-fluid']); ?>
                            <h2 class="h6"><?php the_title(); ?></h2>
                            <small><?php the_date('j F Y'); ?></small>
                            <div class="bi_cat"><?php the_category(', '); ?></div>
                            <hr>
                            <?php the_excerpt(); ?>
                        </a>
                        <a href="<?php the_permalink(); ?>" class="btn_small btn_gray" title="Czytaj <?php the_title(); ?>">Czytaj dalej</a>
                    </div>
                <?php endwhile;
                wp_reset_query(); ?>
            </div>
        </div>
    </main>

<?php get_template_part('template-parts/quick_contact'); ?>
<?php get_footer(); ?>