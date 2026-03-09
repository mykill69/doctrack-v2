   @extends('home.main')
   @section('body')
       <div class="main-content">
           <section class="section">
               <div class="row row-deck">
                   <div class="col-12">
                       <div class="card position-relative text-center">
                           <!-- Logo centered on top of the card header -->
                           <img src="template/img/cpsu_logo.png" alt="cpsu logo" width="110" height="110"
                               class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                           <div class="card-header">
                               <!-- Add padding-top to account for the logo -->
                               <h4>Routed To President's Office</h4>
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
                                               <th>FILE NAME</th>
                                               <th>TRANSACTION REMARKS</th>
                                               <th>OTHER REMARKS</th>
                                               <th>ACTION TAKEN</th>
                                               <th>STATUS</th>
                                               <th>RECEIVED BY / DATE</th>
                                               <th>ACTION</th>
                                           </tr>
                                       </thead>

                                       <tbody>
                                           @foreach ($routedDocs as $doc)
                                               <tr>
                                                   <td>{{ $doc->rslip_id }}</td>
                                                   <td>{{ \Carbon\Carbon::parse($doc->date_received)->format('F j, Y') }}
                                                   </td>
                                                   <td>{{ $doc->source }}</td>
                                                   <td>{{ $doc->subject }}</td>
                                                   <td>
                                                       @if ($doc->file)
                                                           @php
                                                               // Remove timestamp prefix (e.g., 2026-01-21_14-35-12_)
                                                               $displayName = preg_replace(
                                                                   '/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_/',
                                                                   '',
                                                                   $doc->file,
                                                               );
                                                           @endphp
                                                           <a href="{{ asset('storage/documents/' . $doc->file) }}"
                                                               target="_blank">
                                                               {{ $displayName }}
                                                           </a>
                                                       @else
                                                           N/A
                                                       @endif
                                                   </td>

                                                   <td>{{ $doc->trans_remarks ?? 'N/A' }}</td>
                                                   <td>{{ $doc->other_remarks ?? 'N/A' }}</td>
                                                   @php
                                                       $assignedIds = array_filter(
                                                           array_map('trim', explode(',', $doc->set_users_to ?? '')),
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

                                                   <td>{{ implode(', ', $assignedNames) }}</td>
                                                   <td class=" text-small">
                                                       @if ($doc->routing_status == 1)
                                                           <span class="badge badge-warning">Routed To <br> President</span>
                                                       @elseif($doc->routing_status == 2)
                                                           <span class="badge badge-info">Routed Back To <br> Records</span>
                                                       @elseif($doc->routing_status == 3)
                                                           <span class="badge bg-success">Pending</span>
                                                       @else
                                                           <span class="badge bg-secondary">Unknown</span>
                                                       @endif
                                                   </td>
                                                   <td>
                                                       {{ $doc->pres_dept }} /
                                                       {{ \Carbon\Carbon::parse($doc->updated_at)->format('F j, Y') }}
                                                   </td>
                                                   <td>
                                                       <div class="buttons">

                                                           <a href="{{ route('editRoutingPres', ['id' => $doc->id]) }}"
                                                               class="btn btn-icon btn-success edit-slip-btn"><i
                                                                   class="fas fa-pen"></i>
                                                           </a>

                                                           <a href="#" class="btn btn-icon btn-danger"><i
                                                                   class="fas fa-trash"></i></a>
                                                       </div>
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
   @endsection
