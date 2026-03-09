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


                           <form action="{{ route('updateRoutingPres', $slip->id) }}" method="POST"
                               enctype="multipart/form-data">
                               @csrf
                               @method('PUT')

                               <div class="card-body text-bold">

                                   {{-- TRANSACTION TYPE --}}

                                   <input type="hidden" name="source" class="form-control"
                                       value="{{ old('source', $slip->transaction_type) }}" placeholder="Control Number"
                                       readonly>

                                   <input type="hidden" name="validity" value="{{ $slip->validity }}">

                                   <div class="form-group">
                                       <div class="row">

                                           {{-- CONTROL NUMBER --}}
                                           <div class="col-md-6">
                                               <label>Control Number</label>
                                               <input type="text" name="op_ctrl" class="form-control"
                                                   value="{{ old('op_ctrl', $slip->op_ctrl) }}" required>
                                           </div>

                                           {{-- DATE RECEIVED --}}
                                           <div class="col-md-6">
                                               <label>Date Received</label>
                                               <input type="date" name="date_received" class="form-control"
                                                   value="{{ old('date_received', $slip->date_received) }}" readonly>
                                           </div>

                                       </div>
                                   </div>


                                   {{-- SOURCE --}}
                                   <div class="form-group">
                                       <label>Source</label>
                                       <input type="text" name="source" class="form-control"
                                           value="{{ old('source', $slip->source) }}" readonly>
                                   </div>

                                   {{-- SUBJECT --}}
                                   <div class="form-group">
                                       <label>Subject</label>
                                       <textarea name="subject" class="form-control" rows="2" readonly>{{ old('subject', $slip->subject) }}</textarea>
                                   </div>

                                   {{-- TRANSACTION REMARKS --}}
                                   <div class="form-group">
                                       <label>Transaction Remarks</label>
                                       <select class="form-control" name="trans_remarks">
                                           @foreach (['Appropriate Action', 'Comment &/or Recommendation', 'Information', 'Endorsement', 'Edit/Correct', 'Review/Study', 'File', 'Draft Reply'] as $remark)
                                               <option value="{{ $remark }}"
                                                   {{ $slip->trans_remarks == $remark ? 'selected' : '' }}>
                                                   {{ $remark }}
                                               </option>
                                           @endforeach
                                       </select>
                                   </div>

                                   {{-- ACTION UNIT --}}
                                   @php
                                       $assignedUsers = explode(',', $slip->set_users_to);
                                   @endphp

                                   <div class="form-group">
                                       <label>Action Unit</label>
                                       <select name="set_users_to[]" class="form-control select2" multiple required>
                                           @foreach ($users as $user)
                                               <option value="{{ $user->id }}"
                                                   {{ in_array($user->id, $assignedUsers) ? 'selected' : '' }}>
                                                   {{ $user->fname }} {{ $user->lname }}
                                               </option>
                                           @endforeach
                                       </select>
                                   </div>

                                   {{-- FILE --}}
                                   <div class="form-group">
                                       <label>Upload File</label>
                                       <input type="file" name="file" class="form-control">

                                       @if ($slip->file)
                                           <small class="text-muted">
                                               Current file:
                                               <a href="{{ asset('storage/documents/' . $slip->file) }}" target="_blank">
                                                   {{ $slip->file }}
                                               </a>
                                           </small>
                                       @endif
                                   </div>

                                   {{-- ADDITIONAL REMARKS --}}
                                   <div class="form-group">
                                       <label>Additional Remarks</label>
                                       <input type="text" name="other_remarks" class="form-control"
                                           value="{{ old('other_remarks', $slip->other_remarks) }}">
                                   </div>

                               </div>

                               <div class="card-footer text-right">
                                   <button class="btn btn-primary">
                                       <i class="fas fa-save"></i> Update
                                   </button>
                               </div>

                           </form>
                       </div>


                   </div>
               </div>
           </section>
       </div>


       <!-- Page Specific JS File -->
   @endsection
