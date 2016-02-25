<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BingeUp<?php echo $pageTitle; ?></title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <!-- css -->
    <link href="<?php echo base_url(''); ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link href="<?php echo base_url(''); ?>assets/css/prettyPhoto.css" rel="stylesheet">
    <link href="<?php echo base_url(''); ?>assets/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url(''); ?>assets/css/main.css" rel="stylesheet">

    <!-- js -->


    <!--[if lt IE 9]>
    <script src="../assets/js/html5shiv.js"></script>
    <script src="../assets/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<header class="navbar navbar-inverse navbar-fixed-top wet-asphalt" role="banner">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo base_url('');?>"><strong>Binge</strong>Up</a>
        </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true && $_SESSION['is_admin'] === true) : ?>
                        <li><a href="<?= base_url('admin') ?>">Admin</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['username']) && $_SESSION['logged_in'] === true) : ?>
                        <li><a href="<?= base_url('search') ?>">Search</a></li>
                        <li><a href="<?= base_url('logout') ?>">Logout</a></li>
                        <li><a href="<?= base_url('users/'.$_SESSION['username']) ?>"><img src="http://img.bingeup.com/Default.jpg" class="img-circle" width="30px" height="30px"></a></li>
                    <?php else : ?>
                        <li><a href="<?= base_url('search') ?>">Search</a></li>
                        <li><a href="<?= base_url('register') ?>">Register</a></li>
                        <li><a href="<?= base_url('login') ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
    </div>
</header><!--/header-->
