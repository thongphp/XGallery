@extends('base')
@section('content')
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-female"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Idols</span>
                    <span class="info-box-number">
                  {{\App\Models\JavIdols::count()}}
                </span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-tags"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Genres</span>
                    <span class="info-box-number">{{\App\Models\JavGenreModel::count()}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-video"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">JAV</span>
                    <span class="info-box-number">{{\App\Models\JavMovieModel::count()}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-download"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Downloadable</span>
                    <span class="info-box-number">{{\App\Models\JavMovieModel::where(['is_downloadable'=>1])->count()}}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
@stop
