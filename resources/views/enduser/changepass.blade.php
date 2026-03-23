@extends('home.main')
@section('body')
    <div class="main-content">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative text-center">
                        <!-- Logo centered on top of the card header -->
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header">
                            <!-- Add padding-top to account for the logo -->
                            <h4>EDIT ACCOUNT DETAILS</h4>
                        </div>


                        <div class="card-body">

                            <form action="{{ route('passChange', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- NAME --}}
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" value="{{ $user->fname }}" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Middle Name</label>
                                        <input type="text" class="form-control" value="{{ $user->mname }}" readonly>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" value="{{ $user->lname }}" readonly>
                                    </div>
                                </div>

                                {{-- EMAIL & PASSWORD --}}
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label>Email</label>
                                        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                                    </div>
                                    {{-- NEW PASSWORD --}}
                                    <div class="form-group col-md-4">
                                        <label>New Password</label>
                                        <div class="input-group">
                                            <input type="password" id="password" name="password" class="form-control">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePassword('password', this)">
                                                    👁
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    {{-- CONFIRM PASSWORD --}}
                                    <div class="form-group col-md-4">
                                        <label>Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" id="confirm_password" name="password_confirmation"
                                                class="form-control" oninput="checkPasswordMatch();">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary"
                                                    onclick="togglePassword('confirm_password', this)">
                                                    👁
                                                </button>
                                            </div>
                                        </div>

                                        <div id="passwordMatchMessage" class="text-danger" style="display:none;">
                                            Passwords do not match.
                                        </div>
                                    </div>
                                </div>

                                {{-- DEPARTMENT --}}
                                <div class="form-row">
                                    <div class="form-group col-md-8">

                                        <label>Department</label>

                                        <select class="form-control" name="department" id="departmentSelect">
                                            <option value="" disabled>Select Office</option>
                                            @foreach ($offices as $office)
                                                <option value="{{ $office->office_name }}"
                                                    {{ $user->department == $office->office_name ? 'selected' : '' }}>
                                                    {{ $office->office_name }}
                                                </option>
                                            @endforeach
                                        </select>


                                    </div>

                                    {{-- E-SIGNATURE --}}
                                    <div class="form-group col-md-4">
                                        <label>Electronic Signature</label>
                                        <input type="file" class="form-control mb-2" name="esig_file"
                                            accept="image/png,image/jpeg,application/pdf">


                                        @if ($userEsig && $userEsig->esig_file)
                                            <p class="mb-1">Current E‑Signature:</p>

                                            @if (($userEsig->esig_ext ?? '') === 'pdf')
                                                <a class="btn btn-sm btn-outline-primary"
                                                    href="{{ route('esig.show', $user->id) }}" target="_blank">
                                                    View E‑Signature (PDF)
                                                </a>
                                            @else
                                                <img src="{{ route('esig.show', $user->id) }}" class="img-thumbnail"
                                                    style="max-height:120px;">
                                            @endif
                                        @endif

                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Update Details
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
    @if (auth()->user()->dpa === null || auth()->user()->dpa == 0)
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                $('#dataPrivacy').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#dataPrivacy').modal('show');
            });
        </script>
    @endif


    <script>
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const message = document.getElementById('passwordMatchMessage');

            if (password !== confirmPassword) {
                message.style.display = 'block';
                message.textContent = 'Passwords do not match.';
            } else {
                message.style.display = 'none';
            }
        }
    </script>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);

            if (input.type === "password") {
                input.type = "text";
                button.innerText = "🙈";
            } else {
                input.type = "password";
                button.innerText = "👁";
            }
        }
    </script>


    <!-- Page Specific JS File -->
@endsection
