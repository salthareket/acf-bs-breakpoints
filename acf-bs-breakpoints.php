<?php
/**
 * Plugin Name: ACF Bootstrap Breakpoints
 * Description: Bootstrap breakpoint'leri için özel ACF alan tipi. Her breakpoint için ayrı değer girilebilir.
 * Version: 1.2.0
 * Author: Tolga Koçak
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'acf/include_field_types', function() {
    if ( class_exists( 'acf_bs_breakpoints' ) ) return;

    class acf_bs_breakpoints extends \acf_field {

        public $show_in_rest  = true;
        public $defaults      = [];
        public $l10n          = [];
        public $preview_image = '';
        public $env           = [];
        public $breakpoints   = [];
        public $types         = [];

        public function __construct() {
            $this->name        = 'acf_bs_breakpoints';
            $this->label       = __( 'BS Breakpoints', 'acf_bs_breakpoints' );
            $this->category    = 'layout';
            $this->description = __( 'Her Bootstrap breakpoint için ayrı değer tanımla', 'acf_bs_breakpoints' );

            $this->breakpoints = [
                'xxxl' => [ 'range' => '&gt;1600px',  'device' => 'Desktop PC / TV' ],
                'xxl'  => [ 'range' => '≤1599px',     'device' => 'Desktop PC' ],
                'xl'   => [ 'range' => '≤1399px',     'device' => 'Large Laptop' ],
                'lg'   => [ 'range' => '≤1199px',     'device' => 'Laptop' ],
                'md'   => [ 'range' => '≤991px',      'device' => 'Large Tablet' ],
                'sm'   => [ 'range' => '≤767px',      'device' => 'Tablet / Phone' ],
                'xs'   => [ 'range' => '&lt;575px',   'device' => 'Phone' ],
            ];

            $this->types = [
                'text'         => 'Text',
                'number'       => 'Number',
                'units'        => 'Units (px, em, %...)',
                'true_false'   => 'True / False',
                'select'       => 'Select',
                'color_picker' => 'Color Picker',
                'image'        => 'Image',
            ];

            $this->env = [
                'url'     => plugin_dir_url( __FILE__ ),
                'version' => '1.2.0',
            ];

            $this->preview_image = $this->env['url'] . 'assets/images/field-preview-custom.png';

            add_filter( 'acf/update_value/type=' . $this->name, [ $this, 'update_value' ], 10, 3 );
            add_filter( 'acf/format_value/type=' . $this->name, [ $this, 'format_value' ], 10, 3 );

            parent::__construct();
        }

        // ─── Field Settings (ACF admin panel) ────────────────────────────────────

        public function render_field_settings( $field ) {

            // Field Type
            acf_render_field_setting( $field, [
                'label'   => __( 'Field Type', 'acf_bs_breakpoints' ),
                'type'    => 'select',
                'name'    => 'bp_type',
                'choices' => $this->types,
                'layout'  => 'horizontal',
                'wrapper' => [ 'class' => 'bsbp-type-setting' ],
            ] );

            // Responsive açıklama göster/gizle
            acf_render_field_setting( $field, [
                'label'   => __( 'Show Breakpoint Info', 'acf_bs_breakpoints' ),
                'type'    => 'true_false',
                'name'    => 'bp_show_info',
                'ui'      => 1,
                'layout'  => 'horizontal',
                'wrapper' => [ 'class' => 'bsbp-show-info-setting' ],
            ] );

            // Global default
            acf_render_field_setting( $field, [
                'label'   => __( 'Default Value', 'acf_bs_breakpoints' ),
                'type'    => 'text',
                'name'    => 'default_value',
                'layout'  => 'horizontal',
                'wrapper' => [ 'class' => 'bsbp-default-setting' ],
            ] );

            // Select choices
            acf_render_field_setting( $field, [
                'label'        => __( 'Choices', 'acf_bs_breakpoints' ),
                'instructions' => __( 'Her satıra bir seçenek. value : Label formatında.', 'acf_bs_breakpoints' ),
                'type'         => 'textarea',
                'name'         => 'bp_choices',
                'layout'       => 'horizontal',
                'wrapper'      => [ 'class' => 'bsbp-choices-setting' ],
            ] );

            // Units suffix
            acf_render_field_setting( $field, [
                'label'        => __( 'Unit Suffixes', 'acf_bs_breakpoints' ),
                'instructions' => __( 'Virgülle ayır: px, em, rem, %', 'acf_bs_breakpoints' ),
                'type'         => 'text',
                'name'         => 'bp_units',
                'default'      => 'px, em, rem, %',
                'layout'       => 'horizontal',
                'wrapper'      => [ 'class' => 'bsbp-units-setting' ],
            ] );

            // Breakpoint visibility & custom labels
            ?>
            <div class="acf-field bsbp-visibility-table">
                <div class="acf-label">
                    <label><?php _e( 'Breakpoint Görünürlük & Başlıklar', 'acf_bs_breakpoints' ); ?></label>
                    <p class="description"><?php _e( 'Hangi breakpoint\'lerin gösterileceğini ve özel başlıklarını ayarla.', 'acf_bs_breakpoints' ); ?></p>
                </div>
                <div class="acf-input">
                    <table class="widefat bsbp-table">
                        <thead>
                            <tr>
                                <th><?php _e( 'Aktif', 'acf_bs_breakpoints' ); ?></th>
                                <th><?php _e( 'Key', 'acf_bs_breakpoints' ); ?></th>
                                <th><?php _e( 'Aralık', 'acf_bs_breakpoints' ); ?></th>
                                <th><?php _e( 'Cihaz', 'acf_bs_breakpoints' ); ?></th>
                                <th><?php _e( 'Özel Başlık', 'acf_bs_breakpoints' ); ?></th>
                                <th><?php _e( 'Breakpoint Default', 'acf_bs_breakpoints' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $this->breakpoints as $key => $info ) :
                                $enabled = isset( $field[ "bp_enabled_{$key}" ] ) ? (int) $field[ "bp_enabled_{$key}" ] : 1;
                            ?>
                            <tr class="bsbp-bp-row <?php echo $enabled ? 'is-enabled' : 'is-disabled'; ?>">
                                <td>
                                    <?php acf_render_field_setting( $field, [
                                        'type'     => 'true_false',
                                        'name'     => "bp_enabled_{$key}",
                                        'ui'       => 1,
                                        'value'    => $enabled,
                                        'no_label' => 1,
                                    ], true ); ?>
                                </td>
                                <td><code class="bsbp-key-badge"><?php echo esc_html( $key ); ?></code></td>
                                <td><span class="bsbp-range"><?php echo $info['range']; ?></span></td>
                                <td><span class="bsbp-device"><?php echo esc_html( $info['device'] ); ?></span></td>
                                <td>
                                    <?php acf_render_field_setting( $field, [
                                        'type'        => 'text',
                                        'name'        => "bp_label_{$key}",
                                        'placeholder' => $key,
                                        'no_label'    => 1,
                                    ], true ); ?>
                                </td>
                                <td class="bsbp-bp-default-cell">
                                    <?php acf_render_field_setting( $field, [
                                        'type'        => 'text',
                                        'name'        => "bp_default_{$key}",
                                        'placeholder' => __( 'default...', 'acf_bs_breakpoints' ),
                                        'no_label'    => 1,
                                        'wrapper'     => [ 'class' => 'bsbp-bp-default' ],
                                    ], true ); ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php
        }

        // ─── Field Render (post edit screen) ─────────────────────────────────────

        public function render_field( $field ) {
            $type      = $field['bp_type'] ?? 'text';
            $show_info = ! empty( $field['bp_show_info'] );
            $value     = is_array( $field['value'] ?? null ) ? $field['value'] : [];
            $choices   = $this->parse_choices( $field['bp_choices'] ?? '' );
            $units     = $this->parse_units( $field['bp_units'] ?? 'px, em, rem, %' );
            $name_base = $this->get_name_base( $field['name'], array_keys( $this->breakpoints ) );

            $active_bps = [];
            foreach ( $this->breakpoints as $key => $info ) {
                if ( isset( $field["bp_enabled_{$key}"] ) && ! $field["bp_enabled_{$key}"] ) continue;
                $active_bps[ $key ] = $info;
            }

            if ( empty( $active_bps ) ) {
                echo '<p class="bsbp-empty">' . __( 'Hiçbir breakpoint aktif değil.', 'acf_bs_breakpoints' ) . '</p>';
                return;
            }

            $col_count = count( $active_bps );
            ?>
            <div class="bsbp-field-wrap" data-type="<?php echo esc_attr( $type ); ?>" data-cols="<?php echo $col_count; ?>">
                <?php foreach ( $active_bps as $key => $info ) :
                    $label   = ! empty( $field["bp_label_{$key}"] ) ? $field["bp_label_{$key}"] : strtoupper( $key );
                    $bp_def  = $field["bp_default_{$key}"] ?? '';
                    $val     = $value[ $key ] ?? $bp_def ?: ( $field['default_value'] ?? '' );
                    $name    = $name_base . '[' . $key . ']';
                ?>
                <div class="bsbp-col" data-bp="<?php echo esc_attr( $key ); ?>">
                    <div class="bsbp-col-header">
                        <span class="bsbp-label"><?php echo esc_html( $label ); ?></span>
                        <?php if ( $show_info ) : ?>
                            <span class="bsbp-info" title="<?php echo esc_attr( $info['device'] ); ?>">
                                <?php echo $info['range']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="bsbp-col-input">
                        <?php $this->render_input( $type, $name, $val, $choices, $units, $field ); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php
        }

        private function render_input( $type, $name, $val, $choices, $units, $field ) {
            switch ( $type ) {
                case 'text':
                    echo '<input type="text" class="bsbp-input" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" />';
                    break;

                case 'number':
                    echo '<input type="number" class="bsbp-input" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" />';
                    break;

                case 'units':
                    // acf-unit-field plugin'i yüklü değilse fallback olarak text input göster
                    if ( ! class_exists('acf_field_units') ) {
                        echo '<input type="text" class="bsbp-input" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" placeholder="e.g. 16px" />';
                        break;
                    }
                    $num_val  = preg_replace( '/[^0-9.\-]/', '', $val );
                    $unit_val = preg_replace( '/[0-9.\-\s]/', '', $val );
                    echo '<div class="bsbp-units-wrap">';
                    echo '<input type="number" class="bsbp-input bsbp-units-num" name="' . esc_attr( $name ) . '[num]" value="' . esc_attr( $num_val ) . '" />';
                    echo '<select class="bsbp-units-select" name="' . esc_attr( $name ) . '[unit]">';
                    foreach ( $units as $u ) {
                        echo '<option value="' . esc_attr( $u ) . '" ' . selected( $unit_val, $u, false ) . '>' . esc_html( $u ) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                    break;

                case 'true_false':
                    echo '<label class="bsbp-toggle">';
                    echo '<input type="hidden" name="' . esc_attr( $name ) . '" value="0" />';
                    echo '<input type="checkbox" class="bsbp-toggle-input" name="' . esc_attr( $name ) . '" value="1" ' . checked( $val, '1', false ) . ' />';
                    echo '<span class="bsbp-toggle-slider"></span>';
                    echo '</label>';
                    break;

                case 'select':
                    echo '<select class="bsbp-select" name="' . esc_attr( $name ) . '">';
                    foreach ( $choices as $ov => $ol ) {
                        echo '<option value="' . esc_attr( $ov ) . '" ' . selected( $val, $ov, false ) . '>' . esc_html( $ol ) . '</option>';
                    }
                    echo '</select>';
                    break;

                case 'color_picker':
                    echo '<input type="text" class="bsbp-input bsbp-color" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" data-alpha-enabled="true" />';
                    break;

                case 'image':
                    include plugin_dir_path( __FILE__ ) . 'fields/image.php';
                    break;
            }
        }

        // ─── Scripts & Styles ─────────────────────────────────────────────────────

        public function input_admin_enqueue_scripts() {
            $url = $this->env['url'];
            $ver = $this->env['version'];

            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'wp-color-picker-alpha',
                $url . 'assets/js/wp-color-picker-alpha.min.js',
                [ 'wp-color-picker' ], $ver, true
            );

            wp_enqueue_style( 'acf-bsbp', $url . 'assets/css/field.css', [ 'acf-input' ], $ver );
            wp_enqueue_script( 'acf-bsbp', $url . 'assets/js/field.js', [ 'acf-input', 'wp-color-picker' ], $ver, true );
        }

        public function field_group_admin_enqueue_scripts() {
            $url = $this->env['url'];
            $ver = $this->env['version'];

            wp_enqueue_style( 'acf-bsbp-admin', $url . 'assets/css/field.css', [ 'acf-input' ], $ver );
            wp_enqueue_script( 'acf-bsbp-admin', $url . 'assets/js/admin.js', [ 'jquery', 'acf-input' ], $ver, true );
        }

        // ─── Value Hooks ──────────────────────────────────────────────────────────

        public function update_value( $value, $post_id, $field ) {
            if ( ! is_array( $value ) ) return $value;

            // units type: num+unit birleştir
            if ( ( $field['bp_type'] ?? '' ) === 'units' ) {
                $merged = [];
                foreach ( $value as $key => $sub ) {
                    if ( is_array( $sub ) && isset( $sub['num'], $sub['unit'] ) ) {
                        $merged[ $key ] = trim( $sub['num'] ) . trim( $sub['unit'] );
                    } else {
                        $merged[ $key ] = $sub;
                    }
                }
                return $merged;
            }

            return $value;
        }

        public function format_value( $value, $post_id, $field ) {
            if ( ! is_array( $value ) ) return $value;

            if ( ( $field['bp_type'] ?? '' ) === 'image' ) {
                foreach ( $value as $key => $id ) {
                    if ( is_numeric( $id ) && $id > 0 ) {
                        $value[ $key ] = [
                            'id'  => (int) $id,
                            'url' => wp_get_attachment_url( (int) $id ),
                            'alt' => get_post_meta( (int) $id, '_wp_attachment_image_alt', true ),
                        ];
                    }
                }
            }

            return $value;
        }

        // ─── Helpers ──────────────────────────────────────────────────────────────

        private function parse_choices( string $raw ): array {
            $choices = [];
            foreach ( explode( "\n", $raw ) as $i => $line ) {
                $line = trim( $line );
                if ( $line === '' ) continue;
                if ( strpos( $line, ':' ) !== false ) {
                    [ $v, $l ] = explode( ':', $line, 2 );
                    $choices[ trim( $v ) ] = trim( $l );
                } else {
                    $choices[ $i ] = $line;
                }
            }
            return $choices;
        }

        private function parse_units( string $raw ): array {
            return array_filter( array_map( 'trim', explode( ',', $raw ) ) );
        }

        private function get_name_base( string $name, array $bp_keys ): string {
            foreach ( $bp_keys as $key ) {
                $name = str_replace( '[' . $key . ']', '', $name );
            }
            return $name;
        }
    }

    new acf_bs_breakpoints();
} );
