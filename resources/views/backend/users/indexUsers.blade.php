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
            padding-right: 30px; /* Should be equal to or greater than the right padding of list-group-item for consistent alignment */
        }

        input[type="text"]::placeholder {
            color: #3dbdf1; /* Change to your desired color */
            font-family: Arial, 'Font Awesome 5 Free'; /* Ensure the icon font is applied */
        }

    </style>
@stop

@section('content')

    @if(auth()->user()->can('manage_user'))
        <div class="container mt-2">
            <h3>Users List</h3>
            <div class="row">
                <div class="col-md-4 card card-body bradius" style="height: 750px;">
                    <div class="mt-2 mb-2">
                        <form method="GET" action="{{ route('search-user') }}">
                            <div class="input-group">
                                <input type="text" name="name" class="form-control bradius"
                                       placeholder="&#128269; Search"
                                       style="font-family: Arial, 'Font Awesome 5 Free';">
                            </div>
                            <input type="hidden" name="type" value="0">
                            {{ csrf_field() }}
                        </form>
                    </div>

                    <div class="list-group scrollable-div" style="height: 700px;">
                        @foreach($users as $user)
                            <a href="{{ route('user.getUsers', ['id' => $user->id]) }}"
                               class="list-group-item list-group-item-action bradius mb-3 {{ $selectedUser->id === $user->id ? 'active-orange' : 'default-state' }}"
                               style="border-radius: 20px"
                               data-id="{{ $user->id }}">
                                <div class="row">
                                    <div class="col-9">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h5 class="mb-1">{{ $user->email }}</h5>
                                        </div>
                                        <h5 class="mb-1">{{ $user->name }}</h5>
                                        <h5>{{ $user->medcoins }} MEDC</h5>
                                    </div>
                                    <div class="col-3">
                                        <i class='fa fa-angle-right mt-3 bradius {{ $selectedUser->id === $user->id ? 'active-icon' : 'default-icon' }}'
                                           style='font-size:36px; border: 1px solid black; width: 40px; height: 40px; display: flex;
           align-items: center; justify-content: center;'></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card bradius p-1" style="height: 750px;">
                        <div class="row ml-3 mt-1">
                            <div class="col-6"><h4>User info</h4></div>
                            {{-- <div class="col-6 bradius">
                                 <a href="{{ route('user.create') }}" class="btn btn-success pull-right bradius">Add
                                     User</a>
                             </div>--}}
                        </div>
                        <div class="card-body bradius mx-2 p-1" style="border: 1px solid black;">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="{{ asset('images/avatar.png') }}" alt="User Avatar"
                                         class="img-thumbnail rounded-circle mx-auto d-block" width="120px"
                                         height="120px">
                                </div>
                                <div class="col-md-9">
                                    <label for="userName">Name</label>
                                    <input type="text" class="form-control spanStyle" id="userName"
                                           placeholder="Enter name" value="{{ $selectedUser->name }}">
                                    <label for="userAge">Age</label>
                                    <input type="number" class="form-control spanStyle" id="userAge"
                                           placeholder="Enter age" value="{{ $selectedUser->age }}">
                                    <label for="userEmail">Email</label>
                                    <input type="email" class="form-control spanStyle" id="userEmail"
                                           placeholder="Enter email" value="{{ $selectedUser->email }}">
                                    <label for="aboutUser">About</label>
                                    <textarea class="form-control spanStyle" id="aboutUser">{{ $selectedUser->aboutMe }}</textarea>
                                    <label for="">Average Score Per Quiz:</label><span
                                        class="form-control spanStyle"
                                        style="padding-left: 10px">{{   $selectedUser->average_score }}%</span>
                                    <label for="">Passed Quizzes:</label> <span
                                        class="form-control spanStyle"
                                        style="padding-left: 10px">{{    $selectedUser->passed_quizzes }}</span>
                                    <label for="">MEDC:</label> <span
                                        class="form-control  spanStyle"
                                        style="padding-left: 10px">{{ $selectedUser->medcoins }}</span>
                                    <label for="">Created At:</label> <span
                                        class="form-control spanStyle"
                                        style="padding-left: 10px">{{   $selectedUser->created_at->format('d.m.Y') }}</span>
                                </div>
                                <div class="col-12 col-md-12">
                                    <a href="{{ route('user.destroy', $selectedUser->id) }}"
                                       onclick="event.preventDefault(); document.getElementById('delete-form').submit();"
                                       class="btn btn-sm btn-danger mt-2 spanStyle pull-right"
                                       style="background-color: red;">{{$selectedUser->blocked ? 'Unblock User' : 'Block User'}}</a>

                                    <form id="delete-form" action="{{ route('user.destroy', $selectedUser->id) }}"
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
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
    function getUserInfoAndUpdateActive(element) {
        var userId = element.getAttribute('data-id');

        // AJAX call to update the selected user and fetch updated info
        $.ajax({
            url: '/user', // Ensure this route is defined in your Laravel routes
            type: 'GET',
            data: {id: userId},
            success: function (response) {
                updateActiveRow(userId);
                // Optionally update user info display if response contains new user info
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    }

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
</script>

