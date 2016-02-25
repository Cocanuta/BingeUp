<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
    <div class="row">
    <h2>Add Show</h2>
    <?php if(isset($error)) : ?>
    <div class="col-md-12">
        <div class="alert alert-danger" role="alert">
            <?= $error ?>
        </div>
    </div>
    <?php endif; ?>
    <div class="col-md-12">
        <?= form_open() ?>
        <div class="form-group">
            <label for="show_name">Show Name</label>
            <input type="text" class="form-control" id="show_name" name="show_name" placeholder="Enter Show Name">
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-default" valie="Search">
        </div>
        </form>
    </div>
        <?php if(isset($search)) : ?>
            <?php foreach($search as $item): ?>
                    <?php echo $item['name']."</br>"; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
</div>