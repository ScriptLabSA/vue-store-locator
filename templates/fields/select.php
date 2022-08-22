<?php defined( 'ABSPATH' ) || exit(); ?>
<div class="row field-group">
    <div class="col-md-3">
        <label for="<?php echo esc_attr( $this->id ); ?>"><?php echo $this->label; ?></label>
    </div>
    <div class="col-md-9">
        <select <?php echo $this->isMultiple() ? 'multiple' : ''; ?> id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->name ); ?>"><?php 
        foreach( $this->options as $option ) : ?>
            <option value="<?php echo esc_attr( $option ); ?>" <?php selected( $option, $this->value, true ); ?>><?php echo $option; ?></option>
        <?php endforeach; ?>
        ?></select>
        <?php if( ! empty( $this->desc ) ): ?>
            <small class="text-muted"><?php echo $this->desc; ?></small>
        <?php endif; ?>
    </div>
</div>