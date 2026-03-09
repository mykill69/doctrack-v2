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
                               <h4>Edit Routing Slip</h4>
                           </div>


                           <form action="{{ route('updateRoutingEntry', $slipEntry->id) }}" method="POST"
                               enctype="multipart/form-data">
                               @csrf
                               @method('PUT')

                               <div class="card-body text-bold">

                                   {{-- TRANSACTION TYPE --}}

                                   <input type="hidden" name="source" class="form-control"
                                       value="{{ old('source', $slipEntry->transaction_type) }}"
                                       placeholder="Control Number" readonly>

                                   <input type="hidden" name="validity" value="{{ $slipEntry->validity }}">

                                   <div class="form-group">
                                       <div class="row">

                                           {{-- CONTROL NUMBER --}}
                                           <div class="col-md-6">
                                               <label>Control Number</label>
                                               <input type="text" name="op_ctrl" class="form-control"
                                                   value="{{ old('op_ctrl', $slipEntry->rslip_id) }}" readonly>
                                           </div>

                                           {{-- DATE RECEIVED --}}
                                           <div class="col-md-6">
                                               <label>Date Received</label>
                                               <input type="date" name="date_received" class="form-control"
                                                   value="{{ old('date_received', $slipEntry->date_received) }}" readonly>
                                           </div>

                                       </div>
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

                                   <div class="form-group">
                                       <label>Action Unit</label>
                                       <input type="hidden" name="set_users_to" value="{{ $slipEntry->set_users_to }}">
                                       <input type="text" class="form-control"
                                           value="{{ old('set_users_to', implode(', ', $assignedNames)) }}" readonly>
                                   </div>
                                   <div class="form-group">
                                       <label>Name of Personnel / Group Name</label>

                                       <select class="form-control select2" name="routed_users[]" multiple required>

                                           <option disabled>— Select by Group —</option>
                                           @foreach ($groups as $group)
                                               <option value="group:{{ $group->id }}">
                                                   {{ $group->group_name }}
                                               </option>
                                           @endforeach

                                           <option disabled>──────────</option>
                                           <option disabled>— Select by Individual User —</option>

                                           @foreach ($users as $user)
                                               <option value="{{ $user->id }}">
                                                   {{ $user->fname }} {{ $user->lname }}
                                               </option>
                                           @endforeach
                                       </select>
                                   </div>

                                   <div class="form-group">
                                       <div class="row">
                                           <div class="col-md-6">
                                               <label>File</label>
                                               <input type="text" name="file" class="form-control"
                                                   value="{{ old('source', $slipEntry->file) }}" readonly>
                                           </div>

                                           <div class="col-md-6">
                                               <label>Transaction Remarks</label>
                                               <input type="text" name="trans_remarks" class="form-control"
                                                   value="{{ old('trans_remarks', $slipEntry->trans_remarks) }}" readonly>

                                           </div>
                                       </div>

                                       <div class="card-footer text-right">

                                           <!-- Update button (default form action) -->
                                           <button type="submit" class="btn btn-primary">
                                               <i class="fas fa-save"></i> Update
                                           </button> &nbsp;

                                           <!-- Route Back to President button -->
                                           <button type="button" class="btn btn-danger" id="routeBackBtn">
                                               <i class="fas fa-undo-alt mr-1"></i> Route back to President
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


       <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       <script>
           document.getElementById('routeBackBtn').addEventListener('click', function(e) {
               Swal.fire({
                   title: 'Are you sure?',
                   text: "You are about to route this back to the President!",
                   icon: 'warning',
                   showCancelButton: true,
                   confirmButtonColor: '#d33',
                   cancelButtonColor: '#3085d6',
                   confirmButtonText: 'Yes, route it back!',
                   cancelButtonText: 'Cancel'
               }).then((result) => {
                   if (result.isConfirmed) {
                       // Create a temporary form to submit
                       let form = document.createElement('form');
                       form.method = 'POST';
                       form.action = "{{ route('routeBackToPresident', $slipEntry->id) }}";

                       // Add CSRF token
                       let csrfInput = document.createElement('input');
                       csrfInput.type = 'hidden';
                       csrfInput.name = '_token';
                       csrfInput.value = "{{ csrf_token() }}";
                       form.appendChild(csrfInput);

                       // Add method PUT
                       let methodInput = document.createElement('input');
                       methodInput.type = 'hidden';
                       methodInput.name = '_method';
                       methodInput.value = 'PUT';
                       form.appendChild(methodInput);

                       document.body.appendChild(form);
                       form.submit();
                   }
               });
           });
       </script>


       <!-- Page Specific JS File -->
   @endsection
