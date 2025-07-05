<?php

add_action( 'admin_menu', function () {
	add_options_page(
		'Boxy opisowe kategorii',
		'Boxy opisowe kategorii',
		'manage_options',
		'category-text-fields',
		function () {
			?>
			<div class="wrap">
				<h1>Boxy opisowe kategorii</h1>
				<form method="post" action="options.php" enctype="multipart/form-data">
					<?php
					settings_fields( 'category-text-fields-group' );
					do_settings_sections( 'category-text-fields' );
					submit_button();
					?>
				</form>
			</div>
			<?php
		}
	);
} );


add_action( 'admin_init', function () {
	register_setting( 'category-text-fields-group', 'category_main_title_field' );
	register_setting( 'category-text-fields-group', 'category_main_desc_field' );

	register_setting( 'category-text-fields-group', 'category_title_field_1' );
	register_setting( 'category-text-fields-group', 'category_link_field_1' );
	register_setting( 'category-text-fields-group', 'category_desc_field_1' );
	register_setting( 'category-text-fields-group', 'category_image_field_1' );

	register_setting( 'category-text-fields-group', 'category_title_field_2' );
	register_setting( 'category-text-fields-group', 'category_link_field_2' );
	register_setting( 'category-text-fields-group', 'category_desc_field_2' );
	register_setting( 'category-text-fields-group', 'category_image_field_2' );

	register_setting( 'category-text-fields-group', 'category_title_field_3' );
	register_setting( 'category-text-fields-group', 'category_link_field_3' );
	register_setting( 'category-text-fields-group', 'category_desc_field_3' );
	register_setting( 'category-text-fields-group', 'category_image_field_3' );

	add_settings_section(
		'category-text-fields-section',
		'Wstęp',
		null,
		'category-text-fields'
	);

	add_settings_field(
		'category_main_title_field',
		'Tytuł',
		function () {
			echo '<input type="text" name="category_main_title_field" value="' . esc_attr( get_option( 'category_main_title_field' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section'
	);

	add_settings_field(
		'category_main_desc_field',
		'Opis',
		function () {
			$setting = esc_textarea( get_option( 'category_main_desc_field' ) );
			echo "<textarea name='category_main_desc_field' rows='5' cols='50'>{$setting}</textarea>";
		},
		'category-text-fields',
		'category-text-fields-section'
	);

	add_settings_section(
		'category-text-fields-section-1',
		'Box #1',
		null,
		'category-text-fields'
	);

	add_settings_field(
		'category_title_field_1',
		'Tytuł #1',
		function () {
			echo '<input type="text" name="category_title_field_1" value="' . esc_attr( get_option( 'category_title_field_1' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section-1'
	);
	add_settings_field(
		'category_link_field_1',
		'Link #1',
		function () {
			echo '<input type="url" name="category_link_field_1" value="' . esc_attr( get_option( 'category_link_field_1' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section-1'
	);
	add_settings_field(
		'category_desc_field_1',
		'Opis #1',
		function () {
			$setting = esc_textarea( get_option( 'category_desc_field_1' ) );
			echo "<textarea name='category_desc_field_1' rows='5' cols='50'>{$setting}</textarea>";
		},
		'category-text-fields',
		'category-text-fields-section-1'
	);
	add_settings_field(
		'category_image_field_1',
		'Adres URL ikony #1',
		function () {
			$setting = esc_attr( get_option( 'category_image_field_1' ) );
			echo '<input type="text" name="category_image_field_1" value="' . $setting . '">';
		},
		'category-text-fields',
		'category-text-fields-section-1'
	);

	add_settings_section(
		'category-text-fields-section-2',
		'Box #2',
		null,
		'category-text-fields'
	);

	add_settings_field(
		'category_title_field_2',
		'Tytuł #2',
		function () {
			echo '<input type="text" name="category_title_field_2" value="' . esc_attr( get_option( 'category_title_field_2' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section-2'
	);
	add_settings_field(
		'category_link_field_2',
		'Link #2',
		function () {
			echo '<input type="url" name="category_link_field_2" value="' . esc_attr( get_option( 'category_link_field_2' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section-2'
	);
	add_settings_field(
		'category_desc_field_2',
		'Opis #2',
		function () {
			$setting = esc_textarea( get_option( 'category_desc_field_2' ) );
			echo "<textarea name='category_desc_field_2' rows='5' cols='50'>{$setting}</textarea>";
		},
		'category-text-fields',
		'category-text-fields-section-2'
	);
	add_settings_field(
		'category_image_field_2',
		'Adres URL ikony #2',
		function () {
			$setting = esc_attr( get_option( 'category_image_field_2' ) );
			echo '<input type="text" name="category_image_field_2" value="' . $setting . '">';
		},
		'category-text-fields',
		'category-text-fields-section-2'
	);

	add_settings_section(
		'category-text-fields-section-3',
		'Box #3',
		null,
		'category-text-fields'
	);

	add_settings_field(
		'category_title_field_3',
		'Tytuł #3',
		function () {
			echo '<input type="text" name="category_title_field_3" value="' . esc_attr( get_option( 'category_title_field_3' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section-3'
	);
	add_settings_field(
		'category_link_field_3',
		'Link #3',
		function () {
			echo '<input type="url" name="category_link_field_3" value="' . esc_attr( get_option( 'category_link_field_3' ) ) . '">';
		},
		'category-text-fields',
		'category-text-fields-section-3'
	);
	add_settings_field(
		'category_desc_field_3',
		'Opis #3',
		function () {
			$setting = esc_textarea( get_option( 'category_desc_field_3' ) );
			echo "<textarea name='category_desc_field_3' rows='5' cols='50'>{$setting}</textarea>";
		},
		'category-text-fields',
		'category-text-fields-section-3'
	);
	add_settings_field(
		'category_image_field_3',
		'Adres URL ikony #3',
		function () {
			$setting = esc_attr( get_option( 'category_image_field_3' ) );
			echo '<input type="text" name="category_image_field_3" value="' . $setting . '">';
		},
		'category-text-fields',
		'category-text-fields-section-3'
	);
} );