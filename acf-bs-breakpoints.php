<?php
/**
 * Plugin: ACF BS Breakpoints (PHP 8.2 safe)
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'acf_bs_breakpoints' ) ) :

class acf_bs_breakpoints extends \acf_field {

	/* ——— Parent’la uyum için tip YOK ——— */
	public $show_in_rest = true;
	public $defaults     = array( 'font_size' => 14 );
	public $l10n         = array();
	public $preview_image = '';

	/* ——— Bizimkiler ——— */
	public $env          = array();
	public $type         = '';
	public $breakpoints  = array();
	public $types        = array();

	public function __construct() {
		// Temel bilgiler
		$this->name  = 'acf_bs_breakpoints';
		$this->label = __( 'Breakpoints', 'acf_bs_breakpoints' );
		$this->category     = 'layout';
		$this->description  = __( 'FIELD_DESCRIPTION', 'acf_bs_breakpoints' );
		$this->doc_url      = 'FIELD_DOC_URL';
		$this->tutorial_url = 'FIELD_TUTORIAL_URL';

		// Breakpoints
		$this->breakpoints = array(
			"xxxl" => "(>1600px)<br>Desktop PC, TV",
			"xxl"  => "(<=1599px)<br>Desktop PC",
			"xl"   => "(<=1399px)<br>Large Laptop",
			"lg"   => "(<=1199px)<br>Laptop",
			"md"   => "(<=991px)<br>Large Tablet",
			"sm"   => "(<=767px)<br>Tablet, Phone",
			"xs"   => "(<575px)<br>Phone",
		);

		//$this->breakpoints = array_reverse($this->breakpoints);

		// Alt tipler
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
			'version' => '1.0',
		);

		$this->preview_image = trailingslashit( $this->env['url'] ) . 'assets/images/field-preview-custom.png';

		// Hooklar
		add_filter( 'acf/update_value/type='.$this->name, array( $this, 'acf_bs_breakpoints_update_value' ), 10, 3 );
		add_filter( 'acf/format_value/type='.$this->name, array( $this, 'acf_bs_breakpoints_format_value' ), 10, 3 );

		parent::__construct();
	}

	/** Field group ayar ekranındaki ek ayarlar */
	/*public function render_field_settings( $field ) {

		if ( is_admin() ) {
			acf_render_field_setting( $field, array(
				'label'   => __( 'Field Type','acf_bs_breakpoints' ),
				'type'    => 'select',
				'name'    => 'acf_bs_breakpoints_type',
				'choices' => $this->types,
				'layout'  => 'horizontal',
				'wrapper' => array( 'class' => 'bs-breakpoints-type-setting' ),
			) );

			acf_render_field_setting( $field, array(
				'label'   => __( 'Responsive', 'acf_bs_breakpoints' ),
				'type'    => 'true_false',
				'name'    => 'show_description',
				'layout'  => 'horizontal',
				'wrapper' => array( 'class' => 'show-description-setting' ),
			) );

			acf_render_field_setting( $field, array(
				'label'   => __( 'Default Value', 'acf_bs_breakpoints' ),
				'type'    => 'text',
				'name'    => 'default_value',
				'layout'  => 'horizontal',
				'wrapper' => array( 'class' => 'default_value-setting' ),
			) );

			acf_render_field_setting( $field, array(
				'label'   => __( 'Choices','acf_bs_breakpoints' ),
				'type'    => 'textarea',
				'name'    => 'acf_bs_breakpoints_choices',
				'layout'  => 'horizontal',
				'wrapper' => array( 'class' => 'bs-breakpoints-choices-setting' ),
			) );

			foreach ( $this->breakpoints as $key => $bp ) {
				acf_render_field_setting( $field, array(
					'label'   => $key,
					'type'    => 'text',
					'name'    => $key,
					'append'  => '',
					'wrapper' => array(
						'width' => '14.2',
						'class' => 'bs-breakpoints-choices-defaults pe-0',
						'id'    => ''
					),
				) );
			}

		} else {
			// Front için alternatif görünüm istersen
			foreach ( $this->breakpoints as $key => $bp ) {
				acf_render_field_setting( $field, array(
					'label'        => $key,
					'instructions' => $bp,
					'type'         => 'select',
					'name'         => $key,
					'append'       => 'px',
					'wrapper'      => array(
						'width' => '14.2',
						'class' => '',
						'id'    => ''
					),
				) );
			}
		}
	}*/

	/** Edit ekranındaki inputları bas */
	public function render_field_settings( $field ) {

        // 1. Field Type (Eski koddaki layout ve wrapper ile birlikte)
        acf_render_field_setting( $field, array(
            'label'   => __( 'Field Type','acf_bs_breakpoints' ),
            'type'    => 'select',
            'name'    => 'acf_bs_breakpoints_type',
            'choices' => $this->types,
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'bs-breakpoints-type-setting' ),
        ) );

        // 2. Responsive (Show Description) - Eski koddaki switch
        acf_render_field_setting( $field, array(
            'label'   => __( 'Responsive', 'acf_bs_breakpoints' ),
            'type'    => 'true_false',
            'name'    => 'show_description',
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'show-description-setting' ),
        ) );

        // 3. Global Default Value
        acf_render_field_setting( $field, array(
            'label'   => __( 'Default Value', 'acf_bs_breakpoints' ),
            'type'    => 'text',
            'name'    => 'default_value',
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'default_value-setting' ),
        ) );

        // 4. Choices (Select tipi için)
        acf_render_field_setting( $field, array(
            'label'   => __( 'Choices','acf_bs_breakpoints' ),
            'type'    => 'textarea',
            'name'    => 'acf_bs_breakpoints_choices',
            'layout'  => 'horizontal',
            'wrapper' => array( 'class' => 'bs-breakpoints-choices-setting' ),
        ) );

        // --- YENİ TABLO YAPISI (Görünürlük ve Custom Label) ---
        ?>
        <div class="acf-field">
            <div class="acf-label">
                <label>Breakpoint Görünürlük & Özel Başlıklar</label>
                <p class="description">Aktif olmayanlar editörden gizlenir. Özel etiket girilmezse key (md, lg vb.) kullanılır.</p>
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
                            <td>
                                <?php acf_render_field_setting( $field, array(
                                    'type'    => 'true_false',
                                    'name'    => $e_key,
                                    'ui'      => 1,
                                    'value'   => $is_e,
                                    'no_label'=> 1,
                                ), true ); ?>
                            </td>
                            <td><code><?php echo $key; ?></code></td>
                            <td>
                                <?php acf_render_field_setting( $field, array(
                                    'type'    => 'text',
                                    'name'    => $l_key,
                                    'placeholder' => 'Optional...',
                                    'no_label'=> 1,
                                ), true ); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php

        // 5. BREAKPOINT BAZLI DEFAULT TEXTFIELDLAR (Eski kodundaki o 14.2% genişlikli alanlar)
        // Bunlar eski projelerin verilerini tuttuğu için kesinlikle silinmemeli.
        echo '<div class="acf-field"><div class="acf-label"><label>Breakpoint Varsayılan Değerleri</label></div><div class="acf-input acf-fields -border">';
        foreach ( $this->breakpoints as $key => $bp ) {
            acf_render_field_setting( $field, array(
                'label'   => $key,
                'type'    => 'text',
                'name'    => $key,
                'wrapper' => array(
                    'width' => '14.2',
                    'class' => 'bs-breakpoints-choices-defaults pe-0',
                ),
            ) );
        }
        echo '</div></div>';
    }

	public function render_field( $field ) {
        $type = isset( $field['acf_bs_breakpoints_type'] ) ? $field['acf_bs_breakpoints_type'] : 'text';
        $this->type = $type;

        // --- ESKİ KODUNDAKİ SEÇENEKLERİ (CHOICES) PARSE ETME MANTIĞI ---
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
                        if ( $line === '' ) { continue; }
                        if ( strpos( $line, ':' ) !== false ) {
                            $parts = explode( ':', $line, 2 );
                            $val = trim( $parts[0] );
                            $lab = trim( $parts[1] );
                            $choices[ $val ] = $lab;
                        } else {
                            $choices[ $k ] = $line;
                        }
                    }
                }
            }
        }

        $name_root = $field['name'];
        $value     = isset( $field['value'] ) ? $field['value'] : array();

        // Breakpoint keylerini name_root içinden temizle
        foreach ( $this->breakpoints as $bpKey => $_ ) {
            $name_root = str_replace( '['.$bpKey.']', '', $name_root );
        }

        $description = ! empty( $field['show_description'] );
        ?>

        <div class="acf-fields acf-bs-breakpoints-fields d-flex">
            <?php foreach ( $this->breakpoints as $bpKey => $bpLabel ) :
                
                // --- YENİ FİLTRE: Enabled/Disabled Kontrolü ---
                // Ayar yoksa (eski projeyse) default 1 gelsin
                $enabled = isset($field["bp_enabled_{$bpKey}"]) ? $field["bp_enabled_{$bpKey}"] : 1;
                if ( !$enabled ) continue;

                // --- YENİ ÖZELLİK: Custom Label ---
                $display_label = !empty($field["bp_label_{$bpKey}"]) ? $field["bp_label_{$bpKey}"] : $bpKey;

                $name = $name_root . '[' . $bpKey . ']';
                $val  = isset( $value[ $bpKey ] ) ? $value[ $bpKey ] : ( isset( $field[ $bpKey ] ) ? $field[ $bpKey ] : '' );

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
                                        <?php checked( (string) $val, '1' ); ?>>
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
                                    <option value="<?php echo esc_attr( $ov ); ?>" <?php selected( (string) $val, (string) $ov ); ?>>
                                        <?php echo esc_html( $ol ); ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>

                        <?php elseif ( $type === 'color_picker' ) : ?>

                            <div class="acf-color-picker">
                                <input type="text"
                                    id="<?php echo esc_attr( $name ); ?>"
                                    name="<?php echo esc_attr( $name ); ?>"
                                    value="<?php echo esc_attr( $val ); ?>"
                                    data-alpha-skip-debounce="1"/>
                            </div>

                        <?php elseif ( $type === 'image' ) : ?>

                            <?php include __DIR__ . "/fields/image.php"; ?>

                        <?php elseif ( $type === 'units' ) : ?>

                            <?php
                            $unit_field = array(
                                'key'               => $name,
                                'label'             => '',
                                'name'              => $name,
                                'value'             => $val,
                                'type'              => 'units',
                                'instructions'      => 'Select units',
                                'required'          => 0,
                                'conditional_logic' => 0,
                                'wrapper'           => array( 'width' => '100%' ),
                            );
                            acf_render_field( $unit_field );
                            ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

	/** Kayıt öncesi */
	public function acf_bs_breakpoints_update_value( $value, $post_id, $field ) {

		if ( isset( $field['acf_bs_breakpoints_type'] ) && $field['acf_bs_breakpoints_type'] === 'image' && ! empty( $value ) ) {
			$ids = array_values( (array) $value );
			$ids = array_filter( $ids, static function( $v ) {
				return $v !== null && $v !== '' && ( ! is_array( $v ) || ! empty( $v ) );
			} );

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

	/** get_field sonrası formatlama */
    public function acf_bs_breakpoints_format_value( $value, $post_id, $field ) {
        // Eğer tip image ise ve değer boş değilse get_base_data'ya gönder
        if ( isset( $field['acf_bs_breakpoints_type'] ) && $field['acf_bs_breakpoints_type'] === 'image' && ! empty( $value ) ) {
            $value = $this->get_base_data( $value );
        }
        return $value;
    }

    /** id + url doldur (Senin Orijinal Fonksiyonun) */
    public function get_base_data( $value = 0 ) {
        if ( $value && is_array( $value ) ) {
            $ids = array_values( $value );
            // Boş olanları (null, empty string vb.) temizle
            $ids = array_filter( $ids, static function( $v ) {
                return $v !== null && $v !== '' && ( ! is_array( $v ) || ! empty( $v ) );
            } );

            if ( $ids ) {
                $ids          = array_values( $ids );
                $attachmentID = (int) $ids[0]; // İlk bulduğun dolu ID'yi al
                
                // Burası kritik: Orijinal array'in içine id ve url keylerini enjekte ediyor
                $value['id']  = $attachmentID;
                $value['url'] = wp_get_attachment_url( $attachmentID );
            }
        }
        return $value;
    }

	/** Admin enqueue */
	public function input_admin_enqueue_scripts() {
		$url     = trailingslashit( $this->env['url'] );
		$version = $this->env['version'];

		wp_register_script(
			'acf_bs_breakpoints',
			"{$url}assets/js/field.js",
			array( 'acf-input' ),
			$version,
			true
		);

		wp_register_style(
			'acf_bs_breakpoints',
			"{$url}assets/css/field.css",
			array( 'acf-input' ),
			$version
		);

		wp_enqueue_script( 'acf_bs_breakpoints' );
		wp_enqueue_style( 'acf_bs_breakpoints' );

		wp_enqueue_script(
			'bs_breakpoints_admin',
			"{$url}assets/js/admin.js",
			array( 'jquery' ),
			$version,
			true
		);

		wp_localize_script(
			'bs_breakpoints_admin',
			'bs_breakpoints_vars',
			array(
				'type_selector'   => '.bs-breakpoints-type-setting select',
				'choices_wrapper' => '.bs-breakpoints-choices-setting',
			)
		);

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );
	}

	/** Modern ve güvenli: metadata’yı baştan üret */
	private function regenerate_attachment_metadata( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		$file = get_attached_file( $attachment_id );
		if ( ! $file || ! file_exists( $file ) ) { return; }
		$metadata = wp_generate_attachment_metadata( $attachment_id, $file );
		if ( ! is_wp_error( $metadata ) && ! empty( $metadata ) ) {
			wp_update_attachment_metadata( $attachment_id, $metadata );
		}
	}

	/** Ek boyutları sil (thumbnail hariç) */
	public function remove_image_sizes( $attachment_id ) {
		$attachment_id = (int) $attachment_id;

		$metadata = wp_get_attachment_metadata( $attachment_id );
		if ( ! is_array( $metadata ) || empty( $metadata['sizes'] ) ) {
			return;
		}

		$base_file = get_attached_file( $attachment_id );
		if ( ! $base_file ) { return; }
		$base_dir = wp_normalize_path( dirname( $base_file ) );

		foreach ( $metadata['sizes'] as $size => $info ) {
			if ( $size === 'thumbnail' ) { continue; }

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

// Kayıt et
new acf_bs_breakpoints();

endif;
