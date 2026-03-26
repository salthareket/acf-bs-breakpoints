<?php
/**
 * Plugin Name: ACF Bootstrap Breakpoints
 * Description: Bootstrap breakpoint'leri için özel ACF alan tipi sağlar. (PHP 8.2+ Uyumlu)
 * Version: 1.1.0
 * Author: Tolga Koçak
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Field register işlemini ana dosya içinde başlatıyoruz.
 */
add_action('acf/include_field_types', function() {
    if ( ! class_exists( 'acf_bs_breakpoints' ) ) :

    class acf_bs_breakpoints extends \acf_field {

        public $show_in_rest = true;
        public $defaults     = array( 'font_size' => 14 );
        public $l10n         = array();
        public $preview_image = '';
        public $env          = array();
        public $breakpoints  = array();
        public $types        = array();

        public function __construct() {
            $this->name  = 'acf_bs_breakpoints';
            $this->label = __( 'Breakpoints', 'acf_bs_breakpoints' );
            $this->category     = 'layout';
            $this->description  = __( 'Responsive Bootstrap Breakpoints for ACF', 'acf_bs_breakpoints' );

            // Breakpoint Tanımları
            $this->breakpoints = array(
                "xxxl" => "(>1600px)<br>Desktop PC, TV",
                "xxl"  => "(<=1599px)<br>Desktop PC",
                "xl"   => "(<=1399px)<br>Large Laptop",
                "lg"   => "(<=1199px)<br>Laptop",
                "md"   => "(<=991px)<br>Large Tablet",
                "sm"   => "(<=767px)<br>Tablet, Phone",
                "xs"   => "(<575px)<br>Phone",
            );

            // Alt Alan Tipleri
            $this->types = array(
                "text"         => "Text",
                "number"       => "Number",
                "units"        => "Units",
                "true_false"   => "True / false",
                "select"       => "Select",
                "color_picker" => "Color Picker",
                "image"        => "Image",
            );

            $this->env = array(
                'url'     => plugin_dir_url( __FILE__ ),
                'version' => '1.1.0',
            );

            $this->preview_image = $this->env['url'] . 'assets/images/field-preview-custom.png';

            add_filter( 'acf/update_value/type='.$this->name, array( $this, 'acf_bs_breakpoints_update_value' ), 10, 3 );
            add_filter( 'acf/format_value/type='.$this->name, array( $this, 'acf_bs_breakpoints_format_value' ), 10, 3 );

            parent::__construct();
        }

        public function render_field_settings( $field ) {
            // Field Type Seçimi
            acf_render_field_setting( $field, array(
                'label'   => __( 'Field Type','acf_bs_breakpoints' ),
                'type'    => 'select',
                'name'    => 'acf_bs_breakpoints_type',
                'choices' => $this->types,
                'layout'  => 'horizontal',
                'wrapper' => array( 'class' => 'bs-breakpoints-type-setting' ),
            ) );

            // Responsive Switch
            acf_render_field_setting( $field, array(
                'label'   => __( 'Responsive', 'acf_bs_breakpoints' ),
                'type'    => 'true_false',
                'name'    => 'show_description',
                'layout'  => 'horizontal',
                'wrapper' => array( 'class' => 'show-description-setting' ),
            ) );

            // Global Default
            acf_render_field_setting( $field, array(
                'label'   => __( 'Default Value', 'acf_bs_breakpoints' ),
                'type'    => 'text',
                'name'    => 'default_value',
                'layout'  => 'horizontal',
                'wrapper' => array( 'class' => 'default_value-setting' ),
            ) );

            // Choices (Select için)
            acf_render_field_setting( $field, array(
                'label'   => __( 'Choices','acf_bs_breakpoints' ),
                'type'    => 'textarea',
                'name'    => 'acf_bs_breakpoints_choices',
                'layout'  => 'horizontal',
                'wrapper' => array( 'class' => 'bs-breakpoints-choices-setting' ),
            ) );

            // Breakpoint Görünürlük Tablosu
            ?>
            <div class="acf-field">
                <div class="acf-label">
                    <label>Breakpoint Görünürlük & Özel Başlıklar</label>
                </div>
                <div class="acf-input">
                    <table class="acf-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">Aktif</th>
                                <th style="width: 100px;">Key</th>
                                <th>Özel Başlık (Opsiyonel)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $this->breakpoints as $key => $default_label ): 
                                $e_key = "bp_enabled_{$key}";
                                $l_key = "bp_label_{$key}";
                                $is_e = isset($field[$e_key]) ? $field[$e_key] : 1;
                            ?>
                            <tr>
                                <td><?php acf_render_field_setting( $field, array('type'=>'true_false','name'=>$e_key,'ui'=>1,'value'=>$is_e,'no_label'=>1), true ); ?></td>
                                <td><code><?php echo $key; ?></code></td>
                                <td><?php acf_render_field_setting( $field, array('type'=>'text','name'=>$l_key,'placeholder'=>'Optional...','no_label'=>1), true ); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php

            // Varsayılan Değer Girişleri
            echo '<div class="acf-field"><div class="acf-label"><label>Breakpoint Varsayılan Değerleri</label></div><div class="acf-input acf-fields -border">';
            foreach ( $this->breakpoints as $key => $bp ) {
                acf_render_field_setting( $field, array(
                    'label'   => $key,
                    'type'    => 'text',
                    'name'    => $key,
                    'wrapper' => array('width' => '14.2', 'class' => 'bs-breakpoints-choices-defaults'),
                ) );
            }
            echo '</div></div>';
        }

        public function render_field( $field ) {
            $type = isset( $field['acf_bs_breakpoints_type'] ) ? $field['acf_bs_breakpoints_type'] : 'text';
            $choices = array();

            if ( $type === 'select' ) {
                $options = isset( $field['acf_bs_breakpoints_choices'] ) ? $field['acf_bs_breakpoints_choices'] : '';
                $lines = explode( "\n", (string) $options );
                foreach ( $lines as $k => $line ) {
                    $line = trim($line);
                    if ($line === '') continue;
                    if (strpos($line, ':') !== false) {
                        list($val, $lab) = explode(':', $line, 2);
                        $choices[trim($val)] = trim($lab);
                    } else {
                        $choices[$k] = $line;
                    }
                }
            }

            $name_root = $field['name'];
            $value = isset( $field['value'] ) ? $field['value'] : array();
            foreach ( $this->breakpoints as $bpKey => $_ ) { $name_root = str_replace( '['.$bpKey.']', '', $name_root ); }

            echo '<div class="acf-fields acf-bs-breakpoints-fields d-flex" style="gap: 10px; overflow-x: auto;">';
            foreach ( $this->breakpoints as $bpKey => $bpLabel ) :
                if ( isset($field["bp_enabled_{$bpKey}"]) && !$field["bp_enabled_{$bpKey}"] ) continue;

                $display_label = !empty($field["bp_label_{$bpKey}"]) ? $field["bp_label_{$bpKey}"] : $bpKey;
                $name = $name_root . '[' . $bpKey . ']';
                $val = isset( $value[ $bpKey ] ) ? $value[ $bpKey ] : ( isset( $field[ $bpKey ] ) ? $field[ $bpKey ] : '' );
                if ( $val === '' && !empty($field['default_value']) ) { $val = $field['default_value']; }
                ?>
                <div class="acf-field" style="flex: 1; min-width: 120px; border-left: 1px solid #eee; padding-left: 10px;">
                    <div class="acf-label"><label><?php echo esc_html( $display_label ); ?></label>
                    <?php if ( !empty($field['show_description']) ) echo '<p class="description">'.$bpLabel.'</p>'; ?></div>
                    <div class="acf-input">
                        <?php if ( $type === 'text' || $type === 'number' ) : ?>
                            <input type="<?php echo $type; ?>" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($val); ?>" />
                        <?php elseif ( $type === 'true_false' ) : ?>
                            <input type="checkbox" name="<?php echo esc_attr($name); ?>" value="1" <?php checked($val, '1'); ?> />
                        <?php elseif ( $type === 'select' ) : ?>
                            <select name="<?php echo esc_attr($name); ?>">
                                <?php foreach($choices as $ov => $ol): ?>
                                    <option value="<?php echo esc_attr($ov); ?>" <?php selected($val, $ov); ?>><?php echo esc_html($ol); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif ( $type === 'color_picker' ) : ?>
                            <input type="text" class="acf-color-picker" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($val); ?>" />
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; echo '</div>';
        }

        public function input_admin_enqueue_scripts() {
            $url = $this->env['url'];
            wp_enqueue_script( 'acf_bs_breakpoints', "{$url}assets/js/field.js", array('acf-input'), $this->env['version'] );
            wp_enqueue_style( 'acf_bs_breakpoints', "{$url}assets/css/field.css", array('acf-input'), $this->env['version'] );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_style( 'wp-color-picker' );
        }

        public function acf_bs_breakpoints_update_value( $value, $post_id, $field ) {
            if ( isset($field['acf_bs_breakpoints_type']) && $field['acf_bs_breakpoints_type'] === 'image' && !empty($value) ) {
                $ids = array_filter((array)$value);
                if ($ids) {
                    $attachment_id = (int) reset($ids);
                    $file = get_attached_file($attachment_id);
                    if ($file && file_exists($file)) {
                        $metadata = wp_generate_attachment_metadata($attachment_id, $file);
                        wp_update_attachment_metadata($attachment_id, $metadata);
                    }
                }
            }
            return $value;
        }

        public function acf_bs_breakpoints_format_value( $value, $post_id, $field ) {
            if ( isset($field['acf_bs_breakpoints_type']) && $field['acf_bs_breakpoints_type'] === 'image' && !empty($value) && is_array($value) ) {
                $ids = array_filter($value);
                if ($ids) {
                    $id = (int) reset($ids);
                    $value['id'] = $id;
                    $value['url'] = wp_get_attachment_url($id);
                }
            }
            return $value;
        }
    }

    new acf_bs_breakpoints();
    endif;
});