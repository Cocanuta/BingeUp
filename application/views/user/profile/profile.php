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
            <h1><?php echo $user['username']; ?></h1>
        </div>
        <div class="pull-right">
            <p>Joined: <?php echo date_format(new DateTime($user['joined']), 'd-m-Y'); ?></p>
        </div>
    </div><!--/.row-->
        </div>
</section><!--/#about-us-->

<section id="profile" class="container">
    <div class="row">
        <div class="col-sm-12">
            <p>Joined: <?php echo date_format(new DateTime($user['joined']), 'd-m-Y'); ?></p>
        </div>
    </div>
</section>
