<?php
/* Template name: FAQ */
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

            <div class="row" id="faq">

                <div class="col-12" id="faq_header">

                    <div class="row">
                        <div class="order-1 order-md-0 col-12 col-md-7 col-lg-8">
                            <h6>Wybierze kategorię pytań:</h6>
                            <ul class="faq_cat_list">
                                <?php if( have_rows('dostepne_kategorie') ): ?>
                                    <?php $f = 0; while( have_rows('dostepne_kategorie') ): the_row(); $f++; ?>
                                        <li>
                                            <button class="btn faq_cat_element<?= $f == 1 ? ' active' : '' ?>" data-category="<?php echo str_replace(' ', '-', get_sub_field('dostepna_kategoria')); ?>">
                                                <?php the_sub_field('dostepna_kategoria'); ?>
                                            </button>
                                        </li>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        <div class="order-0 order-md-1 col-12 col-md-5 col-lg-4">
                            <div class="input_wrap">
                                <input type="search" class="faq_search_input" placeholder="Tutaj możesz wpisać swoje pytanie" />
                                <img class="search_icon" src="<?php bloginfo('template_url'); ?>/images/svg/search_000.svg" alt="Wyszukaj">
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>

	            <?php if ( have_rows( 'pytania' ) ): ?>
                    <div class="order-1 order-md-0 col-md-3" id="faq_sidebar">
                        <div class="faq_sticky">
                            <h5>Powiązane pytania:</h5>
                            <ul class="faq_questions">
		                        <?php while ( have_rows( 'pytania' ) ): the_row(); ?>
                                    <li class="d-none" data-question data-question-category="<?php echo str_replace(' ', '-', get_sub_field('kategoria_pytania')); ?>"><?php the_sub_field( 'tytul' ); ?></li>
		                        <?php endwhile; ?>
                            </ul>
                        </div>
                    </div>
	            <?php endif; ?>

                <div class="order-0 order-md-1 col-md-9">

                    <ul class="faq_answers">
                        <?php if( have_rows('pytania') ): ?>
                                <?php while( have_rows('pytania') ): the_row(); ?>
                                    <li data-answer-category="<?php echo str_replace(' ', '-', get_sub_field('kategoria_pytania')); ?>">
                                        <div data-answer>
                                            <h4><?php the_sub_field('kategoria_pytania'); ?></h4>
                                            <h5><?php the_sub_field('tytul'); ?></h5>
                                            <?php the_sub_field('tresc'); ?>
                                        </div>
                                    </li>
                                <?php endwhile; ?>
                        <?php endif; ?>

                        <li class="filter-no-results">
                            <p>Brak wyników.</p>
                        </li>
                    </ul>
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
