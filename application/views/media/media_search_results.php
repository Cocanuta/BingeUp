<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if(isset($error)) : ?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    </div>
<?php endif; ?>

<section id="search" class="container">
    <div class="row">
        <div class="col-md-12">
            <?= form_open() ?>
            <div class="form-group">
                <label for="search_query">Search:</label>
                <input type="text" class="form-control" id="search_query" name="search_query" placeholder="">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" valie="Search">
            </div>
            </form>

            <?php if(isset($message)) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if(isset($search)) : ?>
                <div class="list-group">
                <?php foreach($search as $result): ?>

                    <?php if($result['Type'] === "movie"): ?>
                        <a href="<?php echo base_url('movies/'.str_replace('-', '_', str_replace(',', '', str_replace(' ', '_', $result['Title'])))."-".(int)$result['Year']); ?>" class="list-group-item"><i class="fa fa-film"></i> <?php echo $result['Title']; ?> (<?php echo (int)$result['Year']; ?>)</a>
                    <?php elseif ($result['Type'] === "series"): ?>
                        <a href="<?php echo base_url('shows/'.str_replace('-', '_', str_replace(',', '', str_replace(' ', '_', $result['Title'])))."-".(int)$result['Year']); ?>" class="list-group-item"><i class="fa fa-television"></i> <?php echo $result['Title']; ?> (<?php echo (int)$result['Year']; ?>)</a>
                    <?php endif; ?>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div><!--/.row-->
</section><!--/#about-us-->