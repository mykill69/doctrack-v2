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
                               <h4>Reroute To Personnel/s</h4>
                           </div>


                           <form action="{{ route('updateRerouteEntry', $slipEntry->id) }}" method="POST"
                               enctype="multipart/form-data">
                               @csrf
                               @method('PUT')

                               <div class="card-body text-bold">

                                   {{-- TRANSACTION TYPE --}}

                                   <input type="hidden" name="transaction_type" class="form-control"
                                       value="{{ old('source', $slipEntry->transaction_type) }}"
                                       placeholder="Control Number" readonly>

                                   <input type="hidden" name="validity" value="{{ $slipEntry->validity }}">


                                   {{-- CONTROL NUMBER --}}
                                   <div class="col-md-6">
                                       {{-- <label>Control Number</label> --}}
                                       <input type="hidden" name="op_ctrl" class="form-control"
                                           value="{{ old('op_ctrl', $slipEntry->rslip_id) }}" readonly>
                                   </div>

                                   {{-- DATE RECEIVED --}}
                                   <div class="col-md-6">
                                       {{-- <label>Date Received</label> --}}
                                       <input type="hidden" name="date_received" class="form-control"
                                           value="{{ old('date_received', $slipEntry->date_received) }}" readonly>
                                   </div>



                                   {{-- SOURCE --}}
                                   <div class="form-group">
                                       <label>Source</label>
                                       <input type="text" name="source" class="form-control"
                                           value="{{ old('source', $slipEntry->source) }}" readonly>
                                   </div>

                                   {{-- SUBJECT --}}
                                   <div class="form-group">
                                       <label>Subject</label>
                                       <textarea name="subject" class="form-control" rows="2" readonly>{{ old('subject', $slipEntry->subject) }}</textarea>
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
                                       <label>Originally Routed To</label>
                                       <input type="text" class="form-control"
                                           value="{{ implode(', ', $currentNames) ?: '—' }}" readonly>
                                   </div>

                                   {{-- <div class="col-md-6">
                                       <label>Reassigned To</label>
                                       <input type="text" class="form-control bg-success text-white text-bold"
                                           value="{{ implode(', ', $reassignedUsersFull) ?: '—' }}" readonly>
                                   </div> --}}

                                   <div class="form-group">
                                       <div class="row">
                                           {{-- Reassigned Personnel --}}
                                           <div class="col-md-6">
                                               <label>Reassigned To</label>
                                               <input type="text" class="form-control bg-success text-white text-bold"
                                                   value="{{ implode(', ', $reassignedUsers) ?: '—' }}" readonly>
                                           </div>

                                           <div class="col-md-6">
                                               <label>Reassigned By</label>
                                               <input type="text" class="form-control"
                                                   value="{{ implode(', ', $reassignedUsersCreators) ?: '—' }}" readonly>
                                           </div>
                                       </div>
                                   </div>

                                   <div class="form-group">
                                       <label>Re-assigned Action Unit</label>
                                       <select class="form-control select2" name="routed_users[]" multiple required>
                                           <option disabled>— Select by Group —</option>
                                           @foreach ($groups as $group)
                                               <option value="group:{{ $group->id }}">{{ $group->group_name }}</option>
                                           @endforeach
                                           <option disabled>──────────</option>
                                           <option disabled>— Select by Individual User —</option>
                                           @foreach ($users as $user)
                                               <option value="{{ $user->id }}">{{ $user->fname }}
                                                   {{ $user->lname }}</option>
                                           @endforeach
                                       </select>
                                   </div>

                                   <input type="hidden" name="file" class="form-control"
                                       value="{{ old('source', $slipEntry->file) }}" readonly>



                                   <input type="hidden" name="trans_remarks" class="form-control"
                                       value="{{ old('trans_remarks', $slipEntry->trans_remarks) }}" readonly>



                                   <div class="card-footer text-right">
                                       <button class="btn btn-primary">
                                           <i class="fas fa-save"></i> Update Reroute
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
