<?php
/**
 * ╔══════════════════════════════════════════════════════════════════════════════╗
 * ║                        CSS BİRİM REFERANSı                                ║
 * ╠══════════════════════════════════════════════════════════════════════════════╣
 * ║                                                                            ║
 * ║  ABSOLUTE (Sabit) BİRİMLER                                                ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  px     1px = 1 piksel (ekran pikseli, 96dpi'da 1/96 inch)                ║
 * ║  pt     1pt = 1.333px (1/72 inch, baskı/tipografi birimi)                 ║
 * ║  cm     1cm = 37.795px                                                     ║
 * ║  mm     1mm = 3.7795px                                                     ║
 * ║  in     1in = 96px (1 inch = 2.54cm)                                      ║
 * ║                                                                            ║
 * ║  RELATIVE (Göreceli) BİRİMLER — Font Bazlı                                ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  em     1em = parent elementin font-size'ı (parent 16px ise 1em=16px)      ║
 * ║  rem    1rem = root (<html>) font-size'ı (genelde 16px)                    ║
 * ║  ex     1ex ≈ mevcut fontun "x" harfi yüksekliği (genelde ~0.5em)         ║
 * ║  ch     1ch = mevcut fontun "0" karakteri genişliği (monospace'te ~0.5em)  ║
 * ║  cap    1cap = mevcut fontun büyük harf yüksekliği (A,B,C gibi)           ║
 * ║  ic     1ic = CJK (Çince/Japonca) "水" karakteri genişliği                ║
 * ║  lh     1lh = elementin kendi line-height değeri                           ║
 * ║  rlh    1rlh = root elementin line-height değeri                           ║
 * ║                                                                            ║
 * ║  RELATIVE (Göreceli) BİRİMLER — Yüzde                                     ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  %      parent elementin ilgili özelliğinin yüzdesi                        ║
 * ║         (width: 50% = parent genişliğinin yarısı)                          ║
 * ║                                                                            ║
 * ║  VIEWPORT BİRİMLERİ — Klasik                                              ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  vw     1vw = viewport genişliğinin %1'i (1920px ekranda 1vw=19.2px)      ║
 * ║  vh     1vh = viewport yüksekliğinin %1'i (1080px ekranda 1vh=10.8px)     ║
 * ║  vmin   1vmin = vw ve vh'den küçük olanı                                  ║
 * ║  vmax   1vmax = vw ve vh'den büyük olanı                                  ║
 * ║                                                                            ║
 * ║  VIEWPORT BİRİMLERİ — Dynamic (Mobil adres çubuğuna duyarlı)              ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  dvw    1dvw = dynamic viewport genişliğinin %1'i                          ║
 * ║  dvh    1dvh = dynamic viewport yüksekliğinin %1'i                         ║
 * ║         (mobilde adres çubuğu açılıp kapandıkça değişir, vh'nin modern     ║
 * ║          alternatifi — mobil responsive'de vh yerine dvh tercih edilmeli)   ║
 * ║                                                                            ║
 * ║  VIEWPORT BİRİMLERİ — Small (Adres çubuğu açıkken)                        ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  svw    1svw = small viewport genişliğinin %1'i                            ║
 * ║  svh    1svh = small viewport yüksekliğinin %1'i                           ║
 * ║         (adres çubuğu görünürken en küçük viewport — güvenli minimum)      ║
 * ║                                                                            ║
 * ║  VIEWPORT BİRİMLERİ — Large (Adres çubuğu gizliyken)                      ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  lvw    1lvw = large viewport genişliğinin %1'i                            ║
 * ║  lvh    1lvh = large viewport yüksekliğinin %1'i                           ║
 * ║         (adres çubuğu gizliyken en büyük viewport)                         ║
 * ║                                                                            ║
 * ║  CONTAINER QUERY BİRİMLERİ                                                ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  cqw    1cqw = en yakın container'ın genişliğinin %1'i                     ║
 * ║  cqh    1cqh = en yakın container'ın yüksekliğinin %1'i                    ║
 * ║         (container-type: inline-size veya size tanımlı parent gerektirir)  ║
 * ║                                                                            ║
 * ║  GRID BİRİMİ                                                              ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  fr     CSS Grid'de kalan boş alanın oransal payı                         ║
 * ║         (grid-template-columns: 1fr 2fr = 1/3 ve 2/3 oranında)            ║
 * ║                                                                            ║
 * ║  ÖZEL DEĞERLER                                                             ║
 * ║  ─────────────────────────────────────────────────────────────────────────  ║
 * ║  auto   tarayıcı otomatik hesaplar (context'e göre değişir)                ║
 * ║  (boş)  birimsiz sayısal değer (line-height, opacity, z-index vb.)        ║
 * ║                                                                            ║
 * ╚══════════════════════════════════════════════════════════════════════════════╝
 */

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists('acf_field_units') ) :

class acf_field_units extends acf_field {

