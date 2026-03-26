<?php


$uploader = acf_get_setting( 'uploader' );

			// Enqueue uploader scripts
			if ( $uploader === 'wp' ) {
				acf_enqueue_uploader();
			}

			// Elements and attributes.
			$image_value     = '';
			if(!empty($val)){
				$image_value = $val;
			}
			$div_attrs = array(
				'class'             => 'acf-image-uploader',
				'data-preview_size' => "thumbnail",//$field['preview_size'],
				'data-library'      => "all",//$field['library'],
				'data-mime_types'   => "",//$field['mime_types'],
				'data-uploader'     => $uploader,
			);
			$img_attrs = array(
				'src'       => '',
				'alt'       => '',
				'data-name' => 'image',
			);

			// Detect value.
			if ( $image_value && is_numeric( $image_value) ) {
				$image = wp_get_attachment_image_src( $image_value, "thumbnail");//$field['preview_size'] );
				if ( $image ) {
					$image_value         = $image_value;
					$img_attrs['src']    = $image[0];
					$img_attrs['alt']    = get_post_meta( $image_value, '_wp_attachment_image_alt', true );
					$div_attrs['class'] .= ' has-value';
				}
			}

			// Add "preview size" max width and height style.
			// Apply max-width to wrap, and max-height to img for max compatibility with field widths.
			$size               = acf_get_image_size( "thumbnail" );//$field['preview_size'] );
			$size_w             = $size['width'] ? $size['width'] . 'px' : '100%';
			$size_h             = $size['height'] ? $size['height'] . 'px' : '100%';
			$img_attrs['style'] = sprintf( 'max-height: %s;', $size_h );

			// Render HTML.
			?>
<div <?php echo acf_esc_attrs( $div_attrs ); ?>>
			<?php
			acf_hidden_input(
				array(
					'name'  => $name,
					'value' => $image_value,
				)
			);
			?>
	<div class="show-if-value image-wrap" style="max-width: <?php echo esc_attr( $size_w ); ?>">
		<img <?php echo acf_esc_attrs( $img_attrs ); ?> />
		<div class="acf-actions -hover">
			<?php if ( $uploader !== 'basic' ) : ?>
			<a class="acf-icon -pencil dark" data-name="edit" href="#" title="<?php esc_attr_e( 'Edit', 'acf' ); ?>"></a>
			<?php endif; ?>
			<a class="acf-icon -cancel dark" data-name="remove" href="#" title="<?php esc_attr_e( 'Remove', 'acf' ); ?>"></a>
		</div>
	</div>
	<div class="hide-if-value">
		<?php if ( $uploader === 'basic' ) : ?>

			<?php if ( $image_value && ! is_numeric( $image_value ) ) : ?>
				<div class="acf-error-message"><p><?php echo acf_esc_html( $image_value ); ?></p></div>
			<?php endif; ?>

			<label class="acf-basic-uploader">
				<?php
				acf_file_input(
					array(
						'name' => $name,
						'id'   => $name,//$field['id'],
						'key'  => $field['key'],
					)
				);
				?>
			</label>

		<?php else : ?>

			<p><?php esc_html_e( 'No image selected', 'acf' ); ?> <a data-name="add" class="acf-button button" href="#"><?php esc_html_e( 'Add Image', 'acf' ); ?></a></p>

		<?php endif; ?>
	</div>
</div>