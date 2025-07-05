<?php
/* Template name: Kategorie */
get_header(); ?>

<main id="kategorie">
    <div class="section_subbheader">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <span class="ornament"><img src="<?php bloginfo('template_url'); ?>/images/svg/ozdobnik-fff.svg" alt="..." class="img-fluid"></span>

                    <h1 class="h4 section_title" itemprop="name"><?php the_title(); ?></h1>

                </div>
            </div>
        </div>
    </div>

    <div class="container" id="kategorie_inner">

        <div class="row">

                <?php
                $prod_categories = get_terms( 'product_cat', [
	                'orderby'    => 'name',
	                'order'      => 'ASC',
	                'parent'     => 0,
	                'hide_empty' => 1, // 1 for yes, 0 for no
//                    'parent' => 1 // 1 for show child categories, 0 for show only parent category
                ] );
                foreach( $prod_categories as $prod_cat ) :
                    $cat_thumb_id = get_woocommerce_term_meta( $prod_cat->term_id, 'thumbnail_id', true );
                    $cat_thumb_url = wp_get_attachment_thumb_url( $cat_thumb_id );
                    $term_link = get_term_link( $prod_cat, 'product_cat' );
                    ?>
                    <div class="col-12 col-sm-6 col-md-6 col-lg-3 cat_box">

                        <a href="<?php echo $term_link; ?>" rel="nofollow" title="<?php echo $prod_cat->name; ?>">
                            <?php if (!empty($cat_thumb_url)) { ?>
                                <img src="<?php echo $cat_thumb_url; ?>" class="img-fluid" alt="<?php echo $prod_cat->name; ?>">
                            <?php }else{ ?>
                                <img src="<?php bloginfo('template_url'); ?>/images/blank.jpg" class="img-fluid" alt="<?php echo $term->name; ?>">
                            <?php } ?>
                        </a>
                        <h5><a href="<?php echo $term_link; ?>" rel="" title="<?php echo $prod_cat->name; ?>"><?php echo $prod_cat->name; ?></a></h5>
                        <?php /* <p><?php echo $prod_cat->description; ?></p> */ ?>

                    </div>
                <?php endforeach; wp_reset_query(); ?>

        </div>


    </div>
</main>

<?php get_template_part('template-parts/czym-jest-pp'); ?>
<?php get_template_part('template-parts/nasze-atuty'); ?>
<?php get_template_part('template-parts/questions'); ?>

