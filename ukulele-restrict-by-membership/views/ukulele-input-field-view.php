<label><?php esc_html_e( 'Level >=', 'ukulele' ) ?></label>
<input 
    type="text" 
    id="<?php esc_attr_e( $tag->slug ) ?>" 
    name="ukulele_settings[<?php esc_attr_e( $tag->slug ) ?>]"
    value="<?php echo $value ?>"
/>
<label class="ukulele-admin-label"><?php esc_html_e( 'Landing Page URL', 'ukulele' ) ?></label>
<input 
    type="text" 
    id="<?php esc_attr_e( $tag->slug ) ?>-lp" 
    name="ukulele_settings[<?php esc_attr_e( $tag->slug ) ?>-lp]"
    value="<?php echo $value_lp ?>"
/>