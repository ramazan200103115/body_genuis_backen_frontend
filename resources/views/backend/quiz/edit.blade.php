@extends('backend.layouts.default')

@section('page_title', 'Manage Quiz')

@section('style')
    <style>
        /* Custom styles to make text inputs look like buttons */
        .btn-input {
            padding: .375rem .75rem; /* Same padding as Bootstrap buttons */
            border: 1px solid transparent; /* Hide the default border */
            border-radius: .25rem; /* Same border-radius as Bootstrap buttons */
            color: #fff; /* Text color */
            display: inline-block; /* To flow like buttons */
            text-align: center; /* Center the text */
            vertical-align: middle; /* Align with other inline elements */
            cursor: pointer; /* Pointer cursor like buttons */
        }

        /* You may need to adjust the height to align with other buttons */
        .btn-input[type="text"] {
            height: 38px; /* Example height, adjust as needed */
        }

        /* Hover effect */
        .btn-input:hover {
            filter: brightness(95%); /* Slightly darken the button on hover */
        }

        .custom-success {
            background-color: #2fa360;
            color: #ffffff;
            border: 1px solid black;
        }

        .custom-light {
            background-color: #ffffff;
            color: #1b1e21;
            border: 1px solid black;
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
    <div class="row">
        <div class="col-8">
            <div class="d-flex justify-content-start align-items-center">
                <a href="{{ route('quiz.index', ['id' => $quiz->id]) }}" class="text-secondary mr-2">
                    <i class="fa fa-solid fa-arrow-left"></i>
                </a>
                <h2 class="h4"> {{ $quiz->title }} : </h2>
                <span style="font-size: 25px; margin-left: 5px"> questions: {{$questions->count()}}</span>

            </div>
        </div>
        <div class="col-4"><a href="{{ route('create.question',['id'=>0, 'quiz_id' => $quiz->id]) }}"
                              class="btn btn-success pull-right bradius">Add question</a></div>
    </div>
    <div class="card card-body" style="border-radius: 20px">
        @foreach ($questions as $key => $question)
            <div class="card card-body bradius mb-2" style="border: 1px solid black">
                <div class="row input_row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-10 col-sm-10 col-xs-10">
                                <h2 style="font-weight: bold">{{$key+1}}. {{ $question->question }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-10">
                        @foreach ($question->options as $key => $option)
                            <input type="text"
                                   readonly
                                   name="questions[{{ $question->id }}]"
                                   id="question_{{ $question->id }}_option_{{ $key }}"
                                   value="{{ $option->option }}"
                                   class="btn-input {{ $option->is_right_option ? 'custom-success' : 'custom-light' }}">

                        @endforeach
                    </div>
                    <div class="col-2">
                        <div class="row float-right">
                            <div class="col-4">
                                <a href="{{ route('create.question',['id'=>$question->id, 'quiz_id' => $quiz->id]) }}"
                                   class="btn bradius border-0" style="background-color: #32d9f6">
                                    <i class='fas fa-pen' style='font-size:16px; color: black'></i>
                                </a>
                            </div>
                            <div class="col-4">
                                <form action="{{ route('delete.question') }}" method="POST" id="deleteForm">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="id" value="{{ $question->id }}"/>
                                    <input type="hidden" name="quizID" value="{{ $quiz->id }}"/>
                                    <input type="hidden" name="_method" value="DELETE"/>
                                    <button type="button" class="btn btn-danger bradius border-0"
                                            data-toggle="modal" data-target="#deleteConfirmModal">
                                        <i class='fas fa-trash' style='font-size: 16px; color: white'></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
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
                    <h4 style="margin-left: 80px">this question?</h4>
                    <div class="row ml-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-lg" style="border: 1px solid black; border-radius: 10px"
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

@stop
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var deleteForm = document.getElementById('deleteForm');

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            deleteForm.submit();
        });
    });

</script>
