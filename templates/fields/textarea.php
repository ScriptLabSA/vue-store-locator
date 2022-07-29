<?php
defined('ABSPATH') || exit();
?>
<div class="row field-group">
    <div class="col-md-3">
        <label for="<?php echo $this->id; ?>"><?php echo $this->label; ?></label>
    </div>
    <div class="col-md-9">
        <textarea id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>" ></textarea>
    </div>
</div>