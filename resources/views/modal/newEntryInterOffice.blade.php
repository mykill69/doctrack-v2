<style>
    .input-group-text {
        min-width: 45px;
        display: flex;
        align-items: center;
        justify-content: center;

    }

    #envelope {
        min-width: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: auto;
    }
</style>

<div class="modal fade" id="addEntryModal" tabindex="-1" aria-labelledby="addEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEntryModalLabel">Document Transmittal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Updated form -->
            <form id="routingForm" method="POST" action="{{ route('interOffice.addEntry') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="inter_office_id" value="{{ $interOffice->id }}">

                <div class="modal-body">
                    <!-- Transaction Type -->
                    <div class="form-group">
                        <label class="font-weight-bold">Document Type</label>
                        <input type="text" class="form-control" value="{{ $interOffice->trans_type }}" readonly>
                    </div>

                    <!-- Subject -->
                    <div class="form-group">
                        <label class="font-weight-bold">Subject Matter</label>
                        <textarea name="subject" class="form-control" rows="2" placeholder="Enter Subject" readonly>{{ $interOffice->subject }}</textarea>
                    </div>

                    <!-- Select Users -->
                    <div class="form-group">
                        <label class="font-weight-bold">Select Personnels</label>
                        @php
                            // Get existing user IDs from inter_office_logs for this track_slip
                            $existingUserIds = $interOffice->logs->pluck('user_id')->toArray();
                        @endphp
                        <select class="form-control select2" name="user_id[]" multiple style="width:100%;" required>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ in_array($user->id, $existingUserIds) ? 'selected' : '' }}>
                                    {{ $user->fname }} {{ $user->lname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- File -->
                    <div class="form-group">
                        <label class="font-weight-bold">Attach File</label>
                        <input type="file" name="file" class="form-control">
                        @if ($interOffice->file)
                            <small class="text-muted">Current file: {{ basename($interOffice->file) }}</small>
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include select2 initialization -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script>
    $('#routingForm').submit(function() {
        $('.select2').trigger('change');
    });
</script>
