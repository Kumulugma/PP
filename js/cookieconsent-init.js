// obtain plugin
var cc = initCookieConsent();

// run plugin with your configuration
cc.run({
    current_lang: 'pl',
    autoclear_cookies: true,                   // default: false
    page_scripts: true,                        // default: false

    // mode: 'opt-in'                          // default: 'opt-in'; value: 'opt-in' or 'opt-out'
    // delay: 0,                               // default: 0
    // auto_language: null                     // default: null; could also be 'browser' or 'document'
    // autorun: true,                          // default: true
    // force_consent: false,                   // default: false
    // hide_from_bots: false,                  // default: false
    // remove_cookie_tables: false             // default: false
    // cookie_name: 'cc_cookie',               // default: 'cc_cookie'
    // cookie_expiration: 182,                 // default: 182 (days)
    // cookie_necessary_only_expiration: 182   // default: disabled
    // cookie_domain: location.hostname,       // default: current domain
    // cookie_path: '/',                       // default: root
    // cookie_same_site: 'Lax',                // default: 'Lax'
    // use_rfc_cookie: false,                  // default: false
    // revision: 0,                            // default: 0

    onFirstAction: function(user_preferences, cookie){
        // callback triggered only once
    },

    onAccept: function (cookie) {
        // ...
    },

    onChange: function (cookie, changed_preferences) {
        // ...
    },

    languages: {
        'pl': {
            consent_modal: {
                title: 'Używamy ciasteczek!',
                description: 'Ta strona używa niezbędnych plików cookie, aby zapewnić jej prawidłowe działanie i śledzących plików cookie, aby zrozumieć, w jaki sposób wchodzisz z nią w interakcję. Te ostatnie zostaną ustawione dopiero po wyrażeniu zgody. <button type="button" data-cc="c-settings" class="cc-link">Pozwól mi wybrać</button>',
                primary_btn: {
                    text: 'Zaakceptuj wszystkie',
                    role: 'accept_all'              // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Odrzuć wszystkie',
                    role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                }
            },
            settings_modal: {
                title: 'Zarządzaj zgodami plików cookie',
                save_settings_btn: 'Zapisz ustawienia',
                accept_all_btn: 'Akceptuj wszystkie',
                reject_all_btn: 'Odrzuć wszystkie',
                close_btn_label: 'Zamknij',
                cookie_table_headers: [
                    {col1: 'Name'},
                    {col2: 'Domain'},
                    {col3: 'Expiration'},
                    {col4: 'Description'}
                ],
                blocks: [
                    {
                        title: 'Zarządzaj zgodami plików cookie',
                        description: 'Ta strona używa niezbędnych plików cookie, aby zapewnić jej prawidłowe działanie i śledzących plików cookie, aby zrozumieć, w jaki sposób wchodzisz z nią w interakcję. Te ostatnie zostaną ustawione dopiero po wyrażeniu zgody. Więcej informacju znajdziesz w naszej <a href="/polityka-prywatnosci" class="cc-link">Polityce Prywatności</a>.'
                    }, {
                        title: 'Niezbędne do działania strony',
                        description: 'Te pliki cookie są niezbędne do prawidłowego funkcjonowania mojej witryny. Bez tych plików cookie witryna nie działałaby poprawnie.',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                        },                         cookie_table: [             // list of all expected cookies
                            {
                                col1: 'cc_cookie',       // match all cookies starting with "_ga"
                                col2: 'flycars.pl',
                                col3: '14 dni',
                                col4: '',
                                is_regex: true
                            },
                            // {
                            //     col1: '_gid',
                            //     col2: 'google.com',
                            //     col3: '1 day',
                            //     col4: '',
                            // }
                        ]
                    }, {
                        title: 'Pliki cookie wydajności i analityczne',
                        description: 'Te pliki cookie umożliwiają witrynie zapamiętanie wyborów dokonanych w przeszłości.',
                        toggle: {
                            value: 'analytics',     // your cookie category
                            enabled: false,
                            readonly: false
                        },
                        cookie_table: [             // list of all expected cookies
                            {
                                col1: '^_ga',       // match all cookies starting with "_ga"
                                col2: 'google.com',
                                col3: '2 years',
                                col4: '...',
                                is_regex: true
                            },
                            {
                                col1: '_gid',
                                col2: 'google.com',
                                col3: '1 day',
                                col4: '...',
                            }
                        ]
                    }, {
                        title: 'Więcej informacji',
                        description: 'W przypadku jakichkolwiek pytań dotyczących naszej polityki dotyczącej plików cookie i Twoich wyborów, <a class="cc-link" href="/kontakt/">skontaktuj się z nami</a>.',
                    }
                ]
            }
        }
    }
});