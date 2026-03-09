{{-- 

<!-- EDIT TRANSACTION MODAL -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="routingForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Transaction</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>

                <div class="modal-body">
                    <div class="card-body p-0">

                        <!-- TRANSACTION TYPE & VALIDITY -->
                        <select class="form-control" name="transaction_type" required hidden>
                            <option value="1">PRESIDENT'S ACTION</option>
                            <option value="2">PERSONNEL'S ACTION</option>
                        </select>

                        <input type="hidden" name="validity" class="form-control">
                        <input type="hidden" name="control_number" class="form-control" required>

                        <!-- CONTROL NUMBER & DATE -->
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Control Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-hashtag"></i></div>
                                    </div>
                                    <input type="text" name="op_ctrl" class="form-control"
                                        placeholder="Type the Control Number here" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="font-weight-bold">Date Received</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                    </div>
                                    <input type="date" name="date_received" class="form-control" required readonly>
                                </div>
                            </div>
                        </div>

                        <!-- SOURCE -->
                        <div class="form-group">
                            <label class="font-weight-bold">Source of Document</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-flag"></i></div>
                                </div>
                                <input type="text" name="source" class="form-control" required readonly>
                            </div>
                        </div>

                        <!-- SUBJECT -->
                        <div class="form-group">
                            <label class="font-weight-bold">Subject Matter</label>
                            <div class="input-group align-items-stretch">
                                <div class="input-group-prepend">
                                    <div class="input-group-text d-flex align-items-center" id="envelope">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                                <textarea name="subject" class="form-control" rows="2" placeholder="Enter Subject" required readonly></textarea>
                            </div>
                        </div>

                        <!-- TRANSACTION REMARKS -->
                        <div class="form-group">
                            <label class="font-weight-bold">Transaction Remarks</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-exchange-alt"></i></div>
                                </div>
                                <select class="form-control" id="transRemarks" name="trans_remarks" required>
                                    <option value="">Select Remarks</option>
                                    <option value="Appropriate Action">Appropriate Action</option>
                                    <option value="Comment &/or Recommendation">Comment &/or Recommendation</option>
                                    <option value="Information">Information</option>
                                    <option value="Endorsement">Endorsement</option>
                                    <option value="Edit/Correct">Edit/Correct</option>
                                    <option value="Review/Study">Review/Study</option>
                                    <option value="File">File</option>
                                    <option value="Draft Reply">Draft Reply</option>
                                </select>
                            </div>
                        </div>

                        <!-- ACTION UNIT -->
                        <div class="form-group">
                            <label class="font-weight-bold">Action Unit</label>
                            <div class="input-group">
                                <select name="set_users_to[]" class="form-control select2"
                                    data-placeholder="Select users..." multiple required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- FILE -->
                        <div class="form-group">
                            <label class="font-weight-bold">Upload File</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-paperclip"></i></div>
                                </div>
                                <input type="file" name="file" class="form-control">
                            </div>
                            <small class="text-muted d-block mt-1" id="currentFile"></small>
                        </div>

                        <!-- ADDITIONAL REMARKS -->
                        <div class="form-group">
                            <label class="font-weight-bold">Additional Remarks (optional)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                                </div>
                                <input type="text" name="other_remarks" class="form-control"
                                    placeholder="Additional Remarks">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

{{-- 

<script>
    var updateRouteTemplate = "{{ route('updateRoutingPres', ':id') }}";

    $(document).on('click', '.edit-slip-btn', function(e) {
        e.preventDefault();

        const id = $(this).data('id');
        const action = updateRouteTemplate.replace(':id', id);
        $('#routingForm').attr('action', action);

        // Fill form fields
        $('#routingForm input[name="control_number"]').val($(this).data('control'));
        $('#routingForm input[name="date_received"]').val($(this).data('date'));
        $('#routingForm input[name="source"]').val($(this).data('source'));
        $('#routingForm textarea[name="subject"]').val($(this).data('subject'));
        $('#routingForm input[name="validity"]').val($(this).data('validity'));
        $('#routingForm select[name="transaction_type"]').val($(this).data('transaction'));

        // Current file
        const file = $(this).data('file');
        $('#currentFile').text(file ? 'Current file: ' + file.replace(/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_/,
            '') : '');

        // Show modal
        $('#editTransactionModal').modal('show');

        // Reinitialize Select2 after modal is visible
        setTimeout(() => {
            const $select = $('#editTransactionModal').find('select.select2');
            if ($select.hasClass("select2-hidden-accessible")) $select.select2('destroy');
            $select.select2({
                width: '100%',
                placeholder: $select.data('placeholder'),
                allowClear: true
            });
        }, 100);
    });
</script> --}} --}}
