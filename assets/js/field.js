( function ( $ ) {
    'use strict';

    function init_field( $field ) {
        // Color Picker
        $field.find( '.bsbp-color' ).each( function () {
            if ( $( this ).hasClass( 'wp-color-picker' ) ) return;
            $( this ).wpColorPicker( {
                change: function ( event, ui ) {
                    $( this ).val( ui.color.toString() ).trigger( 'change' );
                },
                clear: function () {
                    $( this ).val( '' ).trigger( 'change' );
                },
            } );
        } );

        // Image uploader (ACF native)
        if ( typeof acf !== 'undefined' && acf.fields && acf.fields.image ) {
            $field.find( '.acf-image-uploader' ).each( function () {
                var $uploader = $( this );
                if ( $uploader.data( 'bsbp-init' ) ) return;
                $uploader.data( 'bsbp-init', true );
                acf.fields.image.render( $uploader );
            } );
        }
    }

    // Admin settings panel: type/choices/units visibility
    function init_settings( $field ) {
        var $type    = $field.find( '[data-name="bp_type"] select, [data-name="bp_type"] input' );
        var $choices = $field.find( '.bsbp-choices-setting' );
        var $units   = $field.find( '.bsbp-units-setting' );
        var $default = $field.find( '.bsbp-default-setting' );

        function toggle() {
            var val = $type.val();
            $choices.toggle( val === 'select' );
            $units.toggle( val === 'units' );
            $default.toggle( ! [ 'true_false', 'image', 'color_picker' ].includes( val ) );
        }

        $type.on( 'change', toggle );
        toggle();
    }

    if ( typeof acf !== 'undefined' && typeof acf.add_action !== 'undefined' ) {
        acf.add_action( 'ready_field/type=acf_bs_breakpoints', init_field );
        acf.add_action( 'append_field/type=acf_bs_breakpoints', init_field );
        acf.add_action( 'ready_field_settings/type=acf_bs_breakpoints', init_settings );
    }

} )( jQuery );
