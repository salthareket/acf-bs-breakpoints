( function ( $ ) {
    'use strict';

    /* ═══════════════════════════════════════════
     *  Field Type → Alt ayarları göster/gizle
     * ═══════════════════════════════════════════ */

    function toggleSettings( $select ) {
        var $wrap = $select.closest( '.acf-field-object' );
        if ( ! $wrap.length ) $wrap = $select.closest( '.acf-field-settings, form' );
        if ( ! $wrap.length ) return;

        var val = $select.val();

        function find( cls ) {
            var $el = $wrap.find( '.' + cls );
            if ( ! $el.length ) return $();
            var $field = $el.closest( '.acf-field' );
            return $field.length ? $field : $el;
        }

        find( 'bs-breakpoints-choices-setting' ).toggle( val === 'select' );
        find( 'bsbp-units-allowed-setting' ).toggle( val === 'units' );
        find( 'bsbp-units-default-setting' ).toggle( val === 'units' );
        find( 'default_value-setting' ).toggle( val === 'text' || val === 'number' || val === 'units' );
        find( 'bsbp-color-setting' ).toggle( val === 'color_picker' );
        find( 'bsbp-bool-setting' ).toggle( val === 'true_false' );
        find( 'bsbp-image-setting' ).toggle( val === 'image' );

        // BP Tablo: default hücrelerini field type'a göre toggle
        $wrap.find( '.bsbp-bp-default-cell .bsbp-def-type' ).removeClass( 'is-active' );
        $wrap.find( '.bsbp-bp-default-cell .bsbp-def-' + val ).addClass( 'is-active' );

        // Sync: tablo select defaults (choices textarea'dan)
        syncTableSelectDefaults( $wrap );
        syncDefaultPriority( $wrap );
        // NOT: syncTableUnitsDefaults burada çağrılmaz — PHP zaten doğru render ediyor
        // Sadece kullanıcı "Kullanılacak Birimler"i değiştirdiğinde JS ile güncellenir
    }

    $( document ).on( 'change', '.bs-breakpoints-type-setting select', function () {
        toggleSettings( $( this ) );
    } );

    $( document ).ready( function () {
        $( '.bs-breakpoints-type-setting select' ).each( function () {
            toggleSettings( $( this ) );
        } );
    } );

    // ACF field type "Breakpoints" seçildiğinde settings yeniden render ediliyor
    // Kısa aralıklarla kontrol et — yeni eklenen field'lar için
    setInterval( function () {
        $( '.bs-breakpoints-type-setting select' ).each( function () {
            var $sel = $( this );
            if ( $sel.data( 'bsbp-init' ) ) return;
            $sel.data( 'bsbp-init', true );
            toggleSettings( $sel );
        } );
    }, 500 );

    if ( typeof acf !== 'undefined' && typeof acf.add_action !== 'undefined' ) {
        acf.add_action( 'open_field_object', function ( field ) {
            var $el = field && field.$el ? field.$el : null;
            if ( ! $el ) return;
            $el.find( '.bs-breakpoints-type-setting select' ).each( function () {
                toggleSettings( $( this ) );
            } );
        } );
        acf.add_action( 'append_field_object', function ( field ) {
            var $el = field && field.$el ? field.$el : null;
            if ( ! $el ) return;
            $el.find( '.bs-breakpoints-type-setting select' ).each( function () {
                toggleSettings( $( this ) );
            } );
        } );
        acf.add_action( 'new_field_object', function ( field ) {
            var $el = field && field.$el ? field.$el : null;
            if ( ! $el ) return;
            $el.find( '.bs-breakpoints-type-setting select' ).each( function () {
                toggleSettings( $( this ) );
            } );
        } );
    }

    /* ═══════════════════════════════════════════
     *  Row enabled/disabled — SADECE ilk td switch
     * ═══════════════════════════════════════════ */

    $( document ).on( 'change', '.bsbp-bp-row > td:first-child input[type="checkbox"]', function () {
        var $row = $( this ).closest( 'tr.bsbp-bp-row' );
        $row.toggleClass( 'is-enabled', this.checked );
        $row.toggleClass( 'is-disabled', ! this.checked );
    } );

    /* ═══════════════════════════════════════════
     *  Choices textarea → tablo select sync
     * ═══════════════════════════════════════════ */

    function parseChoices( text ) {
        var choices = [];
        if ( ! text ) return choices;
        var lines = text.split( /\r?\n/ );
        for ( var i = 0; i < lines.length; i++ ) {
            var line = $.trim( lines[i] );
            if ( ! line ) continue;
            var idx = line.indexOf( ':' );
            if ( idx !== -1 ) {
                choices.push({ val: $.trim( line.substring( 0, idx ) ), label: $.trim( line.substring( idx + 1 ) ) });
            } else {
                choices.push({ val: line, label: line });
            }
        }
        return choices;
    }

    function syncTableSelectDefaults( $wrap ) {
        var $textarea = $wrap.find( '.bs-breakpoints-choices-setting textarea' );
        if ( ! $textarea.length ) return;
        var choices = parseChoices( $textarea.val() );

        $wrap.find( '.bsbp-bp-default-cell .bsbp-def-select select' ).each( function () {
            var $sel = $( this ), current = $sel.val();
            $sel.empty().append( $( '<option>' ).val( '' ).text( '—' ) );
            for ( var i = 0; i < choices.length; i++ ) {
                var $opt = $( '<option>' ).val( choices[i].val ).text( choices[i].label );
                if ( choices[i].val === current ) $opt.prop( 'selected', true );
                $sel.append( $opt );
            }
        } );

        $wrap.find( '.bsbp-global-select-default select' ).each( function () {
            var $sel = $( this ), current = $sel.val();
            $sel.empty().append( $( '<option>' ).val( '' ).text( '—' ) );
            for ( var i = 0; i < choices.length; i++ ) {
                var $opt = $( '<option>' ).val( choices[i].val ).text( choices[i].label );
                if ( choices[i].val === current ) $opt.prop( 'selected', true );
                $sel.append( $opt );
            }
        } );
    }

    $( document ).on( 'input change', '.bs-breakpoints-choices-setting textarea', function () {
        var $wrap = $( this ).closest( '.acf-field-object, .acf-field-settings, form' );
        syncTableSelectDefaults( $wrap );
    } );

    /* ═══════════════════════════════════════════
     *  Units allowed → tablo units select sync
     * ═══════════════════════════════════════════ */

    function syncTableUnitsDefaults( $wrap ) {
        var $allowedSelect = $wrap.find( '.bsbp-units-allowed-setting select[multiple]' );

        var selected = [];

        if ( $allowedSelect.length ) {
            selected = $allowedSelect.val() || [];
        }

        // Hiç seçim yoksa veya select bulunamadıysa → tüm option'ları al
        if ( selected.length === 0 && $allowedSelect.length ) {
            $allowedSelect.find( 'option' ).each( function () {
                selected.push( $( this ).val() );
            } );
        }

        // Hala boşsa tablodaki mevcut select'lerden option'ları koru (PHP render'dan gelen)
        if ( selected.length === 0 ) return;

        $wrap.find( '.bsbp-bp-default-cell .bsbp-def-units .bsbp-mini-units select' ).each( function () {
            var $sel = $( this ), current = $sel.val();
            $sel.empty();
            for ( var i = 0; i < selected.length; i++ ) {
                var u = selected[i];
                var $opt = $( '<option>' ).val( u ).text( u === '' ? '—' : u );
                if ( u === current ) $opt.prop( 'selected', true );
                $sel.append( $opt );
            }
        } );
    }

    $( document ).on( 'change', '.bsbp-units-allowed-setting select', function () {
        var $wrap = $( this ).closest( '.acf-field-object, .acf-field-settings, form' );
        syncTableUnitsDefaults( $wrap );
    } );

    /* ═══════════════════════════════════════════
     *  Global vs Tablo Default Priority UI
     *
     *  - Global'e değer girilince → tablodaki default field'lar
     *    gizlenip yerine global değer statik text olarak gösterilir
     *  - Tabloda herhangi bir BP'ye değer girilince → global
     *    default field gizlenip kısa mesaj gösterilir
     *  - İkisi de boşsa → ikisi de aktif
     * ═══════════════════════════════════════════ */

    function getGlobalDefaultValue( $wrap ) {
        var type = $wrap.find( '.bs-breakpoints-type-setting select' ).val();
        var val = '';

        switch ( type ) {
            case 'text':
            case 'number':
            case 'units':
                val = $.trim( $wrap.find( '.default_value-setting input[type="text"], .default_value-setting input[type="number"]' ).first().val() || '' );
                break;
            case 'color_picker':
                val = $.trim( $wrap.find( '.bsbp-color-setting input' ).first().val() || '' );
                break;
            case 'true_false':
                var $cb = $wrap.find( '.bsbp-bool-setting input[type="checkbox"]' ).first();
                val = $cb.is( ':checked' ) ? '1' : '';
                break;
            case 'select':
                val = $wrap.find( '.bsbp-global-select-default select' ).first().val() || '';
                break;
            case 'image':
                val = $.trim( $wrap.find( '.bsbp-image-setting input[type="hidden"]' ).first().val() || '' );
                break;
        }
        return val;
    }

    function hasAnyTableDefault( $wrap ) {
        var type = $wrap.find( '.bs-breakpoints-type-setting select' ).val();
        var hasVal = false;

        $wrap.find( '.bsbp-bp-default-cell .bsbp-def-' + type ).each( function () {
            var $cell = $( this );
            switch ( type ) {
                case 'text':
                case 'number':
                    if ( $.trim( $cell.find( 'input' ).val() || '' ) ) hasVal = true;
                    break;
                case 'units':
                    if ( $.trim( $cell.find( '.bsbp-mini-units input' ).val() || '' ) ) hasVal = true;
                    break;
                case 'true_false':
                    if ( $cell.find( 'input[type="checkbox"]' ).is( ':checked' ) ) hasVal = true;
                    break;
                case 'select':
                    if ( $cell.find( 'select' ).val() ) hasVal = true;
                    break;
                case 'color_picker':
                    if ( $.trim( $cell.find( 'input' ).val() || '' ) ) hasVal = true;
                    break;
                case 'image':
                    if ( $.trim( $cell.find( 'input[type="hidden"]' ).val() || '' ) ) hasVal = true;
                    break;
            }
        } );
        return hasVal;
    }

    function syncDefaultPriority( $wrap ) {
        var globalVal = getGlobalDefaultValue( $wrap );
        var tableHasVal = hasAnyTableDefault( $wrap );
        var type = $wrap.find( '.bs-breakpoints-type-setting select' ).val();

        // Overlay mesajları
        var $globalOverlay = $wrap.find( '.bsbp-global-default-overlay' );
        var $tableOverlay  = $wrap.find( '.bsbp-table-default-overlay' );

        // Global default alanları
        var $globalFields = $wrap.find( '.default_value-setting, .bsbp-color-setting, .bsbp-bool-setting, .bsbp-global-select-default, .bsbp-image-setting' ).filter( ':visible' );

        if ( ! $globalOverlay.length ) {
            $globalOverlay = $( '<div class="bsbp-global-default-overlay bsbp-priority-msg" style="display:none;"><em>⚡ Breakpoint bazlı default kullanılıyor</em></div>' );
            $wrap.find( '.bsbp-visibility-table' ).before( $globalOverlay );
        }

        if ( ! $tableOverlay.length ) {
            $tableOverlay = $( '<div class="bsbp-table-default-overlay"></div>' );
            $wrap.find( '.bsbp-table thead th.bsbp-bp-default-cell' ).append( $tableOverlay );
        }

        // Reset
        $wrap.find( '.bsbp-bp-default-cell .bsbp-def-type' ).removeClass( 'is-passive-content' );
        $globalOverlay.hide();
        $globalFields.removeClass( 'bsbp-field-passive' );

        if ( globalVal && ! tableHasVal ) {
            // Global aktif → tablo passive
            $wrap.find( '.bsbp-bp-default-cell .bsbp-def-' + type ).addClass( 'is-passive-content' );
        } else if ( tableHasVal && ! globalVal ) {
            // Tablo aktif → global passive
            $globalFields.addClass( 'bsbp-field-passive' );
            $globalOverlay.show();
        }
        // İkisi de doluysa veya ikisi de boşsa → ikisi de aktif
    }

    // Global default alanları değişince
    $( document ).on( 'input change', '.default_value-setting input, .bsbp-color-setting input, .bsbp-bool-setting input, .bsbp-global-select-default select, .bsbp-image-setting input', function () {
        var $wrap = $( this ).closest( '.acf-field-object, .acf-field-settings, form' );
        syncDefaultPriority( $wrap );
    } );

    // Tablo default alanları değişince
    $( document ).on( 'input change', '.bsbp-bp-default-cell input, .bsbp-bp-default-cell select', function () {
        // İlk td'deki enabled switch'i atla
        if ( $( this ).closest( 'td' ).is( ':first-child' ) ) return;
        var $wrap = $( this ).closest( '.acf-field-object, .acf-field-settings, form' );
        syncDefaultPriority( $wrap );
    } );

} )( jQuery );
