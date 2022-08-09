<?php defined( 'ABSPATH' ) || exit(); ?>
<div class="row field-group">
    <div class="col-md-3">
        <label for="<?php echo esc_attr( $this->id ); ?>"><?php echo $this->label; ?></label>
    </div>
    <div class="col-md-9">
        <input id="<?php echo esc_attr( $this->id ); ?>" type="<?php echo esc_attr( $this->type ); ?>" name="<?php echo esc_attr( $this->name ); ?>" value="<?php echo esc_attr( $this->value ); ?>" />
    </div>
</div>