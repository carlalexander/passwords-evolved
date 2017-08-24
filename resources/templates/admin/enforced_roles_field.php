<?php
$enforced_roles = $this->options->get('enforced_roles', array('administrator'));
?>
<fieldset>
    <?php foreach (get_editable_roles() as $role => $details) : ?>
        <label>
            <input type="checkbox" name="<?php esc_attr_e($this->options->get_option_name('enforced_roles')) ?>[]" value="<?php esc_attr_e($role) ?>" <?php checked(in_array($role, $enforced_roles)); ?> />
            <?php echo translate_user_role($details['name']); ?>
        </label><br />
    <?php endforeach; ?>
</fieldset>