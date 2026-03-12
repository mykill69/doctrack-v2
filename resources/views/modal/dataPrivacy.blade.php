<div class="modal fade" tabindex="-1" role="dialog" id="dataPrivacy">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">

            <div class="modal-header">
                {{-- <h5 class="modal-title">Data Privacy Notice</h5> --}}
            </div>

            <div class="modal-body" style="text-align: justify;">
                <p class="text-center"><strong>DATA PRIVACY COMPLIANCE</strong></p>
                <div class="px-4 px-md-5 px-lg-5">
                <p>
                    In compliance with <a href="https://privacy.gov.ph/data-privacy-act/" target="blank_" class="text-primary">Republic Act No. 10173</a>, also known as the <em>Data Privacy Act of 2012</em>, 
                    Central Philippines State University recognizes its responsibility to respect and protect the personal data of its clients. In line with this, Central Philippines State University 
                    is committed to ensuring the security and confidentiality of all personal information collected through its Document Tracking System.
                </p>

                <p><strong>Purpose of Data Collection</strong></p>
                <p>The information collected, including your name and signature, will be used for the following purposes:</p>
                <ol>
                    <li>Monitoring, tracking, and managing official documents within the university;</li>
                    <li>Ensuring accountability and transparency in document processing and handling; and</li>
                    <li>Serving as official records for audits, internal assessments, and accreditations.</li>
                </ol>
                <p>
                    All collected data will be securely archived and maintained by the Records Office of Central Philippines State University, in accordance with internal data retention policies and applicable legal standards.
                </p>

                <p><strong>Consent and Acknowledgment</strong></p>
                <p>
                    By submitting your personal information through this system or form, you voluntarily and expressly consent to the collection, recording, organization, updating, use, consolidation, storage, and processing of your personal data, including your name and signature, for the purposes stated above.
                </p>
                <p>
                    You understand that your data shall be retained only as long as necessary for the fulfillment of these purposes and shall be protected from unauthorized access, disclosure, or misuse.
                </p>
                </div>
            </div>

            <div class="modal-footer flex-column align-items-start px-4">

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="dpaCheckbox">
                    <label class="form-check-label" for="dpaCheckbox">
                        <i>I understand and agree to the Data Privacy Notice and consent to the use of my personal data.</i>
                    </label>
                </div>

                <div>
                    <button class="btn btn-success" id="acceptBtn" disabled>
                        Accept
                    </button>

                    <button class="btn btn-danger" id="declineBtn">
                        Decline & Sign Out
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<script>

document.getElementById('dpaCheckbox').addEventListener('change', function () {
    document.getElementById('acceptBtn').disabled = !this.checked;
});

document.getElementById('acceptBtn').addEventListener('click', function () {
    updateDpaStatus(1);
});

document.getElementById('declineBtn').addEventListener('click', function () {
    window.location.href = "{{ route('logout') }}";
});

function updateDpaStatus(status) {

    fetch("{{ route('update.dpa') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            dpa: status
        })
    })
    .then(response => response.json())
    .then(data => {

        $('#dataPrivacy').modal('hide');
        location.reload();

    });

}

</script>