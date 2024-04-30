@extends('layouts.app')
<style>
    .login-image {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }

    .password-wrapper {
        position: relative;
    }

    .toggle-password {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 18px;
    }

    input.custom-input {
        display: block;
        width: 100%; /* Full width */
        padding: 0.375rem 0.75rem; /* Padding similar to form-control */
        font-size: 1rem; /* Font size */
        font-weight: 400; /* Font weight */
        line-height: 1.5; /* Line height */
        color: #212529; /* Text color */
        background-color: #fff; /* Background color */
        background-clip: padding-box; /* Background properties */
        border: 1px solid black; /* Border properties */
        appearance: none; /* Standardizing appearance across browsers */
        border-radius: 20px; /* Border radius */
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out; /* Transition for interaction */
    }

    input.custom-input:focus {
        color: #212529;
        background-color: #fff;
        border-color: #fff;
        outline: 0;
        box-shadow: #fff;
    }


    .password-toggle-icon i {
        font-size: 18px;
        line-height: 1;
        color: #333;
        transition: color 0.3s ease-in-out;
        margin-bottom: 20px;
    }

    .password-toggle-icon i:hover {
        color: #000;
    }
</style>

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 mt-5">
                <div class="align-items-center">
                    <img src="{{ asset('images/body_genuis.png') }}" alt="Login Illustration"
                         class="login-image img-fluid" style="width: 350px; height: 300px">
                </div>

                {{-- Login form --}}
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <h5>Почта</h5>
                    <input id="email" name="email" type="email" class="custom-input" placeholder="email" required autofocus>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                    <br>
                    <h5>Пароль</h5>
                    <div class="password-wrapper">
                        <input id="password" type="password" name="password" class="custom-input" placeholder="password" required autofocus>
                        <span id="togglePassword" class="toggle-password">
                                        <i class="fa fa-eye-slash" aria-hidden="true"></i>
                                    </span>
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                    <br>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn" style="width: 300px; background-color: #18b0e8;
                         color: white; border-radius: 20px">Войти</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('togglePassword');

            if (passwordInput && togglePasswordButton) {
                togglePasswordButton.addEventListener('click', function () {
                    // Check if the password is currently visible
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    // Optionally, change the eye icon or text
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('fa-eye-slash')) {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    } else {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    }                });
            }
        });
    </script>
@endpush

