<?php
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/auth_session.php';
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from master-admin-template.multipurposethemes.com/bs5/real-estate/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Feb 2026 09:55:31 GMT -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://master-admin-template.multipurposethemes.com/bs5/images/favicon.ico">

    <title>Master Admin - Dashboard</title>

    <!-- Vendors Style-->
    <link rel="stylesheet" href="css/vendors_css.css">


    <!-- Style-->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/skin_color.css">

</head>

<body class="hold-transition light-skin sidebar-mini theme-primary fixed">

    <div class="wrapper">
        <div id="loader"></div>

        <header class="main-header">
            <div class="d-flex align-items-center logo-box justify-content-start">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    <!-- logo-->
                    <div class="logo-mini w-30">
                        <span class="light-logo"><img src="../images/logo-letter.png" alt="logo"></span>
                        <span class="dark-logo"><img src="../images/logo-letter.png" alt="logo"></span>
                    </div>
                    <div class="logo-lg">
                        <span class="light-logo"><img src="../images/logo-dark-text.png" alt="logo"></span>
                        <span class="dark-logo"><img src="../images/logo-light-text.png" alt="logo"></span>
                    </div>
                </a>
            </div>
            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <div class="app-menu">
                    <ul class="header-megamenu nav">
                        <li class="btn-group nav-item">
                            <a href="#" class="waves-effect waves-light nav-link push-btn btn-outline no-border"
                                data-toggle="push-menu" role="button">
                                <img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/collapse.svg"
                                    class="img-fluid svg-icon" alt="">
                            </a>
                        </li>
                        <li class="btn-group d-lg-inline-flex d-none">
                            <div class="app-menu">
                                <div class="search-bx mx-5">
                                    <form>
                                        <div class="input-group">
                                            <input type="search" class="form-control" placeholder="Search"
                                                aria-label="Search" aria-describedby="button-addon2">
                                            <div class="input-group-append">
                                                <button class="btn" type="submit" id="button-addon3"><img
                                                        src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/search.svg"
                                                        class="img-fluid svg-icon" alt=""></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </li>
                        <li class="btn-group nav-item d-none d-xl-inline-block">
                            <a href="extra_calendar.php"
                                class="waves-effect waves-light nav-link btn-outline no-border svg-bt-icon"
                                title="Chat">
                                <img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/event.svg"
                                    class="img-fluid svg-icon" alt="">
                            </a>
                        </li>
                        <li class="btn-group nav-item d-none d-xl-inline-block">
                            <a href="extra_taskboard.php"
                                class="waves-effect waves-light btn-outline no-border nav-link svg-bt-icon"
                                title="Taskboard">
                                <img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/correct.svg"
                                    class="img-fluid svg-icon" alt="">
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="navbar-custom-menu r-side">
                    <ul class="nav navbar-nav">
                        <li class="btn-group nav-item d-lg-inline-flex d-none">
                            <a href="#" data-provide="fullscreen"
                                class="waves-effect waves-light nav-link btn-outline no-border full-screen"
                                title="Full Screen">
                                <img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/fullscreen.svg"
                                    class="img-fluid svg-icon" alt="">
                            </a>
                        </li>
                        <!-- Notifications -->

                        <!-- User Account-->
                        <li class="dropdown user user-menu">
                            <a href="#" class="waves-effect waves-light dropdown-toggle btn-outline no-border"
                                data-bs-toggle="dropdown" title="User">
                                <img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/user.svg"
                                    class="img-fluid svg-icon" alt="">
                            </a>
                            <ul class="dropdown-menu animated flipInX">
                                <li class="user-body">
                                    <a class="dropdown-item" href="profile_edit.php"><i class="ti-user text-muted me-2"></i>
                                        Profile</a>
                                    <a class="dropdown-item" href="profile_edit.php"><i class="ti-settings text-muted me-2"></i>
                                        Settings</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="auth_logout.php"><i class="ti-lock text-muted me-2"></i> Logout</a>
                                </li>
                            </ul>
                        </li>
                        <!-- Control Sidebar Toggle Button -->
                        <!-- <li>
                            <a href="#" data-toggle="control-sidebar" title="Setting"
                                class="waves-effect waves-light btn-outline no-border">
                                <img src="https://master-admin-template.multipurposethemes.com/bs5/images/svg-icon/settings.svg"
                                    class="img-fluid svg-icon" alt="">
                            </a>
                        </li> -->

                    </ul>
                </div>
            </nav>
        </header>