@extends('backend.layouts.default')

@section('page_title', 'Listing Users')

@section('style')
    <style>
        .image-wrapper {
            position: relative;
            display: inline-block; /* Adjust this as necessary for your layout */
            max-width: 150px; /* Match this with your image dimensions if needed */
            margin-right: 20px; /* Adjust this value to create the desired space */
        }

        .img-thumbnail {
            display: block;
            width: 100%; /* Ensure the image covers the area of the wrapper */
            height: auto;
        }

        .edit-icon {
            position: absolute;
            bottom: 0;
            right: -40px; /* Negative value moves icon outside of the image wrapper */
            color: red;
            font-size: 28px;
            cursor: pointer; /* Suggests that the icon is interactive */
            background: white; /* Add background to make icon stand out if needed */
            border-radius: 50%; /* Optional: makes the background rounded */
            padding: 5px; /* Optional: adds spacing inside the background */
            margin-top: 10px;
        }


        .active-orange {
            background-color: orange !important;
            color: white;
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
            border-radius: 10px
        }

        label {
            font-size: 18px;
            margin-top: 10px;
            margin-bottom: 2px;
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

    @if(auth()->user()->can('manage_quiz'))
        <div class="container">
            <div class="row mb-0">
                <div class="col-6">
                    <h3>Quizes List</h3>
                </div>
                <div class="col-6 bradius">
                    <a href="{{ route('quiz.create') }}"
                       class="btn btn-success pull-right bradius">Create quiz</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-body bradius" style="height: 750px">
                        <div class="mt-2 mb-2">
                            <form method="GET" action="{{ route('search-quiz') }}">
                                <div class="input-group">
                                    <input type="text" name="title" class="form-control bradius"
                                           placeholder="&#128269; Search"
                                           style="font-family: Arial, 'Font Awesome 5 Free';">
                                </div>
                                {{ csrf_field() }}
                            </form>
                        </div>
                        @if($quizzes->isEmpty())
                            <p>No quizzes records found.</p>
                        @else
                            <div class="list-group scrollable-div" style="height: 700px;">
                                @foreach($quizzes as $quiz)
                                    <a href="{{ route('quiz.index', ['id' => $quiz->id]) }}"
                                       class="list-group-item list-group-item-action bradius mb-2 {{ $quiz->id === $selectedquiz->id ? 'active-orange' : 'default-state' }}"
                                       style="border-radius: 20px; border: 1px solid black"
                                       data-id="{{ $quiz->id }}">
                                        <div class="row">
                                            <div class="col-9">
                                                <h5 class="mb-1">{{ $quiz->title ? $quiz->title : '' }}</h5>
                                                <h5>by {{$quiz->creator->name ?? ''}}</h5>
                                                <h5>{{$quiz->questions_count ?? ''}} questions</h5>
                                                <h5>{{$quiz->participants_count ?? ''}} participants</h5>
                                            </div>
                                            <div class="col-3">
                                                <i class='fa fa-angle-right mt-5 bradius {{ $quiz->id === $selectedquiz->id ? 'active-icon' : 'default-icon' }}'
                                                   style='font-size:36px; border: 1px solid black; width: 40px; height: 40px; display: flex;
           align-items: center; justify-content: center;'></i>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card bradius p-1" style="height: 750px;">
                        <div class="mt-1">
                            <div class="row ml-3">
                                <div class="col-6"><h4>Quiz info</h4></div>
                            </div>
                        </div>
                        @if(!empty($selectedquiz))
                            <div class="card-body bradius" style="border: 2px solid black; margin: 15px">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="image-wrapper text-center">
                                            <div class="image-wrapper text-center">
                                                <img
                                                    src="{{ !empty($selectedquiz->image_url) ? asset('storage/' . $selectedquiz->image_url) : 'https://via.placeholder.com/150' }}"
                                                    alt="Quiz Avatar" class="img-thumbnail mx-auto d-block"
                                                    id="quizImage">
                                                <i class="fa fa-edit edit-icon mt-3"
                                                   style="font-size:28px; color:red; cursor:pointer;"
                                                   onclick="document.getElementById('imageInput').click();"></i>
                                                <input type="file" id="imageInput" style="display: none;"
                                                       onchange="uploadImage(this)"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control spanStyle" id="name"
                                               placeholder="Enter name"
                                               value="{{ $selectedquiz->title ?? ''}}">
                                        <label for="author">Author</label>
                                        <input type="text" class="form-control spanStyle" id="author"
                                               placeholder="Enter author"
                                               value="{{ $selectedquiz->creator->name . ' '. $selectedquiz->creator->email ?? ''}}">
                                        <label for="created_at">Created At</label>
                                        <input type="datetime-local" class="form-control spanStyle"
                                               id="created_at"
                                               placeholder="Enter age"
                                               value="{{ $selectedquiz->created_at ?? '' }}">
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-6">
                                        <div class="spanStyle text-white p-3 mb-2">
                                            Participants count
                                            <h5>{{ $selectedquiz->participants_count}}</h5>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="spanStyle text-white p-3">
                                            Average score of participants
                                            <h5>{{ $selectedquiz->participants_average_score}}%</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="spanStyle text-white p-3 mb-2">
                                            The champion of quiz
                                            <h4>{{ $selectedquiz->champName}}</h4>
                                            <h6>{{ $selectedquiz->champScore}}</h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        @if($selectedquiz->is_private == 0)
                                            <form action="{{ route('quiz.private') }}" method="POST"
                                                  onsubmit="return confirm('Make private this quiz?');">
                                                {{ csrf_field() }}
                                                <div class="input-group mb-2">
                                                    <input type="hidden" name="id" value="{{ $selectedquiz->id }}"/>
                                                    <input type="hidden" name="_method" value="post"/>
                                                    <button type="submit" name="Public_quiz"
                                                            class="btn btn-success bradius">Public quiz
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <form action="{{ route('quiz.private') }}" method="POST"
                                                  onsubmit="return confirm('Make public this quiz?');">
                                                {{ csrf_field() }}
                                                <div class="input-group mb-2">
                                                    <input type="hidden" name="id" value="{{ $selectedquiz->id }}"/>
                                                    <input type="hidden" name="_method" value="post"/>
                                                    <button type="submit" name="Private_quiz"
                                                            class="btn btn-warning bradius">Private quiz
                                                    </button>
                                                    <input type="text" class="form-control bradius"
                                                           placeholder="XXXXXXXX"
                                                           value="{{$selectedquiz->code}}">
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <div class="row float-right">
                                            <div>
                                                @if($selectedquiz->is_active == 1)
                                                    <form action="{{ route('quiz.inactivate') }}" method="POST"
                                                          onsubmit="return confirm('Inactivate this quiz?');">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="id" value="{{ $selectedquiz->id }}"/>
                                                        <input type="hidden" name="_method" value="post"/>
                                                        <button type="submit" name="Inactive_quiz"
                                                                class="btn btn-sm btn-success bradius">Active quiz
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('quiz.activate') }}" method="POST"
                                                          onsubmit="return confirm('Activate this quiz?');">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="id" value="{{ $selectedquiz->id }}"/>
                                                        <input type="hidden" name="_method" value="post"/>
                                                        <button type="submit" name="Inactive_quiz"
                                                                class="btn btn-sm btn-danger bradius">Inactive quiz
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                            <div>
                                                <form action="{{ route('quiz.destroy') }}" method="POST"
                                                      id="deleteForm">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="id" value="{{ $selectedquiz->id }}"/>
                                                    <input type="hidden" name="_method" value="DELETE"/>
                                                    <button type="button" name="Delete"
                                                            class="btn btn-sm btn-danger bradius"
                                                            data-toggle="modal" data-target="#deleteConfirmModal">Delete
                                                        quiz
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-3">
                                        <span>Questions: {{$selectedquiz->questions_count}}</span>
                                    </div>
                                </div>

                                <a href="{{ route('quiz.edit', $selectedquiz->id) }}"
                                   class="btn mt-1 spanStyle  pull-right btn-sm btn-success">To question list →</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
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
                        <h4 style="margin-left: 80px">this quiz?</h4>
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
@if($selectedquiz instanceof \App\Models\Quiz)
    <script>
        function uploadImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    document.getElementById('quizImage').src = e.target.result;
                };

                reader.readAsDataURL(input.files[0]); // Preview the image

                // Prepare the image file to be sent in a FormData object
                var formData = new FormData();
                formData.append('image', input.files[0]); // Append the file
                formData.append('_token', '{{ csrf_token() }}'); // Append CSRF token
                formData.append('id', '{{ $selectedquiz->id }}'); // Append the quiz ID

                // Send the request to the server endpoint
                fetch('{{ route("quiz.updateImage", $selectedquiz->id) }}', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data); // Handle success
                    })
                    .catch((error) => {
                        console.error('Error:', error); // Handle errors
                    });
            }
        }
    </script>
@endif
