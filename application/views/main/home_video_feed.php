<div class="container">
	<div class="row">
		<!-- Section for the already described videos -->
		<div id="standardFeed" class="col-md-5">
			<h4>
				Highest Rated Standard Feed
			</h4>

			<ul id="searchResults" class="media-list">

				<li>
					<?php $this->load->view('main/feedResult'); ?>
				</li>
			</ul>
		</div>

		<div id="describedFeed" class="col-md-5 pull-right">
			<h4>
				Highest Rated Described Videos
			</h4>

			<ul id="searchResults"  class="media-list">
				<?php $this->load->view('main/descriptionResult'); ?>
			</ul>
		</div>
	</div>
</div>
