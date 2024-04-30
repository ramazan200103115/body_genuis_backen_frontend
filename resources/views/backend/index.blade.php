@extends('backend.layouts.default')

@section('page_title', 'Dashboard')

@section('style')

@stop
<style >
    .bold-and-blue {
        color: #0ae3e3;
    }

    .iclass {
        color: #0ae3e3;
    }

    .h2class {
        color: #1b1e21;
    }

    .h3class {
        color: #0ae3e3;
    }
</style>
@section('content')
    <div class="container mt-4">
        <h1>Dashboard</h1>
        <div class="row">
            <!-- Total Users Card -->
            <div class="col-md-4">
                <div class="card text-center" style="border-radius: 25px">
                    <div class="card-body">
                        <h2 class="card-title h2class">Total users</h2>
                        <p class="card-text display-4 bold-and-blue">{{ $totalUsers }}</p>
                    </div>
                </div>
            </div>
            <!-- Total Quizzes Card -->
            <div class="col-md-4">
                <div class="card text-center" style="border-radius: 25px">
                    <div class="card-body">
                        <h2 class="card-titleh 2class">Total quizzes</h2>
                        <p class="card-text display-4 bold-and-blue">{{ $totalQuizzes }}</p>
                    </div>
                </div>
            </div>
            <!-- Average Age of Users Card -->
            <div class="col-md-4">
                <div class="card text-center" style="border-radius: 25px">
                    <div class="card-body">
                        <h2 class="card-title 2class">Average age of users</h2>
                        <p class="card-text display-4 bold-and-blue">{{ $averageAge }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <div class="card" style="border-radius: 20px">
                <div class="card-body">
                    <h2 style="font-weight: bold">Monthly report</h2>
                    <div class="form-group pull-right top_search">
                        <div class="dropdown">
                            <button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 15px">
                                {{ $selectedMonth }}
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <!-- Dynamic month selection -->
                                @foreach($months as $month)
                                    <a class="dropdown-item" href="#">{{ $month }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="justify-content-around">
                        <div class="row" style="width: 300px">
                            <div class="col-2 mt-4">
                                <i class="fa fa-line-chart fa-2x iclass"></i>
                            </div>
                            <div class="col-8">
                                <h2 class="h2class">Average score of users</h2>
                                <h3 class="h3class">{{ $averageScore }}%</h3>
                            </div>
                        </div>
                        <div class="row" style="width: 300px">
                            <div class="col-2 mt-4">
                                <i class="fa fa-user-plus fa-2x iclass"></i>
                            </div>
                            <div class="col-8">
                                <h2 class="h2class">New users</h2>
                                <h3 class="h3class">{{ $newUsers }}%</h3>
                            </div>
                        </div>
                        <div class="row" style="width: 300px">
                            <div class="col-2 mt-4">
                                <i class="fa fa-calendar fa-2x iclass"></i>
                            </div>
                            <div class="col-8">
                                <h2 class="h2class">New quizzes</h2>
                                <h3 class="h3class">{{ $newQuizzes }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--<div class="row">
        <div class="col-md-12 col-sm-12  ">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Quiz Participants</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    @foreach ($questionAnswers as $questions)

                        <div class="row input_row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Answer By </label>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        {{ $questions->email }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row input_row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Question </label>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        {{ $questions->questions->question }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row input_row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">User Answer </label>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                            <?php //echo '<pre>'; var_dump($questions->questions->options) ?>
                                        @foreach($questions->questions->options as $opt)
                                            @php
                                                // echo '<pre>';
                                                // var_dump($opt->option);
                                            @endphp
                                            --}}{{-- {{ $opt->option }} --}}{{--
                                            @if ($questions->option_id == $opt->id)
                                                {{ $opt->option }},
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row input_row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Right Answer </label>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        @foreach($questions->questions->options as $opt)
                                            @php
                                                // echo '<pre>';
                                                // var_dump($opt->is_right_option);
                                            @endphp
                                            @if ($opt->is_right_option == 1)
                                                {{ $opt->option }}
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row input_row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label col-md-2 col-sm-2 col-xs-12">Right Answer </label>
                                    <div class="col-md-10 col-sm-10 col-xs-10">
                                        @if($questions->is_right == 1)
                                            <button class="btn btn-sm btn-success">Correct</button>
                                        @else
                                            <button class="btn btn-sm btn-danger">Wrong</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                    @endforeach


                    <div class="table-responsive">
                        <div>Showing
                            {{ ($questionAnswers->currentpage()-1) * $questionAnswers->perpage()+1}} to
                            {{(($questionAnswers->currentpage()-1) * $questionAnswers->perpage())+$questionAnswers->count()}}
                            of
                            {{$questionAnswers->total()}} records
                        </div>

                        {{ $questionAnswers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>--}}

@stop

@section('script')

@stop
