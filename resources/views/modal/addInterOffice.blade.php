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

<div class="modal fade" tabindex="-1" role="dialog" id="interOfficeModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Document Transmittal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="routingForm" method="POST" action="{{ route('storeInterOffice') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body p-0">


                        <!-- Transaction Type -->
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label class="font-weight-bold">Document Type</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                    </div>
                                    <select name="trans_type" class="form-control" required>
                                        <option value="">Select</option>
                                        <option value="Issuance">Issuance</option>
                                        <option value="Correspondence">Correspondence</option>
                                        <option value="DPCR/IPCR">DPCR/IPCR</option>
                                        <option value="PAPS-PRE">PAPS-PRE</option>
                                        <option value="PPMP">PPMP</option>
                                        <option value="Reimbursement">Reimbursement</option>
                                        <option value="Travel Authority">Travel Authority</option>
                                        <option value="Other Document">Other Document</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label class="font-weight-bold">Subject Matter</label>
                            <div class="input-group align-items-stretch">
                                <div class="input-group-prepend">
                                    <div class="input-group-text d-flex align-items-center" id="envelope"><i
                                            class="fas fa-envelope"></i></div>
                                </div>
                                <textarea name="subject" class="form-control" rows="2" placeholder="Enter Subject" required></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Select Multiple Users -->
                    <div class="form-group">
                        <label class="font-weight-bold">Select Personnels</label>

                        <select class="form-control select2" name="user_ids[]" multiple
                            data-placeholder="Select here..." style="width: 100%;" required>

                            @foreach ($users as $user)
                                @if (!(in_array(auth()->user()->role, ['super_user', 'staff']) && $user->id == 1235))
                                    <option value="{{ $user->id }}">
                                        {{ $user->fname }} {{ $user->lname }}
                                    </option>
                                @endif
                            @endforeach

                        </select>
                    </div>

                    <!-- Remarks -->
                    <div class="form-group">
                        <label class="font-weight-bold">Attach File</label>
                        <input type="file" name="file" id="fileInput" class="form-control">
                    </div>

                    <!-- Submit -->
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Submit
                        </button>
                    </div>

            </div>
            </form>



        </div>
    </div>
</div>


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select users...',
            allowClear: true,
            width: '100%'
        });
    });
</script>
