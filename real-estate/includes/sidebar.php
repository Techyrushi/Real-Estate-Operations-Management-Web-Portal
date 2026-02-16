<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="user-profile px-30 py-15">
            <div class="text-center">
                <div class="image">
                    <img src="<?php echo !empty($_SESSION['profile_image']) ? $_SESSION['profile_image'] : '../images/avatar/9.jpg'; ?>"
                        class="avatar avatar-xxxl box-shadowed" alt="User Image">
                </div>
                <div class="info mt-20">
                    <a class="dropdown-toggle px-20" data-bs-toggle="dropdown"
                        href="#"><?php echo $_SESSION['username'] ?? 'User'; ?></a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="profile_edit.php"><i class="ti-user"></i> Profile</a>
                        <!-- <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="profile_edit.php"><i class="ti-settings"></i> Settings</a> -->
                    </div>
                </div>
            </div>
            <ul class="list-inline profile-setting mt-20 mb-0 d-flex justify-content-center">
                <li><a href="auth_logout.php" data-bs-toggle="tooltip" data-bs-placement="top" title="Logout"><i
                            data-feather="log-out"></i></a></li>
            </ul>
        </div>
        <div class="multinav">
            <div class="multinav-scroll" style="height: 100%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">
                    <li class="header">MENU</li>
                    <li>
                        <a href="index.php">
                            <i data-feather="compass"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <?php if (hasRole('Admin') || hasPermission('manage_projects') || hasPermission('manage_partners')): ?>
                        <li class="header">MASTERS</li>
                        <li class="treeview">
                            <a href="#">
                                <i data-feather="database"></i>
                                <span>Masters Modules</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (hasRole('Admin') || hasPermission('manage_projects')): ?>
                                    <li><a href="admin_projects.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Projects</a></li>
                                    <li><a href="admin_banks.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Banks</a></li>
                                <?php endif; ?>
                                <?php if (hasRole('Admin') || hasPermission('manage_partners')): ?>
                                    <li><a href="admin_partners.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Partners</a></li>
                                <?php endif; ?>
                                <?php if (hasRole('Admin') || hasPermission('manage_vendors')): ?>
                                    <li><a href="admin_materials.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Materials</a></li>
                                    <li><a href="admin_vendors.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Vendors</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <li class="header">OPERATIONS</li>
                    <?php if (hasRole('Admin') || hasPermission('manage_customers')): ?>
                        <li>
                            <a href="admin_customers.php">
                                <i data-feather="users"></i>
                                <span>Customers</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (hasRole('Admin') || hasPermission('manage_expenses')): ?>
                        <li>
                            <a href="admin_expenses.php">
                                <i data-feather="dollar-sign"></i>
                                <span>Expenses</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="treeview">
                        <a href="#">
                            <i data-feather="users"></i>
                            <span>Agents</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="agentslist.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>All Agents</a></li>
                            <li><a href="addagent.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Add Agent</a></li>
                            <!-- <li><a href="agentprofile.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Agent Profile</a></li> -->
                        </ul>
                    </li>

                    <?php if (hasRole('Admin') || hasPermission('view_reports')): ?>
                        <li class="header">ANALYTICS</li>
                        <li>
                            <a href="admin_reports.php">
                                <i data-feather="pie-chart"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (hasRole('Admin') || hasPermission('manage_users') || hasPermission('manage_roles')): ?>
                        <li class="header">ADMINISTRATION</li>
                        <li class="treeview">
                            <a href="#">
                                <i data-feather="lock"></i>
                                <span>Access Control</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-right pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <?php if (hasRole('Admin') || hasPermission('manage_users')): ?>
                                    <li><a href="admin_users.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Users</a></li>
                                <?php endif; ?>
                                <?php if (hasRole('Admin') || hasPermission('manage_roles')): ?>
                                    <li><a href="admin_roles.php"><i class="icon-Commit"><span class="path1"></span><span
                                                    class="path2"></span></i>Roles</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>


                    <!-- <li class="header">Apps</li>
                    <li>
                        <a href="mailbox.php">
                            <i data-feather="mail"></i>
                            <span>Mailbox</span>
                        </a>
                    </li>
                    <li>
                        <a href="file-manager.php">
                            <i data-feather="file-plus"></i>
                            <span>File Manager</span>
                        </a>
                    </li>
                    <li>
                        <a href="contact.php">
                            <i data-feather="phone-call"></i>
                            <span>Contact</span>
                        </a>
                    </li>
                    <li class="treeview">
                        <a href="#">
                            <i data-feather="alert-triangle"></i>
                            <span>Authentication</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-right pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="auth_login.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Login</a></li>
                            <li><a href="auth_register.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Register</a></li>
                            <li><a href="auth_lockscreen.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Lockscreen</a></li>
                            <li><a href="auth_user_pass.php"><i class="icon-Commit"><span class="path1"></span><span
                                            class="path2"></span></i>Recover password</a></li>
                        </ul>
                    </li> -->
                </ul>

                <div class="sidebar-widgets">
                    <div class="copyright text-start m-25">
                        <p><strong class="d-block">Real Estate Admin Dashboard</strong> © <?php echo date('Y'); ?> All
                            Rights Reserved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>