    var $name = 'units';
    var $label = 'Units';
    var $category = 'basic';
    var $units = '';

    function __construct() {
        $this->name = 'units';
        $this->label = __('Units', 'acf');
        $this->category = 'basic';
        $this->defaults = array(
            'units' => array(
                // Absolute
                'px', 'pt', 'cm', 'mm', 'in',
                // Relative — font
                '%', 'em', 'rem', 'ex', 'ch', 'cap', 'ic', 'lh', 'rlh',
                // Viewport — classic
                'vw', 'vh', 'vmin', 'vmax',
                // Viewport — dynamic
                'dvw', 'dvh',
                // Viewport — small
                'svw', 'svh',
                // Viewport — large
                'lvw', 'lvh',
                // Container query
                'cqw', 'cqh',
                // Grid
                'fr',
                // Special
                'auto', '',
            ),
            'default_value' => array('value' => '0', 'unit' => ''),
            'allowed_units' => array(),
            'default_unit'  => '',
        );
        $this->units = $this->defaults['units'];
        parent::__construct();
        add_action('admin_head', array($this, 'acf_field_units_css'));
    }

    function acf_field_units_css() {
        echo '<style>
        .acf-field-units input[type="text"]:focus + select {
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
            outline: 2px solid transparent;
        }
        </style>';
    }

    /**
     * PUBLIC API — Diğer plugin'ler (breakpoints vb.) bu method ile tüm unit listesini çeker.
     * Units plugin'inde birim eklenip çıkarıldığında bu method otomatik güncel listeyi döndürür.
     */
    public static function get_all_units() {
        $instance = acf_get_field_type( 'units' );
        if ( $instance && ! empty( $instance->units ) ) {
            return $instance->units;
        }
        // Fallback — plugin yüklü değilse
        return array( 'px', '%', 'em', 'rem', 'vw', 'vh', 'vmin', 'vmax', 'pt', 'cm', 'mm', 'in', 'ex', 'ch', 'fr', 'auto', '' );
    }

    /**
     * Aktif birimleri döndür — allowed_units doluysa onu, yoksa tümünü
     */
    private function get_active_units( $field ) {
        $allowed = isset( $field['allowed_units'] ) ? (array) $field['allowed_units'] : array();
        $allowed = array_filter( $allowed );
        if ( empty( $allowed ) ) {
            return $this->units;
        }
        return $allowed;
    }

    function render_field( $field ) {
        $units = $this->get_active_units( $field );

        // Değer
        $value = isset($field['value']['value']) && is_array($field['value'])
            ? $field['value']['value']
            : (is_array($field['default_value']) ? $field['default_value']['value'] : '0');

        // Birim: value > default_unit setting > default_value.unit > ''
        $unit = '';
        if ( isset($field['value']['unit']) && is_array($field['value']) ) {
            $unit = $field['value']['unit'];
        } elseif ( ! empty($field['default_unit']) ) {
            $unit = $field['default_unit'];
        } elseif ( is_array($field['default_value']) && ! empty($field['default_value']['unit']) ) {
            $unit = $field['default_value']['unit'];
        }
        ?>
        <div style="display: inline-flex;">
            <input type="text" name="<?php echo esc_attr($field['name']) ?>[value]" value="<?php echo esc_attr($value) ?>" style="flex: 1 1 auto; border-right:none;border-top-right-radius: 0;border-bottom-right-radius: 0;" class="acf-units-input" />
            <select name="<?php echo esc_attr($field['name']) ?>[unit]" class="acf-units-select" style="flex: auto; border-left: none; width:auto; padding-left:15px; padding-right:25px; border-top-left-radius: 0;border-bottom-left-radius: 0;text-align:right; min-width:0;">
                <?php foreach( $units as $unit_option ): ?>
                    <option value="<?php echo esc_attr($unit_option) ?>" <?php selected($unit, $unit_option) ?>><?php echo esc_html($unit_option) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <script>
        (function($) {
            $(document).ready(function() {
                $('.acf-units-select').change(function() {
                    var $input = $(this).closest('div').find('.acf-units-input');
                    if ($(this).val() === 'auto') {
                        $input.val('auto').prop('readonly', true);
                    } else {
                        $input.prop('readonly', false);
                    }
                }).trigger('change');
            });
        })(jQuery);
        </script>
        <?php
    }

