<?php
defined('ABSPATH') || exit();
?>
<div class="field-group">
    <div class="col-md-4">
        <label for="<?php echo $this->id; ?>"><?php echo $this->label; ?></label>
    </div>
    <div class="col-md-8">
        <textarea id="<?php echo $this->id; ?>" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>" ></textarea>
    </div>
</div>