<select class="select <?php echo $class ?>" id="<?php echo $id ?>" name="<?php echo $option_name ?>" >
	
	<?php foreach( $choices as $label => $value ) : ?>
		<option value="<?php esc_attr_e( $value ) ?>" <?php selected( $option_value, $value ) ?> >
			<?php echo $label ?>
		</option>
	<?php endforeach; ?>

</select>

<?php if ( $description ): ?>
	<br /><span class="description"><?php echo $description ?></span>
<?php endif; ?>