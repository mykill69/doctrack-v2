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

                           <div class="card-header d-flex justify-content-between align-items-center">
                               {{-- Left side --}}
                               <h4 class="mb-0">LIST OF REGISTERED USERS</h4>

                               {{-- Right side --}}
                               <a href="#" class="btn btn-sm btn-success" data-toggle="modal"
                                   data-target="#addUserModal">
                                   <i class="fas fa-user-plus mr-1"></i> Add New User
                               </a>

                           </div>


                           <div class="card-body">
                               <div class="table-responsive">
                                   <table class="table table-striped text-small" id="table-2">
                                       <thead>
                                           <tr>
                                               <th>NO.</th>
                                               <th>FULLNAME</th>
                                               <th>EMAIL</th>
                                               <th>OFFICE</th>
                                               <th>ROLE</th>
                                               <th>GROUP</th>
                                               {{-- <th>DATE CREATED</th> --}}
                                               <th>ACTION</th>
                                           </tr>
                                       </thead>

                                       <tbody>
                                           @forelse ($users as $index => $user)
                                               <tr>
                                                   <td>{{ $index + 1 }}</td>

                                                   <td>
                                                       {{ $user->fname }}
                                                       {{ $user->mname }}
                                                       {{ $user->lname }}
                                                   </td>

                                                   <td>{{ $user->email }}</td>

                                                   <td>{{ $user->department }}</td>

                                                   <td><span class="badge badge-primary">
                                                           {{ strtoupper($user->role) }}
                                                       </span></td>

                                                   <td>
                                                       @if ($user->groups && $user->groups->isNotEmpty())
                                                           @foreach ($user->groups as $group)
                                                               <span class="badge badge-secondary" style="font-size: 9px;">
                                                                   {{ $group->group_name }}
                                                               </span>
                                                           @endforeach
                                                       @else
                                                           <span class="badge badge-danger" style="font-size: 9px;">No
                                                               Group</span>
                                                       @endif
                                                   </td>

                                                   {{-- <td>
                                                       {{ $user->created_at ? $user->created_at->format('F d, Y') : '—' }}
                                                   </td> --}}

                                                   <td>
                                                       <div class="dropdown">
                                                           <button class="btn btn-sm btn-info dropdown-toggle"
                                                               type="button" data-toggle="dropdown" aria-haspopup="true"
                                                               aria-expanded="false">

                                                           </button>

                                                           <div class="dropdown-menu dropdown-menu-right">
                                                               <a class="dropdown-item" href="#">
                                                                   <i class="fas fa-eye mr-1"></i> View
                                                               </a>

                                                               <a class="dropdown-item edit-user-btn" href="#"
                                                                   data-id="{{ $user->id }}"
                                                                   data-fname="{{ $user->fname }}"
                                                                   data-mname="{{ $user->mname }}"
                                                                   data-lname="{{ $user->lname }}"
                                                                   data-email="{{ $user->email }}"
                                                                   data-department="{{ $user->department }}"
                                                                   data-role="{{ $user->role }}">
                                                                   <i class="fas fa-edit mr-1"></i> Edit
                                                               </a>

                                                           </div>
                                                       </div>
                                                   </td>

                                               </tr>
                                           @empty
                                               <tr>
                                                   <td colspan="8" class="text-center text-muted">
                                                       No users found
                                                   </td>
                                               </tr>
                                           @endforelse
                                       </tbody>



                                   </table>
                               </div>
                           </div>
                       </div>
                   </div>


               </div>
           </section>
       </div>


       @include('modal.addUsers')
       @include('modal.editUser')






       <!-- Page Specific JS File -->
   @endsection
