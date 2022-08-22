<?php defined( 'ABSPATH' ) || exit(); ?>
<div class="row field-group">
    <div class="col-md-3">
        <label for="<?php echo esc_attr( $this->id ); ?>"><?php echo $this->label; ?></label>
    </div>
    <div class="col-md-9">
        <textarea id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->name ); ?>"><?php echo esc_textarea( $this->value ); ?></textarea>
        <?php if( ! empty( $this->desc ) ): ?>
            <small class="text-muted"><?php echo $this->desc; ?></small>
        <?php endif; ?>
    </div>
</div>