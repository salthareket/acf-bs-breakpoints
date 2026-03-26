( function ( $ ) {
    'use strict';

    $( document ).on( 'acf/setup_fields', function ( e, $el ) {
        $el.find( '.bsbp-type-setting select' ).each( function () {
            var $select = $( this );
            if ( $select.data( 'bsbp-bound' ) ) return;
            $select.data( 'bsbp-bound', true );

            var $wrap    = $select.closest( '.acf-field-object, .acf-field-settings, form' );
            var $choices = $wrap.find( '.bsbp-choices-setting' );
            var $units   = $wrap.find( '.bsbp-units-setting' );
            var $default = $wrap.find( '.bsbp-default-setting' );
            var $bpDefs  = $wrap.find( '.bsbp-bp-default-cell' );

            function toggle() {
                var val = $select.val();
                $choices.toggle( val === 'select' );
                $units.toggle( val === 'units' );

                var hideDefault = [ 'true_false', 'image', 'color_picker', 'select' ].includes( val );
                $default.toggle( ! hideDefault );
                $bpDefs.toggle( ! hideDefault );
            }

            $select.on( 'change', toggle );
            toggle();
        } );
    } );

    // Breakpoint row enabled/disabled visual feedback
    $( document ).on( 'change', '.bsbp-visibility-table input[type="checkbox"]', function () {
        var $row = $( this ).closest( 'tr.bsbp-bp-row' );
        $row.toggleClass( 'is-enabled', this.checked );
        $row.toggleClass( 'is-disabled', ! this.checked );
    } );

} )( jQuery );
