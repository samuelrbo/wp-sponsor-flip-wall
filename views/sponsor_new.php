<?php
/**
 * Sponsor Form ( NEW/EDIT )
 *
 * @license GPLv3
 * @version 0.1 15th 19:25
 * @author Samuel Ramon samuelrbo@gmail.com
 */

global $title;
?>

<div class="wrap sponsor-flip">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>

	<h2>
<?php	echo $title ?>
	</h2>

<?php if ( !empty( $response ) ): ?>
	<div id="message" class="<?php echo ($response['success']) ? 'updated below-h2' : 'error' ?>">
<?php	echo $response['message'] ?>
	</div>
<?php endif; ?>

	<form action="" name="" id="" method="post" enctype="multipart/form-data">
		<div class="fl">
			<p>
				<label for="sponsor_name">
<?php				_e('Name') ?>:
				</label>
				<input type="text" name="name" id="sponsor_name" value="<?php echo @$sponsor->getName() ?>" class="" />
			</p>

			<p>
				<label for="sponsor_link">
<?php				_e('Link') ?>:
				</label>
				<input type="text" name="link" id="sponsor_link" value="<?php echo @$sponsor->getLink() ?>" />
			</p>

			<p>
				<label for="sponsor_desc">
<?php				_e('Description') ?>:
				</label>
				<textarea name="description" id="sponsor_desc"
						  rows="" cols=""><?php echo @$sponsor->getDescription() ?></textarea>
			</p>

			<p>
				<label for="sponsor_status">
<?php				_e('Status') ?>:
				</label>
				<select name="status" id="sponsor_status">
					<option value=""><?php _e('Select') ?></option>
					<option value="active" <?php if ( isset($sponsor) && $sponsor->getStatus() == 'active' ) echo "selected='selected'" ?>><?php _e('Active', 'wp-sfw-plugin') ?></option>
					<option value="inactive" <?php if ( isset($sponsor) && $sponsor->getStatus() == 'inactive' ) echo "selected='selected'" ?>><?php _e('Inactive', 'wp-sfw-plugin') ?></option>
				</select>
			</p>

			<p>
				<label for="sponsor_img">
<?php				_e('Image') ?> ( 140px X 140px ):
				</label>
				<input type="file" name="sponsor_img" id="sponsor_img" />
			</p>

			<p class="submit">
				<input id="submit" class="button-primary" type="submit"
					   value="<?php _e('Save') ?>" name="save">
			</p>
		</div>

<?php	if ( $sponsor && $sponsor->getImgDir() != Sponsor::$default_img ): ?>
		<div class="img" id="sponsorImg">
			<div>
				<img alt="<?php echo @$sponsor->getName() ?>"
					 title="<?php echo @$sponsor->getName() ?>"
					 src="<?php echo @$sponsor->getImgDir() ?>" />
			</div>
			<span>140px X 140px</span> <a href="#" id="removeImg"><?php _e('Remove') ?></a>
		</div>
<?php	endif; ?>
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#removeImg').click(function(e){
		e.preventDefault()

		$.post( ajaxurl, {
			action: 'remove_sponsor_image',
			sponsor_id: '<?php echo Input::get('sponsor') ?>'
		}, function(response){
			alert(response.message)
			if ( response.success )
				$('#sponsorImg').remove()

		}, 'json')
	})
})
</script>
