<?php
/**
 * Created by PhpStorm.
 * User: yupal
 * Date: 2/11/2019
 * Time: 11:14 AM
 */

namespace ColibriWP\Theme\Components;


use ColibriWP\Theme\Core\ComponentBase;
use ColibriWP\Theme\Defaults;
use ColibriWP\Theme\Translations;

class FrontPageContent extends PageContent {

	public function renderContent() {
		?>
        <div class="page-content colibri-page-content">
            <div class="content">
				<?php
				while ( have_posts() ) : the_post();
					the_content();
				endwhile;
				?>
            </div>
        </div>
		<?php
	}
}
