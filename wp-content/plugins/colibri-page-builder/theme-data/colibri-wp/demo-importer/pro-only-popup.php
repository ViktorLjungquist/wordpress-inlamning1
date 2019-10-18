<script type="text/template" id="tmpl-extendthemes-import-popup-pro" style="display: none">
    <div class="extendthemes-demo-import-popup-container pro-only">
        <div class="image-container">
            <img src="{{ data.preview_image }}"/>
        </div>
        <div class="info">

            <h2><?php esc_html_e( 'You are about to import a demo site:', 'colibri-page-builder' ); ?></h2>
            <ul>
                <li><?php esc_html_e( 'No existing content or any other data will be deleted.',
						'colibri-page-builder' ); ?></li>
                <li><?php esc_html_e( 'Current pages will be moved to trash. You can restore them back at any time.',
						'colibri-page-builder' ); ?></li>
                <li><?php esc_html_e( 'Posts, pages, images, widgets, menus and other theme settings will get imported.',
						'colibri-page-builder' ); ?></li>
            </ul>
        </div>
        <div class="popup-footer">
            <div class="footer-content">
                <h2><?php echo esc_html( sprintf( __( 'This demo site is available only in %s',
						'colibri-page-builder' ),
						apply_filters( "mesmerize_demos_available_in_pro", "Colibri Page Builder PRO" ) ) ); ?></h2>
                <a href='http://colibriwp.com' class='button button-hero button-primary upgrade-to-pro'
                   target='_blank'><?php esc_html_e( 'Check all PRO features', 'colibri-page-builder' ); ?></a>
            </div>
        </div>
    </div>
</script>
