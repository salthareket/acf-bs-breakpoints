<?php
/**
 * Plugin Name: ACF Bootstrap Breakpoints
 * Description: Bootstrap breakpoint'leri için özel ACF alan tipi. Her breakpoint için ayrı değer girilebilir.
 * Version: 2.0.0
 * Author: Tolga Koçak
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'admin_init', function() {
    if ( ! class_exists( 'ACF' ) && ! class_exists( 'acf' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error"><p><strong>ACF Bootstrap Breakpoints:</strong> ACF eklentisi yüklü ve aktif olmalıdır.</p></div>';
        });
    }
});

add_action( 'acf/include_field_types', function() {

if ( class_exists( 'acf_bs_breakpoints' ) ) return;

class acf_bs_breakpoints extends \acf_field {

    public $show_in_rest  = true;
    public $defaults      = array( 'font_size' => 14 );
    public $l10n          = array();
    public $preview_image = '';
    public $env           = array();
    public $type          = '';
    public $breakpoints   = array();
    public $types         = array();

    public function __construct() {
        $this->name        = 'acf_bs_breakpoints';
        $this->label       = __( 'Breakpoints', 'acf_bs_breakpoints' );
        $this->category    = 'layout';
        $this->description = __( 'FIELD_DESCRIPTION', 'acf_bs_breakpoints' );

        $this->breakpoints = array(
            "xxxl" => "(>1600px)<br>Desktop PC, TV",
            "xxl"  => "(<=1599px)<br>Desktop PC",
            "xl"   => "(<=1399px)<br>Large Laptop",
            "lg"   => "(<=1199px)<br>Laptop",
            "md"   => "(<=991px)<br>Large Tablet",
            "sm"   => "(<=767px)<br>Tablet, Phone",
            "xs"   => "(<575px)<br>Phone",
        );

        $this->types = array(
            "text"         => "Text",
            "number"       => "Number",
            "units"        => "Units",
            "true_false"   => "True / false",
            "select"       => "Select",
            "color_picker" => "Color Picker",
            "image"        => "Image",
        );

        $this->l10n = array(
            'error' => __( 'Error! Please enter a higher value', 'acf_bs_breakpoints' ),
        );

        $this->env = array(
            'url'     => plugins_url() . '/acf-bs-breakpoints/',
            'version' => '2.0.0',
        );

        $this->preview_image = trailingslashit( $this->env['url'] ) . 'assets/images/field-preview-custom.png';

        add_filter( 'acf/update_value/type=' . $this->name, array( $this, 'acf_bs_breakpoints_update_value' ), 10, 3 );
        add_filter( 'acf/format_value/type=' . $this->name, array( $this, 'acf_bs_breakpoints_format_value' ), 10, 3 );

        parent::__construct();
    }

    /* ═══════════════════════════════════════════
     *  FIELD SETTINGS (Admin Panel)
     * ═══════════════════════════════════════════ */

    public function render_field_settings( $field ) {

        // 1. Field Type
        acf_render_field_setting( $field, array(
            'label'   => __( 'Field Type', 'acf_bs_breakpoints' ),
            'type'    => 'select',
            'name'    => 'acf_bs_breakpoints_type',
            'choices' => $this->types,
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'bs-breakpoints-type-setting' ),
        ));

        // 2. Responsive (Show Description)
        acf_render_field_setting( $field, array(
            'label'   => __( 'Responsive', 'acf_bs_breakpoints' ),
            'type'    => 'true_false',
            'name'    => 'show_description',
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'show-description-setting' ),
        ));

        // 3. Global Default Value
        acf_render_field_setting( $field, array(
            'label'   => __( 'Default Value', 'acf_bs_breakpoints' ),
            'type'    => 'text',
            'name'    => 'default_value',
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'default_value-setting' ),
        ));

        // 4. Choices (Select tipi için)
        acf_render_field_setting( $field, array(
            'label'   => __( 'Choices', 'acf_bs_breakpoints' ),
            'type'    => 'textarea',
            'name'    => 'acf_bs_breakpoints_choices',
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'bs-breakpoints-choices-setting' ),
        ));

        // 4b. Global Default Select (Select tipi için — Choices'a bağlı)
        $global_select_choices = array( '' => '—' );
        $raw_choices = isset( $field['acf_bs_breakpoints_choices'] ) ? $field['acf_bs_breakpoints_choices'] : '';
        if ( ! is_array( $raw_choices ) && ! empty( $raw_choices ) ) {
            $lines = explode( "\n", (string) $raw_choices );
            foreach ( $lines as $line ) {
                $line = trim( $line );
                if ( $line === '' ) continue;
                if ( strpos( $line, ':' ) !== false ) {
                    $parts = explode( ':', $line, 2 );
                    $global_select_choices[ trim( $parts[0] ) ] = trim( $parts[1] );
                } else {
                    $global_select_choices[ $line ] = $line;
                }
            }
        }
        acf_render_field_setting( $field, array(
            'label'   => __( 'Default Selection', 'acf_bs_breakpoints' ),
            'instructions' => __( 'Select tipi için varsayılan seçenek', 'acf_bs_breakpoints' ),
            'type'    => 'select',
            'name'    => 'bp_default_select',
            'choices' => $global_select_choices,
            'value'   => isset( $field['bp_default_select'] ) ? $field['bp_default_select'] : '',
            'wrapper' => array( 'class' => 'bs-breakpoints-choices-setting bsbp-global-select-default' ),
        ));

        // 5. Default Color (color_picker tipi için)
        acf_render_field_setting( $field, array(
            'label'   => __( 'Default Color', 'acf_bs_breakpoints' ),
            'type'    => 'color_picker',
            'name'    => 'bp_default_color',
            'wrapper' => array( 'class' => 'bsbp-color-setting' ),
        ));

        // 5b. Default True/False (true_false tipi için)
        acf_render_field_setting( $field, array(
            'label'   => __( 'Default State', 'acf_bs_breakpoints' ),
            'instructions' => __( 'Tüm breakpoint\'ler için varsayılan durum', 'acf_bs_breakpoints' ),
            'type'    => 'true_false',
            'name'    => 'bp_default_bool',
            'ui'      => 1,
            'wrapper' => array( 'class' => 'bsbp-bool-setting' ),
        ));

        // 6. Default Image (image tipi için)
        acf_render_field_setting( $field, array(
            'label'        => __( 'Default Image', 'acf_bs_breakpoints' ),
            'instructions' => __( 'Tüm breakpoint\'ler için varsayılan görsel', 'acf_bs_breakpoints' ),
            'type'         => 'image',
            'name'         => 'bp_default_image',
            'return_format'=> 'id',
            'preview_size' => 'thumbnail',
            'library'      => 'all',
            'wrapper'      => array( 'class' => 'bsbp-image-setting' ),
        ));

        // 7. Units Ayarları (sadece type=units seçildiğinde görünür)
        // Unit listesini units plugin API'sinden çek
        $all_units = class_exists( 'acf_field_units' ) && method_exists( 'acf_field_units', 'get_all_units' )
            ? acf_field_units::get_all_units()
            : array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'vmin', 'vmax', 'pt', 'cm', 'mm', 'in', 'ex', 'ch', 'fr', 'auto', '' );

        $unit_choices = array();
        foreach ( $all_units as $u ) {
            $unit_choices[ $u ] = $u === '' ? '(boş)' : $u;
        }

        $current_bp_allowed = isset( $field['bp_allowed_units'] ) ? (array) $field['bp_allowed_units'] : array();
        $current_bp_allowed = array_filter( $current_bp_allowed, function( $v ) { return $v !== null; });

        acf_render_field_setting( $field, array(
            'label'        => __( 'Kullanılacak Birimler', 'acf_bs_breakpoints' ),
            'instructions' => __( 'Boş bırakılırsa tüm birimler kullanılır. Sürükleyerek sıralayabilirsiniz.', 'acf_bs_breakpoints' ),
            'type'         => 'select',
            'name'         => 'bp_allowed_units',
            'choices'      => $unit_choices,
            'value'        => $current_bp_allowed,
            'multiple'     => 1,
            'ui'           => 1,
            'allow_null'   => 1,
            'placeholder'  => __( 'Birim seçin...', 'acf_bs_breakpoints' ),
            'wrapper'      => array( 'class' => 'bsbp-units-setting bsbp-units-allowed-setting' ),
        ));

        // Varsayılan birim — seçilenlere göre, boşsa tümü
        $default_unit_choices = array();
        $active_for_bp_default = ! empty( $current_bp_allowed ) ? $current_bp_allowed : $all_units;
        foreach ( $active_for_bp_default as $u ) {
            $default_unit_choices[ $u ] = $u === '' ? '(boş)' : $u;
        }

        $current_bp_default_unit = isset( $field['bp_default_unit'] ) ? $field['bp_default_unit'] : '';

        acf_render_field_setting( $field, array(
            'label'        => __( 'Varsayılan Birim', 'acf_bs_breakpoints' ),
            'instructions' => __( 'Units field açıldığında seçili gelecek birim', 'acf_bs_breakpoints' ),
            'type'         => 'select',
            'name'         => 'bp_default_unit',
            'choices'      => $default_unit_choices,
            'value'        => $current_bp_default_unit,
            'allow_null'   => 1,
            'placeholder'  => __( 'Önce birim seçin...', 'acf_bs_breakpoints' ),
            'wrapper'      => array( 'class' => 'bsbp-units-setting bsbp-units-default-setting' ),
        ));

        // JS: allowed_units (select2) değişince default_unit seçeneklerini güncelle + Tümünü Seç/Temizle
        ?>
        <script>
        (function($) {
            if (window._bsbpUnitsSettingsBound) return;
            window._bsbpUnitsSettingsBound = true;

            // Tümünü Seç / Temizle link'ini inject et
            function bsbpInjectSelectAllLink($wrap) {
                var $container = $wrap.find('.bsbp-units-allowed-setting');
                if (!$container.length || $container.find('.bsbp-select-all-wrap').length) return;

                var $select = $container.find('select[multiple]');
                if (!$select.length) return;

                var $link = $('<span class="bsbp-select-all-wrap" style="display:inline-block; margin-bottom:4px;">' +
                    '<a href="#" class="bsbp-select-all" style="font-size:12px; margin-right:8px;">Tümünü Seç</a>' +
                    '<a href="#" class="bsbp-clear-all" style="font-size:12px;">Temizle</a>' +
                    '</span>');

                $container.find('.acf-label').after($link);

                $link.on('click', '.bsbp-select-all', function(e) {
                    e.preventDefault();
                    var allVals = [];
                    $select.find('option').each(function() { allVals.push($(this).val()); });
                    $select.val(allVals).trigger('change');
                });

                $link.on('click', '.bsbp-clear-all', function(e) {
                    e.preventDefault();
                    $select.val([]).trigger('change');
                });
            }

            function bsbpSyncDefaultUnit($wrap) {
                var $allowedSelect = $wrap.find('.bsbp-units-allowed-setting select[multiple]');
                var $defaultSelect = $wrap.find('.bsbp-units-default-setting select');
                if (!$allowedSelect.length || !$defaultSelect.length) return;

                var currentDefault = $defaultSelect.val();
                var selected = $allowedSelect.val() || [];

                // Hiç seçim yoksa tüm birimleri göster (boş = tümü)
                var allVals = [];
                $allowedSelect.find('option').each(function() { allVals.push($(this).val()); });
                var units = selected.length > 0 ? selected : allVals;

                $defaultSelect.empty();
                $defaultSelect.append($('<option>').val('').text(''));
                $.each(units, function(i, u) {
                    var label = u === '' ? '(boş)' : u;
                    var $opt = $('<option>').val(u).text(label);
                    if (u === currentDefault) $opt.prop('selected', true);
                    $defaultSelect.append($opt);
                });
            }

            $(document).on('change', '.bsbp-units-allowed-setting select', function() {
                var $wrap = $(this).closest('.acf-field-object, .acf-field-settings, form');
                bsbpSyncDefaultUnit($wrap);
            });

            // Link'leri inject et
            $(document).ready(function() {
                $('.bsbp-units-allowed-setting').each(function() {
                    bsbpInjectSelectAllLink($(this).closest('.acf-field-object, .acf-field-settings, form'));
                });
            });

            if (typeof acf !== 'undefined' && typeof acf.add_action !== 'undefined') {
                acf.add_action('open_field_object', function(field) {
                    var $el = field && field.$el ? field.$el : null;
                    if ($el) bsbpInjectSelectAllLink($el);
                });
            }
        })(jQuery);
        </script>
        <?php

        // 8. Breakpoint Visibility & Custom Labels & Per-BP Defaults — TABLO
        $current_type = isset( $field['acf_bs_breakpoints_type'] ) ? $field['acf_bs_breakpoints_type'] : 'text';

        // Select choices parse (tablo default hücresinde select tipi için)
        $table_select_choices = array();
        if ( $current_type === 'select' ) {
            $raw = isset( $field['acf_bs_breakpoints_choices'] ) ? $field['acf_bs_breakpoints_choices'] : '';
            if ( ! is_array( $raw ) && ! empty( $raw ) ) {
                $raw = explode( "\n", (string) $raw );
            }
            if ( is_array( $raw ) ) {
                foreach ( $raw as $k => $line ) {
                    $line = trim( (string) $line );
                    if ( $line === '' ) continue;
                    if ( strpos( $line, ':' ) !== false ) {
                        $parts = explode( ':', $line, 2 );
                        $table_select_choices[ trim( $parts[0] ) ] = trim( $parts[1] );
                    } else {
                        $table_select_choices[ $k ] = $line;
                    }
                }
            }
        }

        // Units listesi (tablo default hücresinde — her zaman hesapla, tip fark etmez)
        $bp_allowed = isset( $field['bp_allowed_units'] ) ? array_filter( (array) $field['bp_allowed_units'] ) : array();
        if ( ! empty( $bp_allowed ) ) {
            $table_units = $bp_allowed;
        } else {
            $table_units = class_exists( 'acf_field_units' ) && method_exists( 'acf_field_units', 'get_all_units' )
                ? acf_field_units::get_all_units()
                : array( 'px', '%', 'em', 'rem', 'vw', 'vh' );
        }
        ?>
        <div class="acf-field bsbp-visibility-table" data-name="bp_visibility">
            <div class="acf-label">
                <label><?php esc_html_e( 'Breakpoints', 'acf_bs_breakpoints' ); ?></label>
                <p class="description"><?php esc_html_e( 'Her breakpoint için görünürlük, özel başlık ve varsayılan değer ayarlayın.', 'acf_bs_breakpoints' ); ?></p>
            </div>
            <div class="acf-input">
                <table class="bsbp-table widefat striped">
                    <thead>
                        <tr>
                            <th style="width:50px"><?php esc_html_e( 'Aktif', 'acf_bs_breakpoints' ); ?></th>
                            <th style="width:100px"><?php esc_html_e( 'Key', 'acf_bs_breakpoints' ); ?></th>
                            <th><?php esc_html_e( 'Özel Başlık', 'acf_bs_breakpoints' ); ?></th>
                            <th class="bsbp-bp-default-cell"><?php esc_html_e( 'Varsayılan Değer', 'acf_bs_breakpoints' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $this->breakpoints as $key => $desc ) :
                            $e_key = "bp_enabled_{$key}";
                            $l_key = "bp_label_{$key}";
                            $d_key = "bp_default_{$key}";
                            $is_e  = isset( $field[ $e_key ] ) ? $field[ $e_key ] : 1;
                            $label = isset( $field[ $l_key ] ) ? $field[ $l_key ] : '';
                            $def   = isset( $field[ $d_key ] ) ? $field[ $d_key ] : '';
                            $row_cls = $is_e ? 'bsbp-bp-row is-enabled' : 'bsbp-bp-row is-disabled';
                        ?>
                        <tr class="<?php echo esc_attr( $row_cls ); ?>">
                            <td>
                                <?php acf_render_field_setting( $field, array(
                                    'type'     => 'true_false',
                                    'name'     => $e_key,
                                    'ui'       => 1,
                                    'value'    => $is_e,
                                    'no_label' => 1,
                                ), true ); ?>
                            </td>
                            <td><code><?php echo esc_html( $key ); ?></code></td>
                            <td>
                                <?php acf_render_field_setting( $field, array(
                                    'type'        => 'text',
                                    'name'        => $l_key,
                                    'placeholder' => $key,
                                    'no_label'    => 1,
                                ), true ); ?>
                            </td>
                            <td class="bsbp-bp-default-cell">
                                <?php $this->render_bp_default_cell( $field, $current_type, $d_key, $def, $key, $table_select_choices, $table_units ); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * BP tablosundaki "Varsayılan Değer" hücresini — TÜM tipleri gizli render et, aktif olanı göster
     */
    private function render_bp_default_cell( $field, $current_type, $d_key, $def, $bp_key, $choices, $units ) {
        $all_types = array( 'text', 'number', 'units', 'true_false', 'select', 'color_picker', 'image' );

        foreach ( $all_types as $type ) {
            $is_active = ( $type === $current_type ) ? ' is-active' : '';
            echo '<div class="bsbp-def-type bsbp-def-' . esc_attr( $type ) . $is_active . '" data-def-type="' . esc_attr( $type ) . '">';

            switch ( $type ) {
                case 'number':
                    acf_render_field_setting( $field, array(
                        'type' => 'number', 'name' => $d_key, 'placeholder' => '', 'no_label' => 1,
                    ), true );
                    break;

                case 'true_false':
                    acf_render_field_setting( $field, array(
                        'type' => 'true_false', 'name' => $d_key, 'ui' => 1, 'value' => $def, 'no_label' => 1,
                    ), true );
                    break;

                case 'select':
                    $sc = array( '' => '—' ) + $choices;
                    acf_render_field_setting( $field, array(
                        'type' => 'select', 'name' => $d_key, 'choices' => $sc, 'value' => $def, 'no_label' => 1,
                    ), true );
                    break;

                case 'color_picker':
                    acf_render_field_setting( $field, array(
                        'type' => 'color_picker', 'name' => $d_key, 'value' => $def, 'no_label' => 1,
                    ), true );
                    break;

                case 'image':
                    acf_render_field_setting( $field, array(
                        'type' => 'image', 'name' => $d_key, 'return_format' => 'id',
                        'preview_size' => 'thumbnail', 'library' => 'all', 'value' => $def, 'no_label' => 1,
                    ), true );
                    break;

                case 'units':
                    $u_val = ''; $u_unit = '';
                    if ( is_array( $def ) ) {
                        $u_val  = isset( $def['value'] ) ? $def['value'] : '';
                        $u_unit = isset( $def['unit'] )  ? $def['unit']  : '';
                    } elseif ( is_string( $def ) ) {
                        $u_val = $def;
                    }
                    $prefix = isset( $field['prefix'] ) ? $field['prefix'] : '';
                    $name_base = $prefix ? "{$prefix}[{$d_key}]" : $d_key;
                    ?>
                    <div class="bsbp-mini-units">
                        <input type="text" name="<?php echo esc_attr( $name_base ); ?>[value]" value="<?php echo esc_attr( $u_val ); ?>" placeholder="0" />
                        <select name="<?php echo esc_attr( $name_base ); ?>[unit]">
                            <?php foreach ( $units as $u_opt ) : ?>
                                <option value="<?php echo esc_attr( $u_opt ); ?>" <?php selected( $u_unit, $u_opt ); ?>><?php echo esc_html( $u_opt === '' ? '—' : $u_opt ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php
                    break;

                default: // text
                    acf_render_field_setting( $field, array(
                        'type' => 'text', 'name' => $d_key, 'placeholder' => '', 'no_label' => 1,
                    ), true );
                    break;
            }

            echo '</div>';
        }
    }

    /* ═══════════════════════════════════════════
     *  RENDER FIELD (Edit Screen)
     * ═══════════════════════════════════════════ */

    public function render_field( $field ) {
        $type = isset( $field['acf_bs_breakpoints_type'] ) ? $field['acf_bs_breakpoints_type'] : 'text';
        $this->type = $type;

        // Select choices parse
        if ( $type === 'select' ) {
            if ( isset( $field['choices'] ) && is_array( $field['choices'] ) ) {
                $choices = $field['choices'];
            } else {
                $options = isset( $field['acf_bs_breakpoints_choices'] ) ? $field['acf_bs_breakpoints_choices'] : '';
                if ( ! is_array( $options ) && ! empty( $options ) ) {
                    $options = explode( "\n", (string) $options );
                }
                $choices = array();
                if ( is_array( $options ) ) {
                    foreach ( $options as $k => $line ) {
                        $line = trim( (string) $line );
                        if ( $line === '' ) continue;
                        if ( strpos( $line, ':' ) !== false ) {
                            $parts = explode( ':', $line, 2 );
                            $choices[ trim( $parts[0] ) ] = trim( $parts[1] );
                        } else {
                            $choices[ $k ] = $line;
                        }
                    }
                }
            }
        }

        $name_root   = $field['name'];
        $value       = isset( $field['value'] ) ? $field['value'] : array();
        $description = ! empty( $field['show_description'] );

        // Breakpoint key'lerini name_root'tan temizle
        foreach ( $this->breakpoints as $bpKey => $_ ) {
            $name_root = str_replace( '[' . $bpKey . ']', '', $name_root );
        }

        ?>
        <div class="acf-fields acf-bs-breakpoints-fields d-flex">
        <?php foreach ( $this->breakpoints as $bpKey => $bpLabel ) :

            // Visibility kontrolü
            $enabled = isset( $field["bp_enabled_{$bpKey}"] ) ? $field["bp_enabled_{$bpKey}"] : 1;
            if ( ! $enabled ) continue;

            // Custom label
            $display_label = ! empty( $field["bp_label_{$bpKey}"] ) ? $field["bp_label_{$bpKey}"] : $bpKey;

            $name = $name_root . '[' . $bpKey . ']';

            // Değer: value > field[bpKey] (eski compat) > bp_default_X > type-specific default > global default
            $val = isset( $value[ $bpKey ] ) ? $value[ $bpKey ] : ( isset( $field[ $bpKey ] ) ? $field[ $bpKey ] : '' );
            if ( $val === '' && ! empty( $field["bp_default_{$bpKey}"] ) ) {
                $val = $field["bp_default_{$bpKey}"];
            }
            if ( $val === '' && $type === 'color_picker' && ! empty( $field['bp_default_color'] ) ) {
                $val = $field['bp_default_color'];
            }
            if ( $val === '' && $type === 'true_false' && isset( $field['bp_default_bool'] ) ) {
                $val = $field['bp_default_bool'];
            }
            if ( $val === '' && $type === 'select' && ! empty( $field['bp_default_select'] ) ) {
                $val = $field['bp_default_select'];
            }
            if ( $val === '' && $type === 'image' && ! empty( $field['bp_default_image'] ) ) {
                $val = $field['bp_default_image'];
            }
            if ( $val === '' && ! empty( $field['default_value'] ) ) {
                $val = $field['default_value'];
            }

        ?>
            <div class="acf-field acf-field-<?php echo esc_attr( $type ); ?>" data-type="<?php echo esc_attr( $type ); ?>" style="flex: 1; min-width: 120px;">
                <div class="acf-label">
                    <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $display_label ); ?></label>
                    <?php if ( $description ) : ?>
                        <p class="description"><?php echo $bpLabel; ?></p>
                    <?php endif; ?>
                </div>
                <div class="acf-input">

                    <?php if ( $type === 'text' ) : ?>
                        <input type="text" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>" />

                    <?php elseif ( $type === 'number' ) : ?>
                        <input type="number" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $val ); ?>" />

                    <?php elseif ( $type === 'true_false' ) : ?>
                        <div class="acf-true-false">
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="0">
                            <label>
                                <input type="checkbox"
                                    id="<?php echo esc_attr( $name ); ?>"
                                    name="<?php echo esc_attr( $name ); ?>"
                                    value="1"
                                    class="acf-switch-input"
                                    autocomplete="off"
                                    <?php checked( (string) $val, '1' ); ?>
                                >
                                <div class="acf-switch <?php echo $val ? '-on' : ''; ?>">
                                    <span class="acf-switch-on">Evet</span>
                                    <span class="acf-switch-off">Hayır</span>
                                    <div class="acf-switch-slider"></div>
                                </div>
                            </label>
                        </div>

                    <?php elseif ( $type === 'select' ) : ?>
                        <select id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>">
                            <?php if ( ! empty( $choices ) ) : foreach ( $choices as $ov => $ol ) : ?>
                                <option value="<?php echo esc_attr( $ov ); ?>" <?php selected( (string) $val, (string) $ov ); ?>><?php echo esc_html( $ol ); ?></option>
                            <?php endforeach; endif; ?>
                        </select>

                    <?php elseif ( $type === 'color_picker' ) : ?>
                        <div class="acf-color-picker">
                            <input type="text"
                                id="<?php echo esc_attr( $name ); ?>"
                                name="<?php echo esc_attr( $name ); ?>"
                                value="<?php echo esc_attr( $val ); ?>"
                                data-alpha-skip-debounce="1"
                            />
                        </div>

                    <?php elseif ( $type === 'image' ) : ?>
                        <?php include __DIR__ . "/fields/image.php"; ?>

                    <?php elseif ( $type === 'units' ) : ?>
                        <?php
                        acf_render_field( array(
                            'key'               => $name,
                            'label'             => '',
                            'name'              => $name,
                            'value'             => $val,
                            'type'              => 'units',
                            'instructions'      => '',
                            'required'          => 0,
                            'conditional_logic' => 0,
                            'wrapper'           => array( 'width' => '100%' ),
                            'allowed_units'     => isset( $field['bp_allowed_units'] ) ? (array) $field['bp_allowed_units'] : array(),
                            'default_unit'      => isset( $field['bp_default_unit'] ) ? $field['bp_default_unit'] : '',
                        ));
                        ?>

                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php
    }

    /* ═══════════════════════════════════════════
     *  UPDATE VALUE (Save)
     * ═══════════════════════════════════════════ */

    public function acf_bs_breakpoints_update_value( $value, $post_id, $field ) {
        if ( isset( $field['acf_bs_breakpoints_type'] ) && $field['acf_bs_breakpoints_type'] === 'image' && ! empty( $value ) ) {
            $ids = array_values( (array) $value );
            $ids = array_filter( $ids, static function( $v ) {
                return $v !== null && $v !== '' && ( ! is_array( $v ) || ! empty( $v ) );
            });
            if ( $ids ) {
                $ids = array_values( $ids );
                if ( count( $ids ) === 1 ) {
                    $attachment_id = (int) $ids[0];
                    if ( ! metadata_exists( 'post', $attachment_id, '_wp_attachment_metadata' ) ) {
                        $this->regenerate_attachment_metadata( $attachment_id );
                    }
                } else {
                    foreach ( $ids as $id ) {
                        $this->remove_image_sizes( (int) $id );
                    }
                }
            }
        }
        return $value;
    }

    /* ═══════════════════════════════════════════
     *  FORMAT VALUE (Frontend Output)
     * ═══════════════════════════════════════════ */

    public function acf_bs_breakpoints_format_value( $value, $post_id, $field ) {
        if ( isset( $field['acf_bs_breakpoints_type'] ) && $field['acf_bs_breakpoints_type'] === 'image' && ! empty( $value ) ) {
            $value = $this->get_base_data( $value );
        }
        return $value;
    }

    /** id + url doldur */
    public function get_base_data( $value = 0 ) {
        if ( $value && is_array( $value ) ) {
            $ids = array_values( $value );
            $ids = array_filter( $ids, static function( $v ) {
                return $v !== null && $v !== '' && ( ! is_array( $v ) || ! empty( $v ) );
            });
            if ( $ids ) {
                $ids          = array_values( $ids );
                $attachmentID = (int) $ids[0];
                $value['id']  = $attachmentID;
                $value['url'] = wp_get_attachment_url( $attachmentID );
            }
        }
        return $value;
    }

    /* ═══════════════════════════════════════════
     *  ADMIN ENQUEUE
     * ═══════════════════════════════════════════ */

    public function input_admin_enqueue_scripts() {
        $url     = trailingslashit( $this->env['url'] );
        $version = $this->env['version'];

        wp_register_script( 'acf_bs_breakpoints', "{$url}assets/js/field.js", array( 'acf-input' ), $version, true );
        wp_register_style( 'acf_bs_breakpoints', "{$url}assets/css/field.css", array( 'acf-input' ), $version );

        wp_enqueue_script( 'acf_bs_breakpoints' );
        wp_enqueue_style( 'acf_bs_breakpoints' );

        wp_enqueue_script( 'bs_breakpoints_admin', "{$url}assets/js/admin.js", array( 'jquery' ), $version, true );
        wp_localize_script( 'bs_breakpoints_admin', 'bs_breakpoints_vars', array(
            'type_selector'   => '.bs-breakpoints-type-setting select',
            'choices_wrapper' => '.bs-breakpoints-choices-setting',
        ));

        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
    }

    /* ═══════════════════════════════════════════
     *  IMAGE HELPERS
     * ═══════════════════════════════════════════ */

    private function regenerate_attachment_metadata( $attachment_id ) {
        $attachment_id = (int) $attachment_id;
        $file = get_attached_file( $attachment_id );
        if ( ! $file || ! file_exists( $file ) ) return;

        $metadata = wp_generate_attachment_metadata( $attachment_id, $file );
        if ( ! is_wp_error( $metadata ) && ! empty( $metadata ) ) {
            wp_update_attachment_metadata( $attachment_id, $metadata );
        }
    }

    public function remove_image_sizes( $attachment_id ) {
        $attachment_id = (int) $attachment_id;
        $metadata = wp_get_attachment_metadata( $attachment_id );
        if ( ! is_array( $metadata ) || empty( $metadata['sizes'] ) ) return;

        $base_file = get_attached_file( $attachment_id );
        if ( ! $base_file ) return;

        $base_dir = wp_normalize_path( dirname( $base_file ) );

        foreach ( $metadata['sizes'] as $size => $info ) {
            if ( $size === 'thumbnail' ) continue;

            $file_name = isset( $info['file'] ) ? $info['file'] : '';
            if ( ! $file_name ) {
                unset( $metadata['sizes'][ $size ] );
                continue;
            }

            $full_path = wp_normalize_path( trailingslashit( $base_dir ) . $file_name );
            if ( file_exists( $full_path ) && is_file( $full_path ) ) {
                @unlink( $full_path );
            }
            unset( $metadata['sizes'][ $size ] );
        }

        wp_update_attachment_metadata( $attachment_id, $metadata );
    }
}

new acf_bs_breakpoints();

}); // end acf/include_field_types
