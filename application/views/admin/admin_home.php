<div class="container">
    <h1>Welcome <?php echo $username; ?>!</h1>
    <a href="<?= base_url('admin/users') ?>" class="btn btn-default" role="button">Users</a>
    <a href="<?= base_url('admin/movies') ?>" class="btn btn-default" role="button">Movies</a>
    <a href="<?= base_url('admin/tv') ?>" class="btn btn-default" role="button">TV</a>
</div>