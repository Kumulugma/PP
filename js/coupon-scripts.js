/*!
 * Kupony rabatowe - JavaScript (POPRAWIONY)
 * Polski Podarek 2025
 * Ładowany tylko na stronach koszyka i checkout
 */

// Namespace dla funkcji kuponów
window.PolskiPodarekCoupons = window.PolskiPodarekCoupons || {};

(function($) {
    'use strict';

    // Konfiguracja
    const CONFIG = {
        selectors: {
            showCouponLink: '.showcoupon',
            couponForm: '.checkout_coupon',
            couponInput: 'input[name="coupon_code"]',
            couponButton: 'button[name="apply_coupon"]',
            removeCouponLinks: '.woocommerce-remove-coupon',
            notices: '.woocommerce-message, .woocommerce-error',
            cartTotals: '.cart_totals, .summary-order'
        },
        classes: {
            loading: 'coupon-loading',
            valid: 'valid',
            invalid: 'invalid',
            checking: 'checking'
        },
        timing: {
            validationDelay: 800, // Zwiększone opóźnienie
            noticeAutoHide: 5000,
            animationDuration: 300
        }
    };

    // Główna inicjalizacja
    $(document).ready(function() {
        PolskiPodarekCoupons.init();
    });

    // Główny obiekt funkcji
    PolskiPodarekCoupons = {
        
        // Inicjalizacja wszystkich funkcji
        init: function() {
            console.log('🎟️ Inicjalizacja systemu kuponów (poprawiony)...');
            
            this.initCouponToggle();
            this.initSimpleCouponValidation(); // Zmieniona funkcja
            this.initCouponSubmission();
            this.initCouponRemoval();
            this.initNoticeHandling();
            this.initUrlCouponHandling();
            this.bindWooCommerceEvents();
            
            console.log('✅ System kuponów załadowany (bez agresywnej walidacji)');
        },

        // Obsługa pokazywania/ukrywania formularza kuponu
        initCouponToggle: function() {
            $(document).on('click', CONFIG.selectors.showCouponLink, function(e) {
                e.preventDefault();
                
                const $form = $(CONFIG.selectors.couponForm);
                const $link = $(this);
                
                if ($form.length === 0) return;
                
                if ($form.is(':visible')) {
                    $form.slideUp(CONFIG.timing.animationDuration);
                    $link.text($link.data('original-text') || 'Kliknij tutaj, aby wprowadzić kod');
                } else {
                    $form.slideDown(CONFIG.timing.animationDuration, function() {
                        // Focus na input po pokazaniu
                        const $input = $form.find(CONFIG.selectors.couponInput);
                        if ($input.length) {
                            $input.focus();
                        }
                    });
                    
                    // Zapisz oryginalny tekst
                    if (!$link.data('original-text')) {
                        $link.data('original-text', $link.text());
                    }
                    $link.text('Ukryj formularz kuponu');
                }
            });
        },

        // NOWA - Prosta walidacja bez agresywnych sprawdzeń
        initSimpleCouponValidation: function() {
            let validationTimeout;
            
            // Usuń wszystkie poprzednie event listenery
            $(document).off('input', CONFIG.selectors.couponInput);
            $(document).off('blur', CONFIG.selectors.couponInput);
            $(document).off('keypress', CONFIG.selectors.couponInput);
            
            // Prosta walidacja - nie blokuj żadnych kodów
            $(document).on('input', CONFIG.selectors.couponInput, function() {
                const $input = $(this);
                const $button = $input.closest('form').find(CONFIG.selectors.couponButton);
                const code = $input.val().trim();
                
                // Wyczyść poprzedni timeout
                clearTimeout(validationTimeout);
                
                // Usuń wszystkie klasy walidacji i feedback
                $input.removeClass([CONFIG.classes.valid, CONFIG.classes.invalid, CONFIG.classes.checking].join(' '));
                PolskiPodarekCoupons.removeFeedback($input);
                
                // Resetuj style border
                $input.css('border-color', '');
                
                // Proste formatowanie - tylko podstawowe czyszczenie
                const cleanCode = code.replace(/\s+/g, ' '); // Normalizuj spacje
                if (cleanCode !== $input.val()) {
                    $input.val(cleanCode);
                }
                
                // Zarządzaj przyciskiem - włącz dla dowolnego kodu 1+ znaków
                if (code.length >= 1) {
                    $button.prop('disabled', false).removeClass('disabled');
                    $input.addClass(CONFIG.classes.valid);
                    
                    // Opcjonalnie pokaż pozytywny feedback
                    if (code.length >= 3) {
                        PolskiPodarekCoupons.showFeedback($input, '✓ Gotowy do sprawdzenia', 'success');
                    }
                } else {
                    $button.prop('disabled', true).addClass('disabled');
                    $input.removeClass(CONFIG.classes.valid);
                }
            });

            // Enter w polu kuponu
            $(document).on('keypress', CONFIG.selectors.couponInput, function(e) {
                if (e.which === 13) { // Enter
                    e.preventDefault();
                    const $button = $(this).closest('form').find(CONFIG.selectors.couponButton);
                    if ($button.length && !$button.is(':disabled')) {
                        $button.click();
                    }
                }
            });
        },

        // Obsługa wysyłania formularza - bez walidacji JS
        initCouponSubmission: function() {
            $(document).on('click', CONFIG.selectors.couponButton, function(e) {
                const $button = $(this);
                const $form = $button.closest('form');
                const $input = $form.find(CONFIG.selectors.couponInput);
                const code = $input.val().trim();
                
                // Tylko podstawowa kontrola - czy coś wprowadzono
                if (!code || code.length < 1) {
                    e.preventDefault();
                    PolskiPodarekCoupons.showFeedback($input, 'Wprowadź kod kuponu', 'error');
                    $input.focus();
                    return false;
                }
                
                // Usuń feedback walidacji JS
                PolskiPodarekCoupons.removeFeedback($input);
                
                // Dodaj loading state
                PolskiPodarekCoupons.setButtonLoading($button, true);
                
                // Upewnij się że jest nonce
                if ($form.find('input[name="_wpnonce"]').length === 0 && 
                    $form.find('input[name="woocommerce-cart-nonce"]').length === 0) {
                    
                    // Dodaj nonce dla bezpieczeństwa
                    if (typeof pp_coupon_ajax !== 'undefined' && pp_coupon_ajax.nonce) {
                        $form.append('<input type="hidden" name="_wpnonce" value="' + pp_coupon_ajax.nonce + '">');
                    }
                }
                
                // Pozwól formularzowi się wysłać - WooCommerce sprawdzi kupon
                return true;
            });
        },

        // Obsługa usuwania kuponów
        initCouponRemoval: function() {
            $(document).on('click', CONFIG.selectors.removeCouponLinks, function(e) {
                const couponCode = $(this).data('coupon') || 
                                  $(this).closest('tr').find('th').text().replace('Kupon: ', '').trim();
                
                const confirmText = 'Czy na pewno chcesz usunąć kupon' + 
                                   (couponCode ? ' "' + couponCode + '"' : '') + '?';
                
                if (!confirm(confirmText)) {
                    e.preventDefault();
                    return false;
                }
                
                const $link = $(this);
                const originalText = $link.text();
                
                $link.text('Usuwam...').addClass('loading');
                
                // Fallback przywrócenia tekstu
                setTimeout(() => {
                    $link.text(originalText).removeClass('loading');
                }, 5000);
            });
        },

        // Obsługa komunikatów
        initNoticeHandling: function() {
            // Dodaj przyciski zamykania
            $(CONFIG.selectors.notices).each(function() {
                const $notice = $(this);
                
                if ($notice.find('.notice-close').length === 0) {
                    const $closeBtn = $('<button type="button" class="notice-close" aria-label="Zamknij">&times;</button>');
                    
                    $closeBtn.on('click', function() {
                        $notice.fadeOut(CONFIG.timing.animationDuration);
                    });
                    
                    $notice.append($closeBtn);
                }
            });
            
            // Auto-hide dla success messages
            $(CONFIG.selectors.notices).filter('.woocommerce-message').each(function() {
                const $notice = $(this);
                
                setTimeout(() => {
                    if ($notice.is(':visible')) {
                        $notice.fadeOut(CONFIG.timing.animationDuration);
                    }
                }, CONFIG.timing.noticeAutoHide);
            });
        },

        // Obsługa kuponów z URL
        initUrlCouponHandling: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const couponFromUrl = urlParams.get('coupon');
            
            if (couponFromUrl) {
                console.log('🎟️ Kupon z URL:', couponFromUrl);
                
                // Automatycznie wypełnij pole
                setTimeout(() => {
                    const $input = $(CONFIG.selectors.couponInput).first();
                    if ($input.length) {
                        $input.val(couponFromUrl).trigger('input');
                        
                        // Pokaż formularz jeśli ukryty
                        const $form = $(CONFIG.selectors.couponForm);
                        if ($form.length && !$form.is(':visible')) {
                            $(CONFIG.selectors.showCouponLink).click();
                        }
                    }
                }, 500);
            }
        },

        // Binduj eventy WooCommerce
        bindWooCommerceEvents: function() {
            $(document.body).on('updated_cart_totals updated_checkout', function() {
                PolskiPodarekCoupons.handleCartUpdate();
            });
            
            $(document.body).on('applied_coupon removed_coupon', function(e, couponCode) {
                console.log('🎟️ Kupon event:', e.type, couponCode);
                PolskiPodarekCoupons.handleCouponChange(e.type, couponCode);
            });
        },

        // WYŁĄCZONA AJAX walidacja - pozwól WooCommerce sprawdzać
        validateCouponAjax: function(code, $input) {
            // Funkcja wyłączona - nie sprawdzaj przez AJAX
            console.log('🔧 AJAX validation disabled - WooCommerce will validate');
            return;
        },

        // Bardzo liberalna walidacja lokalna
        validateCouponLocal: function(code, $input) {
            $input.removeClass([CONFIG.classes.checking].join(' '));
            
            // Zawsze pozytywny feedback dla kodów 1+ znaków
            if (code && code.trim().length >= 1) {
                $input.addClass(CONFIG.classes.valid);
                PolskiPodarekCoupons.showFeedback($input, '✓ Kod gotowy do sprawdzenia', 'success');
            } else {
                $input.removeClass(CONFIG.classes.valid);
                PolskiPodarekCoupons.showFeedback($input, 'Wprowadź kod kuponu', 'info');
            }
        },

        // Pomocnicze funkcje - zachowane z oryginalnego kodu
        formatCoupon: function(code) {
            // Minimalne formatowanie - zachowaj więcej znaków
            return code.trim().replace(/\s+/g, ' '); // Tylko normalizuj spacje
        },

        isValidCouponFormat: function(code) {
            // Bardzo liberalna walidacja - akceptuj prawie wszystko
            return code && code.trim().length >= 1;
        },

        getFeedbackElement: function($input) {
            let $feedback = $input.parent().find('.coupon-feedback');
            
            if ($feedback.length === 0) {
                $feedback = $('<div class="coupon-feedback"></div>');
                $input.parent().append($feedback);
            }
            
            return $feedback;
        },

        showFeedback: function($input, message, type = 'info') {
            const $feedback = PolskiPodarekCoupons.getFeedbackElement($input);
            
            $feedback.removeClass('success error warning info')
                     .addClass(type)
                     .text(message)
                     .show();
        },

        removeFeedback: function($input) {
            $input.parent().find('.coupon-feedback').hide().remove();
        },

        updateSubmitButton: function($input) {
            const $button = $input.closest('form').find(CONFIG.selectors.couponButton);
            const code = $input.val().trim();
            
            // Włącz przycisk dla dowolnego kodu
            if (code.length >= 1) {
                $button.prop('disabled', false).removeClass('disabled');
            } else {
                $button.prop('disabled', true).addClass('disabled');
            }
        },

        setButtonLoading: function($button, loading) {
            if (loading) {
                const originalText = $button.data('original-text') || $button.text();
                $button.data('original-text', originalText)
                       .text('Sprawdzam kupon...')
                       .prop('disabled', true)
                       .addClass('loading');
            } else {
                const originalText = $button.data('original-text');
                if (originalText) {
                    $button.text(originalText)
                           .prop('disabled', false)
                           .removeClass('loading');
                }
            }
        },

        handleCartUpdate: function() {
            const $cartTotals = $(CONFIG.selectors.cartTotals);
            
            if ($cartTotals.length) {
                $cartTotals.css('opacity', '0.7');
                
                setTimeout(() => {
                    $cartTotals.css('opacity', '1');
                }, 1000);
            }
            
            // Reinicjalizuj handlery po aktualizacji
            setTimeout(() => {
                PolskiPodarekCoupons.initNoticeHandling();
                
                // Resetuj loading state przycisków
                $(CONFIG.selectors.couponButton).each(function() {
                    PolskiPodarekCoupons.setButtonLoading($(this), false);
                });
            }, 100);
        },

        handleCouponChange: function(eventType, couponCode) {
            if (eventType === 'applied_coupon') {
                PolskiPodarekCoupons.showMessage('🎉 Kupon "' + couponCode + '" został zastosowany!', 'success');
                
                // Wyczyść pole input
                $(CONFIG.selectors.couponInput).val('').trigger('input');
                
            } else if (eventType === 'removed_coupon') {
                PolskiPodarekCoupons.showMessage('✅ Kupon "' + couponCode + '" został usunięty', 'info');
            }
        },

        showMessage: function(message, type = 'success') {
            const messageClass = type === 'success' ? 'woocommerce-message' : 'woocommerce-error';
            const $message = $('<div class="' + messageClass + '">' + message + '</div>');
            
            // Znajdź najlepsze miejsce do wstawienia komunikatu
            const $target = $('.woocommerce-notices-wrapper, .checkout_coupon, .cart-form').first();
            
            if ($target.length) {
                $target.prepend($message);
                
                // Auto-hide po 5 sekundach
                setTimeout(() => {
                    $message.fadeOut(CONFIG.timing.animationDuration, function() {
                        $(this).remove();
                    });
                }, CONFIG.timing.noticeAutoHide);
            }
        },

        // Funkcje publiczne API
        validateCoupon: function(code) {
            const $input = $(CONFIG.selectors.couponInput).first();
            if ($input.length) {
                $input.val(code).trigger('input');
            }
        },

        applyCoupon: function(code) {
            const $input = $(CONFIG.selectors.couponInput).first();
            const $button = $(CONFIG.selectors.couponButton).first();
            
            if ($input.length && $button.length) {
                $input.val(code).trigger('input');
                setTimeout(() => {
                    $button.click();
                }, 100);
            }
        },

        toggleCouponForm: function(show) {
            const $form = $(CONFIG.selectors.couponForm);
            const $link = $(CONFIG.selectors.showCouponLink);
            
            if ($form.length) {
                if (show === undefined) {
                    $link.click();
                } else if (show && !$form.is(':visible')) {
                    $link.click();
                } else if (!show && $form.is(':visible')) {
                    $link.click();
                }
            }
        },

        resetCouponForm: function() {
            const $input = $(CONFIG.selectors.couponInput);
            const $button = $(CONFIG.selectors.couponButton);
            
            $input.val('')
                  .removeClass([CONFIG.classes.valid, CONFIG.classes.invalid, CONFIG.classes.checking].join(' '))
                  .css('border-color', '');
            
            PolskiPodarekCoupons.removeFeedback($input);
            
            if ($button.length) {
                $button.prop('disabled', true).addClass('disabled');
                PolskiPodarekCoupons.setButtonLoading($button, false);
            }
        },

        // Debug funkcje
        debug: {
            logCouponStatus: function() {
                console.group('🎟️ Status kuponów (poprawiony)');
                console.log('Formularz kuponu:', $(CONFIG.selectors.couponForm).length > 0 ? 'Znaleziony' : 'Nie znaleziony');
                console.log('Link pokazujący:', $(CONFIG.selectors.showCouponLink).length > 0 ? 'Znaleziony' : 'Nie znaleziony');
                console.log('Input kuponu:', $(CONFIG.selectors.couponInput).length);
                console.log('Przycisk kuponu:', $(CONFIG.selectors.couponButton).length);
                console.log('Linki usuwania:', $(CONFIG.selectors.removeCouponLinks).length);
                console.log('Walidacja JS:', 'WYŁĄCZONA - WooCommerce sprawdza');
                console.groupEnd();
            },

            testCoupon: function(code = 'TEST123') {
                console.log('🧪 Testowanie kuponu (bez JS walidacji):', code);
                PolskiPodarekCoupons.applyCoupon(code);
            },

            simulateSuccess: function() {
                const $input = $(CONFIG.selectors.couponInput).first();
                if ($input.length) {
                    $input.addClass(CONFIG.classes.valid);
                    PolskiPodarekCoupons.showFeedback($input, '✓ Test sukcesu', 'success');
                }
            },

            clearValidation: function() {
                $(CONFIG.selectors.couponInput).each(function() {
                    const $input = $(this);
                    $input.removeClass([CONFIG.classes.valid, CONFIG.classes.invalid, CONFIG.classes.checking].join(' '));
                    $input.css('border-color', '');
                    PolskiPodarekCoupons.removeFeedback($input);
                });
                console.log('🧹 Walidacja wyczyszczona');
            }
        }
    };

    // Dodaj funkcje debug do window dla łatwego dostępu z konsoli
    if (typeof window.console !== 'undefined') {
        window.couponDebug = PolskiPodarekCoupons.debug;
    }

    // Ekspozycja głównych funkcji na window
    window.validateCoupon = PolskiPodarekCoupons.validateCoupon;
    window.applyCoupon = PolskiPodarekCoupons.applyCoupon;
    window.toggleCouponForm = PolskiPodarekCoupons.toggleCouponForm;

})(jQuery);

// Fallback dla środowisk bez jQuery
if (typeof jQuery === 'undefined') {
    console.warn('⚠️ jQuery nie jest załadowane - niektóre funkcje kuponów mogą nie działać');
    
    // Podstawowa implementacja bez jQuery
    document.addEventListener('DOMContentLoaded', function() {
        // Podstawowe toggle formularza
        const showLinks = document.querySelectorAll('.showcoupon');
        const forms = document.querySelectorAll('.checkout_coupon');
        
        showLinks.forEach((link, index) => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const form = forms[index];
                if (form) {
                    form.style.display = form.style.display === 'none' ? 'block' : 'none';
                }
            });
        });
        
        // Podstawowa obsługa inputów - bez walidacji
        const inputs = document.querySelectorAll('input[name="coupon_code"]');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const code = this.value.trim();
                const button = this.form.querySelector('button[name="apply_coupon"]');
                
                if (button) {
                    button.disabled = code.length < 1;
                }
            });
        });
        
        console.log('✅ Podstawowa obsługa kuponów załadowana (bez jQuery, bez walidacji)');
    });
}