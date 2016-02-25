<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="page-header">
				<h1>Home</h1>
				<p>This is the home page.</p>
			</div>
		</div>
	</div><!-- .row -->
</div><!-- .container -->

<section id="recent" class="midnight-blue">
    <div class="container">
        <div class="row">
            <h2>Recently Added</h2>
        </div>
		<div class="row">
			<?php foreach($recent as $item): ?>
				<div class="col-md-2">
                    <div class="wet-asphalt">
                    <?php if($item->Type === "movie"): ?>
                    <a href="<?php echo base_url('movies/'.str_replace('-', '_', str_replace(',', '', str_replace(' ', '_', $item->Title)))."-".(int)$item->Year); ?>">
                    <?php elseif($item->Type === "series"): ?>
                    <a href="<?php echo base_url('shows/'.str_replace('-', '_', str_replace(',', '', str_replace(' ', '_', $item->Title)))."-".(int)$item->Year); ?>">
                    <?php endif; ?>
                    <div style="background-image:url('http://img.omdbapi.com/?i=<?php echo $item->imdbID; ?>&apikey=51b4d0d7&h=257'); position: relative; width: 171px; height: 257px; background-repeat: no-repeat; background-position: center center; -webkit-background-size: ;background-size: cover;" class="img-responsive"></div>
                    <div class="text-center"><?php echo $item->Title; ?></div>
                    </a>
                        </div>
				</div>
			<?php endforeach; ?>
		</div>
        </div>
</section><!--/#title-->