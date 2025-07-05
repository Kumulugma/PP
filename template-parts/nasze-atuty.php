<div id="nasze-atuty">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <span class="ornament"><img src="<?php bloginfo('template_url'); ?>/images/svg/ozdobnik.svg" alt="..." class="img-fluid"></span>
                <h2 class="h3 section_title">Nasze atuty</h2>
            </div>

            <?php if( have_rows('atuty', 50) ): ?>
                <?php while( have_rows('atuty', 50) ): the_row(); ?>

                    <div class="col-12 col-lg-4 na_item">
                        <img src="<?php the_sub_field('ikona')?>" alt="<?php the_sub_field('naglowek')?>">
                        <div class="na_txt">
                            <a href="<?php the_sub_field('link')?>">
                                <span class="h5"><?php the_sub_field('naglowek')?></span>
                            </a>
                            <p><?php the_sub_field('opis')?></p>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
</div>