<?php get_header(); ?>



    <section class="main_content">
        <div class="container container-wide">
            <div class="row">
    <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'entry' ); ?>
    <?php endwhile; ?>

            </div>

        </div>
    </section>



<?php get_template_part('template-parts/branches'); ?>
<?php get_template_part('template-parts/contact_form'); ?>
<?php get_footer(); ?>
