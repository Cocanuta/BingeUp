<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if(isset($error)) : ?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    </div>
<?php endif; ?>

<section id="title" class="belize-hole">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h1><?php echo $movie->Title; ?> (<?php echo $movie->Year; ?>)</h1>
            </div>

        </div>
    </div>
</section><!--/#title-->

<section id="movie-info" class="container">
    <div class="row">
        <h2>Movie</h2>
        <div class="col-sm-4">
            <img class="img-responsive" src="http://img.omdbapi.com/?i=<?php echo $movie->imdbID; ?>&apikey=51b4d0d7&h=444" alt="<?php echo $movie->Title; ?> Poster" width="300px" height="444px">
        </div><!--/.col-sm-6-->
        <div class="col-sm-8">
            <h2>Information</h2>
            <p class="lead"><?php echo $movie->Plot; ?></p>
            <p class="lead">Runtime: <?php echo (int)$movie->Runtime ?> min.</p>
            <p class="lead">Genre: <?php echo $movie->Genre ?>.</p>
            <p class="lead">Director: <?php echo $movie->Director ?>.</p>
            <p class="lead">Writer: <?php echo $movie->Writer ?>.</p>
            <p class="lead">Actors: <?php echo $movie->Actors ?>.</p>
        </div><!--/.col-sm-8-->
    </div><!--/.row-->
</section><!--/#about-us-->