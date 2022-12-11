<nav class="main-header navbar navbar-expand-md navbar-dark bg-primary fixed-top">
    <div class="container-fluid">
        <a href="/" class="navbar-brand">
            <img src="/assets/image/logo/logopsp.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-5" style="opacity: .6">
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <?php
                $request       = \Config\Services::request();
                $menuaktif     = $request->uri->getSegment(1);
                ?>
                <li class="nav-item">
                    <a href="/" class="nav-link <?= $menuaktif == '' ? 'active' : null ?>">Home</a>
                </li>
                <?= $this->include('template/menu') ?>
            </ul>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <!-- Notifications Dropdown Menu -->
            <!-- <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">15</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-header">15 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-envelope mr-2"></i> 4 new messages
                        <span class="float-right text-muted text-sm">3 mins</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
                </div>
            </li> -->
            <!-- <li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li> -->
            <li class="nav-item dropdown user user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <img src="/assets/adminlte/dist/img/avatar5.png" class="user-image img-circle elevation-2 alt=" User Image">
                    <span class="hidden-xs"><?= user()->username; ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <!-- User image -->
                    <li class="user-header bg-primary">
                        <img src="/assets/adminlte/dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
                        <p>
                            <?= user()->username; ?>
                            <?php if (user()->created_at != null) : ?>
                                <small><?= date('d F Y', strtotime(user()->created_at)) ?></small>
                            <?php endif ?>
                        </p>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="#" class="btn btn-default btn-flat float-left">Profile</a>
                        </div>
                        <div class="pull-right">
                            <a href="/logout" class="btn btn-default btn-flat float-right">Sign out</a>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>