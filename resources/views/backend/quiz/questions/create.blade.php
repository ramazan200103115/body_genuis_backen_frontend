@extends('backend.layouts.default')

@section('page_title', 'Manage Quiz')

@section('style')
    <style>

    </style>
@stop
@section('content')
    <div class="d-flex justify-content-start align-items-center m-3">
        <a href="{{ route('quiz.edit',['id'=>$quiz->id]) }}" class="text-secondary mr-2">
            <i class="fa fa-solid fa-arrow-left"></i>
        </a>
        @if($question_index > 0)
            <h2 class="h4"> Updating question {{$question_index}}</h2>
        @else
            <h2 class="h4"> Adding question</h2>
        @endif
    </div>
    <div class="card bradius">
        <div class="card-body">
            @include('backend.partials.choice')
        </div>
    </div>
@stop
