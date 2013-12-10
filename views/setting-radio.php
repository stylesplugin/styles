<?php $i = 0; ?>
<?php foreach ( $choices as $label => $value ) : ?>

	<input 
		class="radio <?php echo $class ?>" 
		type="radio"
		name="<?php echo $option_name ?>"
		id="<?php echo $id ?>-<?php esc_attr_e( $value ) ?>"
		value="<?php esc_attr_e( $value ) ?>"
		<?php checked( $option_value, $value ) ?>
	/>
	<label for="<?php echo $id ?>-<?php esc_attr_e( $value ) ?>"><?php echo $label ?></label>

	<?php if ( $i < count( $choices ) - 1 ) : $i++ ?>
		<br />
	<?php endif; ?>

<?php endforeach; ?>

<?php if ( $description ): ?>
	<br /><span class="description"><?php echo $description ?></span>
<?php endif; ?>