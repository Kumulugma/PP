/**
 * JavaScript dla panelu administracyjnego Polski Podarek
 */

jQuery(document).ready(function($) {
    
    let thumbnailIndex = $('#thumbnails-content .thumbnail-row').length;

    /**
     * Dodawanie nowego wiersza miniaturek
     */
    $('#add-thumbnail-row').on('click', function(e) {
        e.preventDefault();
        
        const newRow = `
            <fieldset class="thumbnail-row">
                <label>Nazwa</label>
                <input name="pp_thumbnails[${thumbnailIndex}][name]" 
                       type="text" 
                       value="" 
                       placeholder="nazwa_miniatury">
                
                <label>Szerokość</label>
                <input name="pp_thumbnails[${thumbnailIndex}][width]" 
                       type="number" 
                       value="" 
                       class="small-text" 
                       min="0" 
                       step="1"
                       placeholder="300">
                
                <label>Wysokość</label>
                <input name="pp_thumbnails[${thumbnailIndex}][height]" 
                       type="number" 
                       value="" 
                       class="small-text" 
                       min="0" 
                       step="1"
                       placeholder="300">
                
                <label>
                    <input name="pp_thumbnails[${thumbnailIndex}][crop]" 
                           type="checkbox" 
                           value="1">
                    Przycinaj
                </label>
                
                <button type="button" class="button button-secondary remove-thumbnail-row">Usuń</button>
            </fieldset>
        `;
        
        $('#thumbnails-content').append(newRow);
        thumbnailIndex++;
    });

    /**
     * Usuwanie wiersza miniaturek
     */
    $(document).on('click', '.remove-thumbnail-row', function(e) {
        e.preventDefault();
        
        if (confirm('Czy na pewno chcesz usunąć ten rozmiar miniatury?')) {
            $(this).closest('.thumbnail-row').remove();
        }
    });

    /**
     * Walidacja formularza miniaturek
     */
    $('form').on('submit', function(e) {
        const form = $(this);
        
        // Sprawdź czy to formularz miniaturek
        if (form.find('input[name="action"][value="save_thumbnails"]').length > 0) {
            let hasError = false;
            
            form.find('.thumbnail-row').each(function() {
                const row = $(this);
                const name = row.find('input[name*="[name]"]').val();
                const width = row.find('input[name*="[width]"]').val();
                const height = row.find('input[name*="[height]"]').val();
                
                // Usuń poprzednie style błędów
                row.find('input').removeClass('error-input');
                
                if (name && (!width || !height)) {
                    row.find('input[name*="[width]"], input[name*="[height]"]').addClass('error-input');
                    hasError = true;
                }
                
                if ((width || height) && !name) {
                    row.find('input[name*="[name]"]').addClass('error-input');
                    hasError = true;
                }
            });
            
            if (hasError) {
                e.preventDefault();
                alert('Proszę wypełnić wszystkie pola (nazwa, szerokość, wysokość) lub usunąć niepełne wiersze.');
                return false;
            }
        }
    });

    /**
     * Dodaj style dla błędów walidacji
     */
    if ($('head').find('#pp-admin-styles').length === 0) {
        $('head').append(`
            <style id="pp-admin-styles">
                .thumbnail-row {
                    border: 1px solid #ddd;
                    padding: 15px;
                    margin: 10px 0;
                    background: #f9f9f9;
                    border-radius: 4px;
                }
                
                .thumbnail-row label {
                    display: inline-block;
                    width: 80px;
                    margin-right: 10px;
                    font-weight: bold;
                }
                
                .thumbnail-row input[type="text"],
                .thumbnail-row input[type="number"] {
                    margin-right: 15px;
                    margin-bottom: 5px;
                }
                
                .error-input {
                    border: 2px solid #dc3232 !important;
                    box-shadow: 0 0 2px rgba(220, 50, 50, 0.8) !important;
                }
                
                .remove-thumbnail-row {
                    margin-left: 10px;
                }
                
                .nav-tab-wrapper {
                    margin-bottom: 20px;
                }
                
                .form-table th {
                    width: 200px;
                }
                
                fieldset {
                    margin-bottom: 10px;
                    padding: 5px 0;
                }
                
                fieldset label {
                    font-weight: normal;
                }
                
                .small-text {
                    width: 80px;
                }
                
                #add-thumbnail-row {
                    margin-bottom: 20px;
                }
                
                .notice {
                    margin: 20px 0;
                }
            </style>
        `);
    }

    /**
     * Potwierdzenie resetowania ustawień (jeśli dodamy tę funkcję)
     */
    $(document).on('click', '.reset-settings', function(e) {
        if (!confirm('Czy na pewno chcesz zresetować wszystkie ustawienia do domyślnych? Ta operacja jest nieodwracalna.')) {
            e.preventDefault();
            return false;
        }
    });

    /**
     * Automatyczne zapisywanie podczas zmiany checkboxów (opcjonalne)
     */
    $('.auto-save input[type="checkbox"]').on('change', function() {
        const checkbox = $(this);
        const form = checkbox.closest('form');
        
        // Dodaj informację o automatycznym zapisywaniu
        if (form.find('.auto-save-notice').length === 0) {
            form.prepend('<div class="auto-save-notice notice notice-info"><p>Automatyczne zapisywanie...</p></div>');
        }
        
        // Wyślij formularz po krótkiej pauzie
        setTimeout(function() {
            form.submit();
        }, 500);
    });

    /**
     * Ukryj/pokaż szczegóły sekcji (accordion)
     */
    $('.section-toggle').on('click', function(e) {
        e.preventDefault();
        const section = $(this).next('.section-content');
        section.slideToggle();
        
        // Zmień tekst przycisku
        const currentText = $(this).text();
        $(this).text(currentText === 'Pokaż' ? 'Ukryj' : 'Pokaż');
    });

    /**
     * Tooltips dla opcji (jeśli dodamy data-tooltip)
     */
    $('[data-tooltip]').hover(
        function() {
            const tooltip = $('<div class="pp-tooltip">' + $(this).data('tooltip') + '</div>');
            $('body').append(tooltip);
            
            const pos = $(this).offset();
            tooltip.css({
                position: 'absolute',
                top: pos.top - tooltip.outerHeight() - 5,
                left: pos.left + ($(this).outerWidth() / 2) - (tooltip.outerWidth() / 2),
                background: '#333',
                color: '#fff',
                padding: '5px 10px',
                borderRadius: '3px',
                fontSize: '12px',
                zIndex: 9999
            });
        },
        function() {
            $('.pp-tooltip').remove();
        }
    );

});