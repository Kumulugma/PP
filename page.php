<?php
/* Template name: Standard */
get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <main class="main_content">
        <div class="container">
            <div class="row subpage_header">
                    <div class="col-12">
                        <?php the_breadcrumb(); ?>
                        <h1 class="h3  entry-title" itemprop="name"><?php the_title(); ?></h1>
                    </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </main>

<?php /*
    <?php if (has_post_thumbnail()) { ?>
        <section class="main_content">

            <div class="page_side_bg"></div>
            <div class="page_side_img">
                <div class="inner_img" style="background-image: url('<?php echo get_the_post_thumbnail_url(); ?>')"></div>
            </div>

            <div class="container container-wide">

                <div class="row">
                    <div class="col-12 col-md-6 content_default">
                        <?php the_content(); ?>
                    </div>
                </div>

            </div>
        </section>
    <?php } else { ?>
        <section class="main_content">
            <div class="container container-wide">
                <div class="row">
                    <div class="col-12">
                        <?php the_content(); ?>
                    </div>
                </div>

            </div>
        </section>
    <?php } ?>
*/ ?>

<?php endwhile; endif; ?>
<?php get_template_part('template-parts/questions'); ?>
<?php get_footer(); ?>