    function render_field_settings( $field ) {

        // 1. Default Value (Number)
        acf_render_field_setting( $field, array(
            'label'         => __('Default Value (Number)','acf'),
            'instructions'  => __('Enter the default numeric value for this field','acf'),
            'type'          => 'text',
            'name'          => 'default_value_value',
            'value'         => !empty($field['default_value_value']) ? $field['default_value_value'] : '0'
        ));

        // 2. Kullanılacak Birimler — select multiple (select2)
        $all_units = $this->units;
        $choices = array();
        foreach ( $all_units as $u ) {
            $choices[ $u ] = $u === '' ? '(boş)' : $u;
        }

        $current_allowed = isset( $field['allowed_units'] ) ? (array) $field['allowed_units'] : array();
        $current_allowed = array_filter( $current_allowed, function( $v ) { return $v !== null; });

        acf_render_field_setting( $field, array(
            'label'         => __('Kullanılacak Birimler','acf'),
            'instructions'  => __('Boş bırakılırsa tüm birimler kullanılır. Sürükleyerek sıralayabilirsiniz.','acf'),
            'type'          => 'select',
            'name'          => 'allowed_units',
            'choices'       => $choices,
            'value'         => $current_allowed,
            'multiple'      => 1,
            'ui'            => 1,
            'allow_null'    => 1,
            'placeholder'   => __('Birim seçin...','acf'),
            'wrapper'       => array( 'class' => 'acf-units-allowed-setting' ),
        ));

        // 3. Varsayılan Birim — select (sadece seçili birimlerden, boşsa tümü)
        $default_choices = array();
        $active_for_default = ! empty( $current_allowed ) ? $current_allowed : $all_units;
        foreach ( $active_for_default as $u ) {
            $default_choices[ $u ] = $u === '' ? '(boş)' : $u;
        }

        $current_default = isset( $field['default_unit'] ) ? $field['default_unit'] : '';

        acf_render_field_setting( $field, array(
            'label'         => __('Varsayılan Birim','acf'),
            'instructions'  => __('Field açıldığında seçili gelecek birim','acf'),
            'type'          => 'select',
            'name'          => 'default_unit',
            'choices'       => $default_choices,
            'value'         => $current_default,
            'allow_null'    => 1,
            'placeholder'   => __('Önce birim seçin...','acf'),
            'wrapper'       => array( 'class' => 'acf-units-default-unit-setting' ),
        ));

        // JS: allowed_units (select2) değişince default_unit seçeneklerini güncelle + Tümünü Seç/Temizle
        ?>
        <script>
        (function($) {
            if (window._acfUnitsSettingsBound) return;
            window._acfUnitsSettingsBound = true;

            // Tümünü Seç / Temizle link'ini inject et
            function injectSelectAllLink($wrap) {
                var $container = $wrap.find('.acf-units-allowed-setting');
                if (!$container.length || $container.find('.acf-units-select-all-wrap').length) return;

                var $select = $container.find('select[multiple]');
                if (!$select.length) return;

                var $link = $('<span class="acf-units-select-all-wrap" style="display:inline-block; margin-bottom:4px;">' +
                    '<a href="#" class="acf-units-select-all" style="font-size:12px; margin-right:8px;">Tümünü Seç</a>' +
                    '<a href="#" class="acf-units-clear-all" style="font-size:12px;">Temizle</a>' +
                    '</span>');

                $container.find('.acf-label').after($link);

                $link.on('click', '.acf-units-select-all', function(e) {
                    e.preventDefault();
                    var allVals = [];
                    $select.find('option').each(function() { allVals.push($(this).val()); });
                    $select.val(allVals).trigger('change');
                });

                $link.on('click', '.acf-units-clear-all', function(e) {
                    e.preventDefault();
                    $select.val([]).trigger('change');
                });
            }

            function syncDefaultUnit($wrap) {
                var $allowedSelect = $wrap.find('.acf-units-allowed-setting select[multiple]');
                var $defaultSelect = $wrap.find('.acf-units-default-unit-setting select');
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

            $(document).on('change', '.acf-units-allowed-setting select', function() {
                var $wrap = $(this).closest('.acf-field-object, .acf-field-settings, form');
                syncDefaultUnit($wrap);
            });

            // Link'leri inject et — sayfa yüklendiğinde ve field açıldığında
            $(document).ready(function() {
                $('.acf-units-allowed-setting').each(function() {
                    injectSelectAllLink($(this).closest('.acf-field-object, .acf-field-settings, form'));
                });
            });

            if (typeof acf !== 'undefined' && typeof acf.add_action !== 'undefined') {
                acf.add_action('open_field_object', function(field) {
                    var $el = field && field.$el ? field.$el : null;
                    if ($el) injectSelectAllLink($el);
                });
            }
        })(jQuery);
        </script>
        <?php
    }

    function update_value( $value, $post_id, $field ) {
        if (empty($value) || !is_array($value)) {
            return array(
                'value' => isset($field['default_value']['value']) ? $field['default_value']['value'] : '0',
                'unit' => isset($field['default_value']['unit']) ? $field['default_value']['unit'] : ''
            );
        }

        $val = isset($value['value']) ? $value['value'] : (isset($field['default_value']['value']) ? $field['default_value']['value'] : '0');
        $unit = isset($value['unit']) ? $value['unit'] : (isset($field['default_value']['unit']) ? $field['default_value']['unit'] : '');

        if ($unit === 'auto') {
            $val = 'auto';
        }

        return array('value' => $val, 'unit' => $unit);
    }
}

new acf_field_units();

endif;
