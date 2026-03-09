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
                               <h4>INTER - OFFICE DOCUMENTS</h4>
                           </div>

                           <div class="card-body">
                               <div class="table-responsive">
                                   <table class="table table-striped text-small" id="table-2">
                                       <thead>
                                           <tr>
                                               <th hidden></th>
                                               <th>DATE RECEIVED</th>
                                               <th>SOURCE</th>
                                               <th>SUBJECT MATTER</th>
                                               <th>ACTION TAKEN</th>
                                               <th>REMARKS</th>
                                               <th>STATUS</th>
                                               <th>TRACKING CODE</th>
                                               <th>DURATION</th>
                                               <th>ACTION</th>

                                           </tr>
                                       </thead>

                                       <tbody>
                                           @foreach ($interOffices as $io)
                                               <tr>
                                                   <td hidden></td>
                                                   <td>
                                                       {{ $io->created_at->format('M j, Y') }}
                                                   </td>

                                                   <!-- SOURCE: creator name -->
                                                   <td>{{ optional($io->creator)->fname }}
                                                       {{ optional($io->creator)->lname }}</td>

                                                   <td>{{ $io->subject }}</td>

                                                   <!-- ACTION TAKEN: show number of logs completed -->
                                                   <td>
                                                       @php
                                                           $assigned = $io->assignedUsers(); // collection of assigned users
                                                           $isCreator = auth()->id() === $io->creator_id;
                                                       @endphp

                                                       @if ($isCreator)
                                                           {{-- Creator sees ALL assigned users --}}
                                                           @foreach ($assigned as $user)
                                                               {{ $user->fname }} {{ $user->lname }}@if (!$loop->last)
                                                                   ,
                                                               @endif
                                                           @endforeach
                                                       @else
                                                           {{-- Assigned user sees ONLY their own name --}}
                                                           @foreach ($assigned as $user)
                                                               @if ($user->id === auth()->id())
                                                                   {{ $user->fname }} {{ $user->lname }}
                                                                   @break
                                                               @endif
                                                           @endforeach
                                                       @endif
                                                   </td>

                                                   <td>
                                                       @foreach ($io->logs as $log)
                                                           @if (!empty($log->remarks))
                                                               <div>
                                                                   <strong>{{ $log->user->fname ?? '' }}
                                                                       {{ $log->user->lname ?? '' }}</strong>:
                                                                   {{ $log->remarks }}
                                                               </div>
                                                           @endif
                                                       @endforeach
                                                   </td>

                                                   {{-- <td>
                                                       @if ($io->track_status == 1)
                                                           <span class="badge badge-warning">Pending</span>
                                                       @elseif($io->track_status == 2)
                                                           <span class="badge badge-info">In Progress</span>
                                                       @elseif($io->track_status == 3)
                                                           <span class="badge badge-success">Acknowledged</span>
                                                       @elseif($io->track_status == 4)
                                                           <span class="badge badge-danger">Returned</span>
                                                       @endif
                                                   </td> --}}

                                                   <td>
                                                       @php
                                                           $currentUserId = auth()->id();
                                                           $isCreator = $currentUserId === $io->creator_id;
                                                           $statusLabel = '';

                                                           // Map statuses to badge colors
                                                           $statusColors = [
                                                               1 => 'badge-warning', // Pending
                                                               3 => 'badge-success', // Acknowledged
                                                               2 => 'badge-danger', // Returned
                                                               4 => 'badge-danger', // Returned with comments
                                                           ];

                                                           if ($isCreator) {
                                                               // Creator: check all logs for this track_slip
                                                               $logs = $io
                                                                   ->logs()
                                                                   ->where('track_slip', $io->track_slip)
                                                                   ->get();

                                                               if (
                                                                   $logs->isNotEmpty() &&
                                                                   $logs->every(fn($log) => $log->track_status == 3)
                                                               ) {
                                                                   $statusLabel =
                                                                       '<span class="badge ' .
                                                                       $statusColors[3] .
                                                                       '">Acknowledged</span>';
                                                               } else {
                                                                   $statusLabel =
                                                                       '<span class="badge ' .
                                                                       $statusColors[1] .
                                                                       '">Pending</span>';
                                                               }
                                                           } else {
                                                               // Non-creator: get the log for the current user only
                                                               $userLog = $io
                                                                   ->logs()
                                                                   ->where('track_slip', $io->track_slip)
                                                                   ->where('user_id', $currentUserId)
                                                                   ->latest('updated_at')
                                                                   ->first();

                                                               if ($userLog) {
                                                                   $colorClass =
                                                                       $statusColors[$userLog->track_status] ??
                                                                       'badge-secondary';
                                                                   $labelText = match ($userLog->track_status) {
                                                                       1 => 'Pending',
                                                                       2 => 'Returned',
                                                                       3 => 'Acknowledged',
                                                                       4 => 'Returned with comments',
                                                                       default => 'Unknown',
                                                                   };
                                                                   $statusLabel =
                                                                       '<span class="badge ' .
                                                                       $colorClass .
                                                                       '">' .
                                                                       $labelText .
                                                                       '</span>';
                                                               } else {
                                                                   $statusLabel =
                                                                       '<span class="badge badge-warning">Pending</span>';
                                                               }
                                                           }
                                                       @endphp

                                                       {!! $statusLabel !!}
                                                   </td>

                                                   <td> <a href="{{ route('viewInterOffice', $io->track_slip) }}"
                                                           class="btn btn-sm">{{ $io->track_slip }}</a> </td>
                                                   <!-- DURATION: time from creation to last log update -->
                                                   <td>
                                                       @php
                                                           // Get the latest updated_at for this track_slip
                                                           $lastLog = $io
                                                               ->logs()
                                                               ->where('track_slip', $io->track_slip)
                                                               ->latest('updated_at')
                                                               ->first();

                                                           $duration = '';

                                                           if ($lastLog) {
                                                               $diffInSeconds = $io->created_at->diffInSeconds(
                                                                   $lastLog->updated_at,
                                                               );

                                                               // Only display if more than 12 seconds
                                                               if ($diffInSeconds > 12) {
                                                                   $duration = $io->created_at->diffForHumans(
                                                                       $lastLog->updated_at,
                                                                       [
                                                                           'short' => true, // optional, for short format like "1h ago"
                                                                       ],
                                                                   );
                                                               }
                                                           }
                                                       @endphp

                                                       {{ $duration }}
                                                   </td>

                                                   <td>
                                                       <a href="{{ route('viewInterOffice', $io->track_slip) }}"
                                                           class="btn btn-sm btn-primary">
                                                           View
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
