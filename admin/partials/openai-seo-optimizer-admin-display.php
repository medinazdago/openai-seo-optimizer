<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://yoviajocr.com/about
 * @since      1.0.0
 *
 * @package    Openai_Seo_Optimizer
 * @subpackage Openai_Seo_Optimizer/admin/partials
 */
?>

<div class="wrap">
    <h1>OpenAI SEO Optimizer Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('openai_seo_optimizer_settings');
        do_settings_sections('openai-seo-optimizer');
        submit_button();
        ?>
    </form>
</div>