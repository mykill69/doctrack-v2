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
                               <h4>Direct To Personnel Distribution List </h4>
                           </div>

                           <div class="card-body">
                               <div class="table-responsive">
                                   <table class="table table-striped text-small" id="table-2">
                                       <thead>
                                           <tr>
                                               <th>CTRL #</th>
                                               <th>SOURCE</th>
                                               <th>SUBJECT MATTER</th>
                                               <th>ACTION UNIT</th>
                                               <th>DATE RELEASED</th>
                                               <th>ACTION</th>
                                           </tr>
                                       </thead>
                                       <tbody>
                                           @forelse ($slip as $row)
                                               <tr>
                                                   <td>{{ $row->rslip_id ?? '—' }}</td>
                                                   <td>{{ $row->source ?? '—' }}</td>
                                                   <td>{{ $row->subject ?? '—' }}</td>
                                                   <td>
                                                       @php
                                                           $userIds = $row->routed_users
                                                               ? explode(',', $row->routed_users)
                                                               : [];

                                                           $routedUsers = $users->whereIn('id', $userIds);
                                                       @endphp

                                                       @if ($routedUsers->isNotEmpty())
                                                           {{ $routedUsers->map(fn($u) => $u->fname . ' ' . $u->lname)->implode(', ') }}
                                                       @else
                                                           —
                                                       @endif
                                                   </td>
                                                   <td>
                                                       {{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('F j, Y') : '—' }}
                                                   </td>
                                                   <td>
                                                       <a href="{{ route('distListDrirectPdf', ['id' => $row->id]) }}"
                                                           class="btn btn-info" target="_blank">
                                                           <i class="fas fa-file-pdf"></i> View PDF
                                                       </a>
                                                   </td>
                                               </tr>
                                           @empty
                                               <tr>
                                                   <td colspan="6" class="text-center text-muted">
                                                       No records found.
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


       <!-- Page Specific JS File -->
   @endsection
