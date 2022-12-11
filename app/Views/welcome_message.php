<?= $this->extend('template/main') ?>
<?= $this->section('content') ?>
<div class="content-wrapper mt-5">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <h5 class="m-0"> Dashboard</h5>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- <div class="card card-primary card-outline"> -->
                    <!-- <div class="card-header">
                            <h5 class="card-title m-0">Dashboard</h5>
                        </div> -->
                    <!-- <div class="card-body">
                        </div> -->
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
    <!-- /.content -->
</div>
<!-- SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY','')) -->
<?= $this->endsection() ?>