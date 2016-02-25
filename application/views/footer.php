<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<section id="bottom" class="wet-asphalt">
    <div class="container">
        <div class="row">
            <div class="col-sm-10 col-md-offset-1">
                <?php $attributes = array('class' => 'form form-inline', 'role' => 'form'); ?>
                <?= form_open('search', $attributes) ?>
                <div class="form-group col-sm-10">
                    <input type="text" class="form-control input-lg" id="search_query" name="search_query" placeholder="Search...">
                </div>
                    <input type="submit" class="btn btn-default btn-lg" value="Search">
                </form>
            </div>
        </div>
    </div>
</section><!--/#bottom-->

<footer id="footer" class="midnight-blue">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                &copy; 2016 <a target="_blank" href="<?php echo base_url(''); ?>" title="">BingeUp</a>, All Rights Reserved.
            </div>
            <div class="col-sm-6">
                <ul class="pull-right">

                    <!--<li><a id="gototop" class="gototop" href="#"><i class="icon-chevron-up"></i></a></li><!--#gototop-->

                </ul>
            </div>
        </div>
    </div>
</footer><!--/#footer-->

<script src="<?php echo base_url(''); ?>assets/js/jquery.js"></script>
<script src="<?php echo base_url(''); ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo base_url(''); ?>assets/js/jquery.prettyPhoto.js"></script>

</body>
</html>