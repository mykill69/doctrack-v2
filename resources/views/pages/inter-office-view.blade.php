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

                           <div class="card-header d-flex justify-content-between">
                               <h4>TRACKING SLIP: {{ $interOffice->track_slip }}</h4>

                               @if (auth()->id() == $interOffice->creator_id)
                                   <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEntryModal">
                                       <i class="fas fa-plus"></i> Add Entry
                                   </button>
                               @endif
                           </div>

                           <div class="card-body bg-light" style="min-height: 700px;padding-top: 5%;">

                               @foreach ($logs as $log)
                                   <div class="row justify-content-center mb-4">
                                       <div class="col-12 col-md-9">
                                           <div class="card shadow-sm border">

                                               @php
                                                   $borderColor = match ($log->track_status) {
                                                       1 => 'orange', // Pending
                                                       3 => 'green', // Acknowledged
                                                       2 => 'red', // Returned
                                                       4 => 'red', // Returned with comments
                                                       default => '#ddd',
                                                   };

                                                   $shadowColor = match ($log->track_status) {
                                                       1 => 'rgba(255,165,0,0.4)', // orange shadow
                                                       3 => 'rgba(0,128,0,0.4)', // green shadow
                                                       2 => 'rgba(255,0,0,0.4)', // red shadow
                                                       4 => 'rgba(255,0,0,0.4)',
                                                       default => 'rgba(0,0,0,0.1)',
                                                   };
                                               @endphp

                                               <div class="card-body rounded shadow-sm"
                                                   style="border: 1px solid {{ $borderColor }}; box-shadow: 0 4px 12px {{ $shadowColor }};">

                                                   <div
                                                       class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                                       <strong class="text-primary" style="font-size: 28px;">
                                                           {{ optional($log->user)->fname }}
                                                           {{ optional($log->user)->lname }}
                                                       </strong>

                                                       <small class="text-muted">
                                                           {{ $log->created_at->format('M d, Y h:i A') }}
                                                       </small>
                                                   </div>

                                                   <p class="mb-3" style="font-size: 18px;text-align: left;">
                                                       {{ $interOffice->subject }}
                                                   </p>

                                                   <div
                                                       class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light border rounded">
                                                       <div>
                                                           @if ($log->interOffice && $log->interOffice->file)
                                                               <a href="{{ asset('storage/' . $log->interOffice->file) }}"
                                                                   target="_blank" style="text-decoration: none;">
                                                                   {{ basename($log->interOffice->file) }}
                                                               </a>
                                                           @else
                                                               <em class="text-muted">No file attached</em>
                                                           @endif
                                                       </div>

                                                       <form method="POST" enctype="multipart/form-data">
                                                           @csrf
                                                           <input type="file" name="file" hidden
                                                               onchange="this.form.submit()">
                                                           <button type="button" class="btn btn-sm btn-info"
                                                               onclick="this.previousElementSibling.click()">
                                                               Upload
                                                           </button>
                                                       </form>
                                                   </div>

                                                   <div class="d-flex justify-content-between align-items-center mt-3">

                                                       {{-- LEFT SIDE : REMARKS --}}
                                                       <div>
                                                           @if (!empty($log->remarks))
                                                               <small class="text-muted">
                                                                   <strong>Remarks:</strong>
                                                                   {{ $log->remarks }}
                                                               </small>
                                                           @endif
                                                       </div>

                                                       {{-- RIGHT SIDE : ACTION BUTTONS --}}
                                                       <div>
                                                           @php
                                                               $isUser = $log->user_id === auth()->id();
                                                               $isCreator =
                                                                   $log->interOffice->creator_id === auth()->id();
                                                           @endphp

                                                           @if ($isUser || $isCreator)
                                                               @if ($log->track_status == 1)
                                                                   {{-- Pending --}}
                                                                   <div class="dropdown">
                                                                       <button
                                                                           class="btn btn-sm btn-secondary dropdown-toggle"
                                                                           type="button" data-bs-toggle="dropdown"
                                                                           {{ $isCreator ? 'disabled' : '' }}>
                                                                           Pending. Waiting for user's action.
                                                                       </button>

                                                                       <ul class="dropdown-menu">
                                                                           <li>
                                                                               <a class="dropdown-item" href="#"
                                                                                   onclick="submitStatus({{ $loop->index }}, 3)"
                                                                                   {{ $isCreator ? 'class=disabled' : '' }}>
                                                                                   <i class="fas fa-handshake mr-1"></i>
                                                                                   Acknowledge
                                                                               </a>
                                                                           </li>

                                                                           <li>
                                                                               <a class="dropdown-item" href="#"
                                                                                   onclick="returnWithRemarks({{ $loop->index }}, {{ $log->id }})"
                                                                                   {{ $isCreator ? 'class=disabled' : '' }}>
                                                                                   <i class="fas fa-undo mr-1"></i>
                                                                                   Return with comments
                                                                               </a>
                                                                           </li>
                                                                       </ul>
                                                                   </div>
                                                               @elseif ($log->track_status == 3)
                                                                   <button class="btn btn-sm btn-success"
                                                                       disabled>Acknowledged</button>
                                                               @elseif ($log->track_status == 2)
                                                                   <button class="btn btn-sm btn-danger"
                                                                       {{ $isCreator ? 'disabled' : '' }}>
                                                                       Returned
                                                                   </button>
                                                               @elseif ($log->track_status == 4)
                                                                   <button class="btn btn-sm btn-danger"
                                                                       {{ $isCreator ? 'disabled' : '' }}>
                                                                       Returned with comments
                                                                   </button>
                                                               @endif
                                                           @endif
                                                       </div>

                                                   </div>
                                               </div>
                                               <!-- Hidden form for each log -->
                                               <form id="statusForm{{ $loop->index }}"
                                                   action="{{ route('interOffice.updateStatus', $log->id) }}"
                                                   method="POST" style="display:none;">
                                                   @csrf
                                                   @method('PUT')
                                                   <input type="hidden" name="track_status"
                                                       id="doctrackStatInput{{ $loop->index }}">
                                                   <input type="hidden" name="remarks"
                                                       id="remarksInput{{ $loop->index }}">
                                               </form>
                                           </div>
                                       </div>
                                   </div>
                               @endforeach

                           </div>
                       </div>
                   </div>
               </div>
           </section>
       </div>

       @include('modal.newEntryInterOffice')


       <!-- SweetAlert2 JS -->
       <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

       <!-- Bootstrap JS (MUST be last) -->
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

       <script>
           function submitStatus(index, status) {
               document.getElementById(`doctrackStatInput${index}`).value = status;
               document.getElementById(`remarksInput${index}`).value = ''; // clear remarks if any
               document.getElementById(`statusForm${index}`).submit();
           }

           function returnWithRemarks(index, logId) {
               Swal.fire({
                   title: 'Return with comments',
                   input: 'textarea',
                   inputPlaceholder: 'Enter remarks...',
                   showCancelButton: true,
                   confirmButtonText: 'Submit',
                   preConfirm: (remarks) => {
                       if (!remarks) Swal.showValidationMessage('Remarks are required');
                       return remarks;
                   }
               }).then((result) => {
                   if (result.isConfirmed) {
                       document.getElementById(`doctrackStatInput${index}`).value = 2; // returned
                       document.getElementById(`remarksInput${index}`).value = result.value;
                       document.getElementById(`statusForm${index}`).submit();
                   }
               });
           }
       </script>

       <!-- Page Specific JS File -->
   @endsection
