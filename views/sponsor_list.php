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

<div class="wrap sponsor-flip-table">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br /></div>

	<h2>
<?php	echo $title ?>
		<a class="button add-new-h2" href="?page=wp-swf-add">
<?php		_e('Add') ?>
		</a>
	</h2>

<?php if ( isset($message) ): ?>
	<div id="message" class="updated below-h2">
		<p>
<?php		echo $message ?>
		</p>
	</div>
<?php endif; ?>

	<ul class="subsubsub">
		<li>
			<a href="?page=wp-swf-list">
<?php			_e('All') ?>
			</a>
		</li>
		<li>
			<a href="?page=wp-swf-list&state=active">
<?php			_e('Actives') ?>
			</a>
		</li>
		<li>
			<a href="?page=wp-swf-list&state=inactive">
<?php			_e('Inactives') ?>
			</a>
		</li>
	</ul>

	<table class="wp-list-table widefat fixed posts" cellspacing="0">
		<thead>
			<tr>
				<th>
<?php				_e('Sponsor', 'wp-sfw-plugin') ?>
				</th>
				<th>
<?php				_e('Description') ?>
				</th>
				<th>
<?php				_e('Status') ?>
				</th>
				<th>
<?php				_e('Actions') ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>
<?php				_e('Sponsor', 'wp-sfw-plugin') ?>
				</th>
				<th>
<?php				_e('Description') ?>
				</th>
				<th>
<?php				_e('Status') ?>
				</th>
				<th>
<?php				_e('Actions') ?>
				</th>
			</tr>
		</tfoot>
		<tbody>
<?php		foreach ( $sponsors as $sponsor ): ?>
			<tr>
				<td><?php echo $sponsor->getName() ?></td>
				<td><?php echo $sponsor->getDescription() ?></td>
				<td><?php echo $sponsor->getStatus() ?></td>
				<td>
					<a href="admin.php?page=wp-swf-add&sponsor=<?php echo $sponsor->getId() ?>">
<?php					_e('Edit') ?>
					</a>
					<a href="admin.php?page=wp-swf-list&remove=<?php echo $sponsor->getId() ?>">
<?php					_e('Remove') ?>
					</a>
				</td>
			</tr>
<?php		endforeach; ?>
		</tbody>
	</table>
</div>