<?php get_template_part('template-parts/info'); ?>
<?php /*
<div class="content_wrap" id="info_articles">
    <div class="container">

        <div class="row ha_row">
            <div class="order-0 col-12 order-md-0 col-md-6">

                <h4>Regionalne zestawy prezentowe</h4>

                <h5>Markę Polski Podarek tworzymy z szacunku i zamiłowania do polskiej tradycji oraz regionalnych wyrobów, które powstają z pasji oraz dzięki budowanemu przez lata doświadczeniu.</h5>


                <p>Przekazywane z pokolenia na pokolenie receptury, przepisy oraz unikatowa wiedza pozwalają tworzyć produkty, za którymi stoją ludzie pełni pasji, a nierzadko również niezwykłe historie. Jeśli
                    zastanawiają się Państwo, co podarować swoim kontrahentom oraz klientom, pragniemy zwrócić uwagę na nasze zestawy upominkowe dla firm. Kompozycja słodyczy, win i przetworów najwyższej jakości
                    przypadnie do gustu osobom nawet z najbardziej wymagającym podniebieniem, ponieważ do współpracy zapraszamy wytwórców, którzy mogą pochwalić się unikatowym dorobkiem. Wybierając Polski Podarek,
                    przyczyniacie się Państwo do realnego wparcia lokalnych producentów, którym czasem dość trudno dotrzeć do szerszego grona odbiorców. Dzięki wspólnemu zaangażowaniu, z dumą promujemy polskie wyroby
                    i rękodzieło, nadzwyczajne i jedyne w swoim rodzaju.</p>

                <a href="#" class="btn btn-primary btn-arrow">Zobacz produkty</a>

            </div>
            <div class="order-1 col-12 order-md-1 col-md-6 ha_img">
                <img loading="lazy" width="524" height="478" src="http://dev2022.polskipodarek.pl/wp-content/uploads/2022/10/Image.jpg" alt="" class="img-fluid">
            </div>
        </div>

        <div class="row ha_row">
            <div class="order-0 col-12 order-md-1 col-md-6">

                <h4>Regionalne zestawy prezentowe</h4>

                <h5>Markę Polski Podarek tworzymy z szacunku i zamiłowania do polskiej tradycji oraz regionalnych wyrobów, które powstają z pasji oraz dzięki budowanemu przez lata doświadczeniu.</h5>


                <p>Przekazywane z pokolenia na pokolenie receptury, przepisy oraz unikatowa wiedza pozwalają tworzyć produkty, za którymi stoją ludzie pełni pasji, a nierzadko również niezwykłe historie. Jeśli
                    zastanawiają się Państwo, co podarować swoim kontrahentom oraz klientom, pragniemy zwrócić uwagę na nasze zestawy upominkowe dla firm. Kompozycja słodyczy, win i przetworów najwyższej jakości
                    przypadnie do gustu osobom nawet z najbardziej wymagającym podniebieniem, ponieważ do współpracy zapraszamy wytwórców, którzy mogą pochwalić się unikatowym dorobkiem. Wybierając Polski Podarek,
                    przyczyniacie się Państwo do realnego wparcia lokalnych producentów, którym czasem dość trudno dotrzeć do szerszego grona odbiorców. Dzięki wspólnemu zaangażowaniu, z dumą promujemy polskie wyroby
                    i rękodzieło, nadzwyczajne i jedyne w swoim rodzaju.</p>

                <a href="#" class="btn btn-primary btn-arrow">Zobacz produkty</a>

            </div>
            <div class="order-1 col-12 order-md-0 col-md-6 ha_img">
                <img loading="lazy" width="524" height="478" src="http://dev2022.polskipodarek.pl/wp-content/uploads/2022/10/Image.jpg" alt="" class="img-fluid">
            </div>
        </div>

        <div class="row ha_row">
            <div class="order-0 col-12 order-md-0 col-md-6">

                <h4>Regionalne zestawy prezentowe</h4>

                <h5>Markę Polski Podarek tworzymy z szacunku i zamiłowania do polskiej tradycji oraz regionalnych wyrobów, które powstają z pasji oraz dzięki budowanemu przez lata doświadczeniu.</h5>


                <p>Przekazywane z pokolenia na pokolenie receptury, przepisy oraz unikatowa wiedza pozwalają tworzyć produkty, za którymi stoją ludzie pełni pasji, a nierzadko również niezwykłe historie. Jeśli
                    zastanawiają się Państwo, co podarować swoim kontrahentom oraz klientom, pragniemy zwrócić uwagę na nasze zestawy upominkowe dla firm. Kompozycja słodyczy, win i przetworów najwyższej jakości
                    przypadnie do gustu osobom nawet z najbardziej wymagającym podniebieniem, ponieważ do współpracy zapraszamy wytwórców, którzy mogą pochwalić się unikatowym dorobkiem. Wybierając Polski Podarek,
                    przyczyniacie się Państwo do realnego wparcia lokalnych producentów, którym czasem dość trudno dotrzeć do szerszego grona odbiorców. Dzięki wspólnemu zaangażowaniu, z dumą promujemy polskie wyroby
                    i rękodzieło, nadzwyczajne i jedyne w swoim rodzaju.</p>

                <a href="#" class="btn btn-primary btn-arrow">Zobacz produkty</a>

            </div>
            <div class="order-1 col-12 order-md-1 col-md-6 ha_img">
                <img loading="lazy" width="524" height="478" src="http://dev2022.polskipodarek.pl/wp-content/uploads/2022/10/Image.jpg" alt="" class="img-fluid">
            </div>
        </div>

        <div class="row ha_row">
            <div class="order-0 col-12 order-md-1 col-md-6">

                <h4>Regionalne zestawy prezentowe</h4>

                <h5>Markę Polski Podarek tworzymy z szacunku i zamiłowania do polskiej tradycji oraz regionalnych wyrobów, które powstają z pasji oraz dzięki budowanemu przez lata doświadczeniu.</h5>


                <p>Przekazywane z pokolenia na pokolenie receptury, przepisy oraz unikatowa wiedza pozwalają tworzyć produkty, za którymi stoją ludzie pełni pasji, a nierzadko również niezwykłe historie. Jeśli
                    zastanawiają się Państwo, co podarować swoim kontrahentom oraz klientom, pragniemy zwrócić uwagę na nasze zestawy upominkowe dla firm. Kompozycja słodyczy, win i przetworów najwyższej jakości
                    przypadnie do gustu osobom nawet z najbardziej wymagającym podniebieniem, ponieważ do współpracy zapraszamy wytwórców, którzy mogą pochwalić się unikatowym dorobkiem. Wybierając Polski Podarek,
                    przyczyniacie się Państwo do realnego wparcia lokalnych producentów, którym czasem dość trudno dotrzeć do szerszego grona odbiorców. Dzięki wspólnemu zaangażowaniu, z dumą promujemy polskie wyroby
                    i rękodzieło, nadzwyczajne i jedyne w swoim rodzaju.</p>

                <a href="#" class="btn btn-primary btn-arrow">Zobacz produkty</a>

            </div>
            <div class="order-1 col-12 order-md-0 col-md-6 ha_img">
                <img loading="lazy" width="524" height="478" src="http://dev2022.polskipodarek.pl/wp-content/uploads/2022/10/Image.jpg" alt="" class="img-fluid">
            </div>
        </div>

    </div>
</div>
*/?>

<?php get_template_part('template-parts/newsletter'); ?>

<?php get_footer(); ?>
