@extends('backend.layouts.default')

@section('page_title', 'Listing Users')

@section('style')
    <style>
        .active-orange {
            background-color: orange !important;
            color: white !important;
        }

        .default-state {
            color: #3dbdf1;
            background-color: white;
        }

        .spanStyle {
            background-color: #3dbdf1;
            color: white;
            border-radius: 15px
        }

        .bradius {
            border-radius: 15px
        }

        label {
            font-size: 25px;
            margin-top: 15px;
        }

        /* For WebKit browsers like Chrome, Safari, and Edge */
        .scrollable-div::-webkit-scrollbar {
            width: 13px; /* Adjust the width of the scrollbar */
        }

        .scrollable-div::-webkit-scrollbar-thumb {
            background-color: lightblue; /* Your desired color for the scrollbar thumb */
            border-radius: 5px; /* Optional: adds rounded corners to the scrollbar thumb */
        }

        /* Always show vertical scrollbar and ensure it's visible even if content doesn't overflow */
        .scrollable-div {
            overflow-y: scroll;
            padding-right: 10px; /* Should be equal to or greater than the right padding of list-group-item for consistent alignment */
        }

        input[type="text"]::placeholder {
            color: #3dbdf1; /* Change to your desired color */
            font-family: Arial, 'Font Awesome 5 Free'; /* Ensure the icon font is applied */
        }

        .modal-dialog {
            display: flex;
            align-items: center; /* This line ensures vertical centering */
            min-height: calc(100% - (.5rem * 2)); /* This prevents the modal from touching the edges */
        }

        .modal-content {
            margin: auto; /* Helps in centering the content inside the dialog if needed */
        }
    </style>
@stop
@section('content')
    @if(auth()->user()->can('manage_user'))
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <h3>Education</h3>
                </div>
                <div class="col-6 bradius">
                    <a href="{{ route('education.create') }}"
                       class="btn btn-success pull-right bradius">Create topic</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 card card-body bradius" style="height: 750px">
                    <div class="mt-4 mb-3">
                        <form method="GET" action="{{ route('search-education') }}">
                            <div class="input-group">
                                <input type="text" name="title" class="form-control bradius"
                                       placeholder="&#128269; Search"
                                       style="font-family: Arial, 'Font Awesome 5 Free';">
                            </div>
                            {{ csrf_field() }}
                        </form>
                    </div>
                    @if($educations->isEmpty())
                        <p>No education records found.</p>
                    @else
                        <div class="list-group scrollable-div" style="height: 750px;">
                            @foreach($educations as $education)
                                <a href="{{ route('education.index', ['id' => $education->id]) }}"
                                   class="list-group-item list-group-item-action bradius mb-3 {{ $education->id === $selectedEducation->id ? 'active-orange' : 'default-state' }}"
                                   style="border-radius: 20px"
                                   data-id="{{ $education->id }}">
                                    <div class="row">
                                        <div class="col-9">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">{{ $education->title }}</h5>
                                            </div>
                                            <h5 class="mb-1">{{ $education->questions_count ?? '' }} questions</h5>
                                        </div>
                                        <div class="col-3">
                                            <i class='fa fa-angle-right mt-2 bradius {{ $education->id === $selectedEducation->id ? 'active-icon' : 'default-icon' }}'
                                               style='font-size:36px; border: 1px solid black; width: 40px; height: 40px; display: flex;
           align-items: center; justify-content: center;'></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="card bradius" style="height: 750px;">
                        <div class="mt-2">
                            <div class="row m-2">
                                <div class="col-6"><h4>Education info</h4></div>
                            </div>
                        </div>
                        @if(!is_null($selectedEducation))
                            <div class="card-body bradius"
                                 style="border: 2px solid black; margin: 15px; padding-left: 30px">
                                <div class="row">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control spanStyle" id="title"
                                           placeholder="Enter title"
                                           value="{{ $selectedEducation->title ?? ''}}">
                                    <label for="created_at">Created At</label>
                                    <input type="datetime-local" class="form-control spanStyle"
                                           id="created_at"
                                           placeholder="Enter age"
                                           value="{{ $selectedEducation->created_at ?? '' }}">
                                </div>
                                <div class="row">
                                    <div class="col-3 mt-4">
                                        <h5>Questions: {{$selectedEducation->questions_count ?? 0 }}</h5>
                                    </div>
                                    <div class="col-9">
                                        <div class="d-flex flex-column align-items-end gap-2 mt-4">
                                            <a href="{{ route('education.edit', $selectedEducation->id) }}"
                                               class="btn spanStyle bradius" type="button">To question list <i
                                                    class="fa fa-solid fa-arrow-right"></i></a>
                                            <a href="{{ route('education.info', ['id' => $selectedEducation->id]) }}"
                                               class="btn spanStyle bradius" type="button">To information of topic <i
                                                    class=" fa fa-solid fa-arrow-right"></i></a>
                                            <a href="{{ route('education.diseases', ['id' => $selectedEducation->id]) }}"
                                               class="btn spanStyle bradius" type="button">To
                                                diseases of topic <i
                                                    class=" fa fa-solid fa-arrow-right"></i></a>
                                            <form action="{{ route('education.destroy') }}" method="POST"
                                                  id="deleteForm">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="id" value="{{ $selectedEducation->id }}"/>
                                                <input type="hidden" name="_method" value="DELETE"/>
                                                <button type="button" name="Delete"
                                                        class="btn btn-sm btn-danger bradius"
                                                        data-toggle="modal" data-target="#deleteConfirmModal">Delete
                                                    education
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteConfirmModal" tabindex="-1" role="dialog"
             aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" style="width: 300px; border-radius: 10px">
                    <div class="modal-header"
                         style="background-color: #d6f1fa; display: flex; justify-content: center; align-items: center; height: 150px;">
                        <div
                            style="background-color: #abe0f6; border-radius: 100px; height: 100px; width: 100px; display: flex; justify-content: center; align-items: center;">
                            <i class='fas fa-exclamation-triangle' style='font-size:48px; color:white;'></i>
                        </div>
                    </div>
                    <div class="modal-body">
                        <h4 class="ml-3">Do you want to delete</h4>
                        <h4 style="margin-left: 80px">this education?</h4>
                        <div class="row ml-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-lg"
                                        style="border: 1px solid black; border-radius: 10px"
                                        data-dismiss="modal">No
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-lg"
                                        style="color:red; border: 1px solid black; border-radius: 10px"
                                        id="confirmDeleteBtn">Yes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        @include('errors.401')
    @endif
@stop
<script>
    function updateActiveRow(userId) {
        // Remove active-orange from all elements
        document.querySelectorAll('.list-group-item').forEach(function (item) {
            item.classList.remove('active-orange');
            item.classList.add('default-state');
        });

        // Find the new selected element and add active-orange
        let selectedElement = document.querySelector(`a[data-id="${userId}"]`);
        if (selectedElement) {
            selectedElement.classList.add('active-orange');
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        var deleteForm = document.getElementById('deleteForm');

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            deleteForm.submit();
        });
    });
</script>
