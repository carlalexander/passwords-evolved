<div class="wrap" id="passwords-evolved-admin">
    <div id="icon-tools" class="icon32"><br></div>
    <h2><?php echo $this->get_page_title(); ?></h2>
    <?php if (!empty($_GET['updated'])) : ?>
        <div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">
            <p><strong><?php echo $this->translate('settings_saved'); ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo $this->translate('dismiss_notice'); ?></span></button>
        </div>
    <?php endif; ?>
    <form action="<?php echo $this->get_form_url(); ?>" method="POST">
        <?php settings_fields($this->get_slug()); ?>
        <?php do_settings_sections($this->get_slug()); ?>
        <?php submit_button($this->translate('save_button')); ?>
    </form>
</div>
