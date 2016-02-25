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
                <h1><?php echo $show->Title; ?> (<?php echo $show->Year; ?>)</h1>
            </div>

        </div>
    </div>
</section><!--/#title-->

<section id="show-info" class="container">
    <div class="row">
        <h2>Show</h2>
        <?php if(isset($message)) : ?>
            <div class="col-md-12">
                <div class="alert alert-info" role="alert">
                    <?= $message ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-sm-4">
            <img class="img-responsive" src="http://img.omdbapi.com/?i=<?php echo $show->imdbID; ?>&apikey=51b4d0d7&h=444" alt="<?php echo $show->Title; ?> Poster" width="300px" height="444px">
        </div><!--/.col-sm-6-->
        <div class="col-sm-8">
            <h2>Information</h2>
            <p class="lead"><?php echo $show->Plot; ?></p>
            <p class="lead">Average Runtime: <?php echo (int)$show->Runtime ?> min.</p>
            <p class="lead">Genre: <?php echo $show->Genre ?>.</p>
            <p class="lead">Director: <?php echo $show->Director ?>.</p>
            <p class="lead">Writer: <?php echo $show->Writer ?>.</p>
            <p class="lead">Actors: <?php echo $show->Actors ?>.</p>
        </div><!--/.col-sm-8-->
    </div><!--/.row-->

    <div id="episode-info" class="row">
    <h2>Episodes</h2>

        <?php if(isset($message)) : ?>
            <div class="col-md-12">
                <div class="alert alert-info" role="alert">
                    Episode data on their way, check back in a mo'!
                </div>
            </div>
        <?php else: ?>
            <?php $seasonCount = end($show->Episodes)->Season; ?>
            <?php for($i=1; $i<=$seasonCount; $i++): ?>
        <h3>Season <?php echo $i; ?></h3>
        <ul class="list-group">
            <?php foreach($show->Episodes as $episode) : ?>
                <?php if((int)$episode->Season === $i): ?>
                    <li class="list-group-item">
                        <span class="label label-default pull-right"><?php echo (int)$episode->Runtime; ?> min</span>
                        <a data-toggle="collapse" href="#<?php echo $episode->imdbID; ?>">Episode <?php echo $episode->Episode; ?> - <?php echo $episode->Title; ?></a>
                        <div id ="<?php echo $episode->imdbID; ?>" class="collapse">
                            <div class="row">
                                <div class="col-md-3">
                                    <div style="background-image:url('http://img.omdbapi.com/?i=<?php echo $episode->imdbID; ?>&apikey=51b4d0d7&h=444'); position: relative; width: 100%; height: 0; padding-bottom: 70%; background-repeat: no-repeat; background-position: center center; -webkit-background-size: ;background-size: cover;"></div>
                                </div>
                                <div class="col-md-9">
                                    <p><?php echo $episode->Plot; ?></p>
                                    <p>Release: <?php echo $episode->Released ?>.</p>
                                    <p>Director: <?php echo $episode->Director ?>.</p>
                                    <p>Writer: <?php echo $episode->Writer ?>.</p>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <?php endfor; ?>
        <?php endif; ?>

    </div>


</section>
