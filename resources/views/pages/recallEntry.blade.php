   @extends('home.main')
   @section('body')
       <div class="main-content">
           <section class="section">
               <div class="row row-deck">
                   <div class="col-12">
                       <div class="card position-relative">
                           <!-- Logo centered on top of the card header -->
                           <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                               class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                           <div class="card-header">
                               <!-- Add padding-top to account for the logo -->
                               <h4>Recall Transaction/s</h4>
                           </div>


                           <form action="{{ route('updateRecall', $slipEntry->id) }}" method="POST"
                               enctype="multipart/form-data">
                               @csrf
                               @method('PUT')

                               <div class="card-body text-bold">
                                   <div class="form-group">
                                       <label>File</label>

                                       <input type="text" class="form-control mb-2" value="{{ $slipEntry->file }}"
                                           readonly>

                                       <input type="file" class="form-control" name="file">

                                       <small class="text-muted">
                                           Leave blank if you do not want to change the file.
                                       </small>
                                   </div>

                                   {{-- ACTION UNIT --}}
                                   @php
                                       $assignedIds = array_filter(
                                           array_map('trim', explode(',', $slipEntry->set_users_to ?? '')),
                                       );
                                       $assignedNames = [];
                                       foreach ($users as $user) {
                                           if (
                                               in_array((string) $user->id, $assignedIds) ||
                                               in_array($user->id, $assignedIds)
                                           ) {
                                               $assignedNames[] = $user->fname . ' ' . $user->lname;
                                           }
                                       }
                                   @endphp


                                   @php
                                       $currentRoutedRaw = $slipEntry->routed_users ?? '';

                                       // Convert CSV to array
                                       $currentRouted = array_filter(
                                           array_map('trim', explode(',', $currentRoutedRaw)),
                                       );

                                       $currentNames = [];

                                       foreach ($currentRouted as $item) {
                                           // GROUP
                                           if (str_starts_with($item, 'group:')) {
                                               $groupId = str_replace('group:', '', $item);
                                               $group = $groups->firstWhere('id', $groupId);

                                               if ($group) {
                                                   $currentNames[] = 'Group: ' . $group->group_name;
                                               }
                                           }
                                           // INDIVIDUAL USER
                                           else {
                                               $user = $users->firstWhere('id', $item);
                                               if ($user) {
                                                   $currentNames[] = $user->fname . ' ' . $user->lname;
                                               }
                                           }
                                       }
                                   @endphp

                                   <div class="form-group">
                                       <label>Currently Routed To</label>
                                       <input type="text" class="form-control bg-info text-white text-bold"
                                           value="{{ implode(', ', $routedUsers) ?: '—' }}" readonly>
                                   </div>

                                   <div class="form-group">
                                       <label>Recall From (Personnel)</label>

                                       <select class="form-control select2" name="routed_users[]" multiple>
                                           {{-- GROUPS --}}
                                           <option disabled>— Select by Group —</option>
                                           @foreach ($groups as $group)
                                               <option value="group:{{ $group->id }}"
                                                   {{ in_array('group:' . $group->id, $currentRouted) ? 'selected' : '' }}>
                                                   Group: {{ $group->group_name }}
                                               </option>
                                           @endforeach

                                           <option disabled>──────────</option>

                                           {{-- USERS --}}
                                           <option disabled>— Select by Individual User —</option>
                                           @foreach ($users as $user)
                                               <option value="{{ $user->id }}"
                                                   {{ in_array((string) $user->id, $currentRouted) ? 'selected' : '' }}>
                                                   {{ $user->fname }} {{ $user->lname }}
                                               </option>
                                           @endforeach
                                       </select>

                                       <small class="text-muted">
                                           Currently routed users are pre-selected.
                                           Adding more will recall additional personnel.
                                       </small>
                                   </div>



                                   <div class="card-footer text-right">
                                       <button class="btn btn-primary">
                                           <i class="fas fa-save"></i> Submit Recall
                                       </button>

                                       <a href="{{ route('routing') }}" class="btn btn-warning">
                                           <i class="fas fa-times"></i> Back
                                       </a>
                                   </div>
                           </form>
                       </div>


                   </div>
               </div>
           </section>
       </div>


       <!-- Page Specific JS File -->
   @endsection
