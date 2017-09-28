<div class="wrap">
    <h1><?php esc_html_e( 'Ukulele Membership Settings', 'ukulele' ) ?></h1>
    <form method="post" action="options.php">
    <?php
        settings_fields( 'ukulele_settings_group' );
        do_settings_sections( 'ukulele-membership-settings' );
        submit_button();
    ?>
    </form>

    <strong><p><?php esc_html_e( 'NOTE: Membership levels can be customized as prefered by an Administrator. The default WordPress user membership level will be 0. The membership level 1 will be the only level that will be free although being a registered user with privileges on WordPress. By default, levels greater than 1 will be assigned as level 1 when their membership package is downgraded due to payment failed or by manual action of Administrator. The membership levels > 1 can always upgrade to higher levels. Level 0 can\'t upgrade membership, but needs to purchase a membership package.' ) ?></p></strong>
</div>