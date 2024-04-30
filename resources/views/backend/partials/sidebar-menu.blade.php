<div class="scroll-view">
    <div>
        <div class="navbar mb-4" style="border: 0;">
            <a href="{{ route('dashboard') }}" class="site_title">
                <img src="{{asset('images/body_genuis.png')}}" alt="" width="200px" height="200/1.26px">
            </a>
        </div>
    </div>
    <br/>
    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu mt-5">
        <div class="menu_section">
            <ul class="nav side-menu" style="color: #1d2124">

               {{-- @if( Auth::user()->can('manage_role'))
                    <li><a href="{{ route('role.index') }}"><i class="fa fa-edit"></i> Manage Role </a></li>
                @endif--}}

                @if( Auth::user()->can('manage_user'))
                    <li><a href="{{ route('dashboard') }}"><i class="fa fa-home"></i> Dashboard </a></li>

{{--                    <li><a href="{{ route('user.index') }}"><i class="fa fa-user"></i> All User </a></li>--}}
                    <li><a href="{{ route('user.getUsers') }}"><i class="fa fa-user-circle"></i> User List </a></li>
                @endif

                <li><a href="{{ route('quiz.index') }}"><i class="fa fa-question-circle" aria-hidden="true"></i>
                        Quizzes List </a></li>

                @if( Auth::user()->can('manage_user'))
                    <li><a href="{{ route('education.index') }}"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Education </a></li>
                    <li><a href="{{ route('user.getTeachers') }}"><i class="fas fa-chalkboard-teacher"></i> Teacher's List </a></li>
                @endif
            </ul>
        </div>
    </div>
    <div class="logout-button-container" style="position: absolute; bottom: 0; width: 100%; text-align: center; padding: 10px 0;">
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
           class="btn btn-default" style="width: 90%; margin: 0 auto;">
            <span class="glyphicon glyphicon-log-out" style="font-size: 18px"></span> Log out
        </a>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>



</div>
