@extends('backend.layouts.default')

@section('page_title', 'Create User')

@section('style')
@stop

@section('content')

    @if( ! Auth::user()->can('manage_user'))
        @include('errors.401')
    @else
        <div class="d-flex justify-content-start align-items-center m-3">
            <a href="{{ route('user.getTeachers') }}" class="text-secondary mr-2">
                <i class="fa fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="h4"> Creating a teacher acc</h2>
        </div>
        <div class="card bradius">
            <div class="card-body">
                @include('backend.partials.error')
                <form method="POST" action="{{ route('teacher.store') }}" class="" enctype="multipart/form-data">
                    <div class="row input_row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Name: </label> <br>
                                <input type="text" name="name" required="required" placeholder="Name"
                                       class="form-control bradius col-md-8 col-xs-12">
                            </div>
                        </div>
                    </div>
                    <div class="row input_row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Email: </label> <br>
                                <input type="email" name="email" required="required" placeholder="Email"
                                       class="form-control bradius col-md-8 col-xs-12">
                            </div>
                        </div>
                    </div>
                    <div class="row input_row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Password:</label> <br>
                                <input type="password" name="password" minlength="8" required="required" placeholder="min: 8 digits"
                                       class="form-control bradius col-md-8 col-xs-12">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row input_row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-2">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-success bradius pull-right">save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    @endif

@stop
