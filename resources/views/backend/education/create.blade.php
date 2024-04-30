@extends('backend.layouts.default')

@section('page_title', 'Creating a new topic')

@section('style')
@stop

@section('content')

    @if( ! Auth::user()->can('manage_user'))
        @include('errors.401')
    @else
        <div class="d-flex justify-content-start align-items-center m-3">
            <a href="{{ route('education.index') }}" class="text-secondary mr-2">
                <i class="fa fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="h4"> Creating a new topic</h2>
        </div>
        <div class="card bradius">

            <div class="card-body">
                @include('backend.partials.error')

                <form method="POST" action="{{ route('education.store') }}">
                    <div class="mb-3">
                        <label for="inputName" class="form-label">Name:</label>
                        <input type="text" name="title" class="form-control" id="inputName"
                               placeholder="" required>
                    </div>
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-success bradius float-right">Save</button>
                </form>
            </div>
        </div>

    @endif

@stop
