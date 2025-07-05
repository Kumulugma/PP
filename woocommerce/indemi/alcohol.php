<div id="alco_box"<?=(isset($_COOKIE['alcohol']) ? '' : 'style="display: block;"')?> class="alcohol_box">

    <div class="sb_inner">
        <form action="<?php echo home_url('/alcohol'); ?>" method="get" class="search_form">
            <div class="col-12">
                <p class="text-white text-center">Produkt, który wybrałeś zawiera alkohol. Prosimy potwierdź, że masz 18lat.</p>
            </div>
            <div class="col-12 d-flex justify-content-around">
                <a class="btn btn-primary-outline " href="/sklep/">
                    Wróć
                </a>
                <a class="btn btn-secondary" id="alcohol_cookie" href="#">
                    Potwierdzam
                </a>
            </div>
            
        </form>
    </div>
</div>