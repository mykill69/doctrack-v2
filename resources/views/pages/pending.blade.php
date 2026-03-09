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
                               <h4>PENDING DOCUMENTS</h4>
                           </div>

                           <div class="card-body">
                               <div class="table-responsive">
                                   <table class="table table-striped text-small" id="table-2">
                                       <thead>
                                           <tr>

                                               <th>CTRL #</th>
                                               <th>DATE RECEIVED</th>
                                               <th>SOURCE</th>
                                               <th>SUBJECT MATTER</th>
                                               <th>ACTION UNIT</th>
                                               <th>RECEIVED BY / DATE</th>
                                               <th>ACTION TAKEN</th>
                                               <th>DATE RELEASED</th>
                                               <th>REMARKS</th>
                                               <th>FILE NAME</th>
                                               <th>ACTION</th>

                                           </tr>
                                       </thead>

                                       <tbody>
                                           @foreach ($pendingDocs as $slipId => $logs)
                                               @php
                                                   $firstLog = $logs->first();
                                                   $rUsersNames = $logs
                                                       ->flatMap(function ($log) {
                                                           return explode(',', $log->r_users);
                                                       })
                                                       ->unique()
                                                       ->map(function ($id) {
                                                           $user = \App\Models\User::find($id);
                                                           return $user ? $user->fname . ' ' . $user->lname : '—';
                                                       })
                                                       ->implode(', ');
                                               @endphp
                                               <tr>
                                                   {{-- CTRL # --}}
                                                   <td>
                                                       <a href="{{ route('viewRouteSlip', $firstLog->rslip_id) }}"
                                                           target="_blank" class="text-primary font-weight-bold">
                                                           {{ $firstLog->rslip_id }}
                                                       </a>
                                                   </td>

                                                   {{-- DATE RECEIVED --}}
                                                   <td>{{ \Carbon\Carbon::parse($firstLog->date_received)->format('F j, Y') }}
                                                   </td>

                                                   {{-- SOURCE --}}
                                                   <td>{{ $firstLog->source }}</td>

                                                   {{-- SUBJECT MATTER --}}
                                                   <td>{{ $firstLog->subject }}</td>

                                                   {{-- ACTION UNIT --}}
                                                   <td>Dr. Aladino C. Moraca</td>

                                                   {{-- RECEIVED BY / DATE --}}
                                                   <td>
                                                       <small>{{ $firstLog->created_at->format('F j, Y') }}</small>
                                                   </td>

                                                   {{-- ACTION TAKEN (merged r_users) --}}
                                                   <td>{{ $rUsersNames }}</td>

                                                   {{-- DATE RELEASED --}}
                                                   <td>{{ $firstLog->created_at ? $firstLog->created_at->format('m-d-Y h:i:s A') : '—' }}
                                                   </td>

                                                   {{-- REMARKS --}}
                                                   <td>{{ $firstLog->trans_remarks ?? 'N/A' }}</td>

                                                   {{-- FILE NAME --}}
                                                   <td>
                                                       @if ($firstLog->file)
                                                           @php
                                                               $displayName = preg_replace(
                                                                   '/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_/',
                                                                   '',
                                                                   $firstLog->file,
                                                               );
                                                           @endphp
                                                           <a href="{{ asset('storage/documents/' . $firstLog->file) }}"
                                                               target="_blank">
                                                               {{ $displayName }}
                                                           </a>
                                                       @else
                                                           N/A
                                                       @endif
                                                   </td>

                                                   <td>
                                                       <a href="{{ route('routingTimeline', ['id' => $logs->first()->id]) }}?slip_id={{ $logs->first()->slip_id }}"
                                                           class="btn btn-sm btn-primary">
                                                           <i class="fas fa-eye"></i>
                                                       </a>
                                                   </td>

                                               </tr>
                                           @endforeach
                                       </tbody>


                                   </table>
                               </div>
                           </div>
                       </div>
                   </div>


               </div>
           </section>
       </div>


       <!-- Page Specific JS File -->
   @endsection
