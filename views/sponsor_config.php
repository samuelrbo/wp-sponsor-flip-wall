<?php
/**
 * Sponsor Options
 *
 * @license GPLv3
 * @version 0.1 16th 12:40
 * @author Samuel Ramon samuelrbo@gmail.com
 */
?>

<div class="wrap">
	<h2>WP Sponsor Flip Wall - <?php _e('Configurations', 'wp-sfw-plugin') ?></h2>

	<form id="sfw_config" method="post" action="options.php">
<?php	settings_fields('wp_sfw_config') ?>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
<?php				_e('Plugin image directory', 'wp-sfw-plugin') ?>
				</th>
				<td>
					<input type="text" name="wp_sfw_img_folder" size="50" value="<?php echo get_option('wp_sfw_img_folder', WP_SFW_IMG_DIR) ?>" onfocus="javascript: this.select();" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
<?php				_e('Remove database tables when disable the plugin', 'wp-sfw-plugin') ?>
				</th>
				<td>
					<label for="yes">
						<input type="radio" value="1" id="yes" name="wp_sfw_remove_tables" <?php if ( get_option('wp_sfw_remove_tables','1') == '1' ) echo "checked='checked'" ?> />
<?php					_e('Yes') ?>
					</label>
					<label for="no">
						<input type="radio" value="0" id="no" name="wp_sfw_remove_tables" <?php if ( get_option('wp_sfw_remove_tables','1') == '0' ) echo "checked='checked'" ?> />
<?php					_e('No') ?>
					</label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
<?php				_e('Remove plugin folder and files when disable the plugin', 'wp-sfw-plugin') ?>
				</th>
				<td>
					<label for="yes_folder">
						<input type="radio" value="1" id="yes_folder" name="wp_sfw_remove_folders" <?php if ( get_option('wp_sfw_remove_folders','1') == "1" ) echo "checked='checked'" ?> />
<?php					_e('Yes') ?>
					</label>
					<label for="no_folder">
						<input type="radio" value="0" id="no_folder" name="wp_sfw_remove_folders" <?php if ( get_option('wp_sfw_remove_folders','1') == "0" ) echo "checked='checked'" ?> />
<?php					_e('No') ?>
					</label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
<?php				_e('Image width and height to validate', 'wp-sfw-plugin') ?>
				</th>
				<td>
					<label for="img_width">
<?php					_e('Width') ?>:
						<input type="text" value="<?php echo get_option('wp_sfw_img_width', '140') ?>" id="img_width" name="wp_sfw_img_width" onfocus="javascript: this.select();" /> px
					</label>
					<br />
					<label for="img_height">
<?php					_e('Height') ?>:&nbsp;&nbsp;&nbsp;
						<input type="text" value="<?php echo get_option('wp_sfw_img_height', '140') ?>" id="img_height" name="wp_sfw_img_height" onfocus="javascript: this.select();" /> px
					</label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
<?php				_e("Auto crop sponsor's images", 'wp-sfw-plugin') ?>
				</th>
				<td>
					<label for="yes_crop">
						<input type="radio" value="1" id="yes_crop" name="wp_sfw_auto_crop" <?php if ( get_option('wp_sfw_auto_crop', '0') == '1' ) echo "checked='checked'" ?> />
<?php					_e('Yes') ?>
					</label>
					<label for="no_crop">
						<input type="radio" value="0" id="no_crop" name="wp_sfw_auto_crop" <?php if ( get_option('wp_sfw_auto_crop', '0') == '0' ) echo "checked='checked'" ?> />
<?php					_e('No') ?>
					</label>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
<?php				_e('Image crop width and height if auto crop images is set', 'wp-sfw-plugin') ?>
				</th>
				<td>
					<label for="crop_width">
<?php					_e('Width') ?>:
						<input type="text" value="<?php echo get_option('wp_sfw_crop_width', '140') ?>" id="crop_width" name="wp_sfw_crop_width" onfocus="javascript: this.select();" /> px
					</label>
					<br />
					<label for="crop_height">
<?php					_e('Height') ?>:&nbsp;&nbsp;&nbsp;
						<input type="text" value="<?php echo get_option('wp_sfw_crop_height', '140') ?>" id="crop_height" name="wp_sfw_crop_height" onfocus="javascript: this.select();" /> px
					</label>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	</form>
</div>

