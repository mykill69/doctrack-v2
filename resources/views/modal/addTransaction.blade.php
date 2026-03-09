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

<div class="modal fade" tabindex="-1" role="dialog" id="exampleModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Transaction</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="routingForm" method="POST" action="{{ route('addRoutingPres') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body p-0">

                        <!-- Transaction Type -->
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Transaction Type</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                    </div>
                                    <select class="form-control" name="transaction_type" id="transactionType" required>
                                        <option value="">Select Transaction Type</option>

                                        @if (!in_array(auth()->user()->id, [20, 56]))
                                            {{-- Only show Personnel's Action --}}
                                            <option value="2">PERSONNEL's ACTION</option>
                                        @else
                                            {{-- Show both options --}}
                                            <option value="1">PRESIDENT'S ACTION</option>
                                            <option value="2">PERSONNEL's ACTION</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="font-weight-bold">Validity</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                    </div>
                                    <input type="text" name="validity" class="form-control" id="validityYear"
                                        readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Control Number & Date Received -->
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label class="font-weight-bold">Control Number</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-hashtag"></i></div>
                                    </div>
                                    <input type="text" name="control_number" class="form-control"
                                        placeholder="Enter Control Number" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="font-weight-bold">Date Received</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                                    </div>
                                    <input type="text" name="date_received" class="form-control datepicker"
                                        placeholder="Select Date" required>
                                </div>
                            </div>
                        </div>

                        <!-- Source -->
                        <div class="form-group">
                            <label class="font-weight-bold">Source of Document</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-flag"></i></div>
                                </div>
                                <input type="text" name="source" class="form-control" placeholder="Enter Source"
                                    required>
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

                        <!-- Personnel Selection -->
                        <div class="form-group d-none" id="personnelSection">
                            <label class="font-weight-bold">Select Personnels</label>
                            <select name="routed_users[]" class="form-control select2"
                                data-placeholder="Select users..." multiple="multiple">
                                @if (!in_array(auth()->user()->role, ['super_user', 'staff']))
                                    <option disabled>— Select by Group —</option>
                                    @foreach ($groups as $group)
                                        <option value="group:{{ $group->group_name }}">{{ $group->group_name }}</option>
                                    @endforeach
                                    <option disabled>──────────</option>
                                @endif

                                <option disabled>— Select by Individual User —</option>
                                @foreach ($users as $user)
                                    @if (!(in_array(auth()->user()->role, ['super_user', 'staff']) && $user->id == 1235))
                                        <option value="{{ $user->id }}">{{ $user->fname }} {{ $user->lname }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group">
                            <label class="font-weight-bold">Upload File</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-paperclip"></i></div>
                                </div>
                                <input type="file" name="file" class="form-control" required>
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer bg-whitesmoke br">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("validityYear").value = new Date().getFullYear();

        const transactionType = document.getElementById("transactionType");
        const personnelSection = document.getElementById("personnelSection");
        const form = document.getElementById("routingForm");
        const submitBtn = document.getElementById("submitBtn");

        // Show/hide personnel section and set form action dynamically
        transactionType.addEventListener("change", function() {
            if (this.value === "2") {
                personnelSection.classList.remove("d-none");
                $('.select2').select2({
                    width: '100%'
                });
            } else {
                personnelSection.classList.add("d-none");
                $('.select2').val(null).trigger('change');
            }
        });

        submitBtn.addEventListener("click", function() {
            // Set form action dynamically
            if (transactionType.value === "2") {
                form.action = "{{ route('addRoutingPersonnel') }}";
            } else {
                form.action = "{{ route('addRoutingPres') }}";
            }
            form.submit();
        });
    });
</script>
