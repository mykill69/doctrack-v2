@extends('home.main')
@section('body')
    <div class="main-content">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <section class="section">
            <div class="row row-deck">
                <div class="col-12">
                    <div class="card position-relative text-center">
                        <!-- Logo centered on top of the card header -->
                        <img src="{{ asset('template/img/cpsu_logo.png') }}" alt="cpsu logo" width="110" height="110"
                            class="position-absolute" style="top: -50px; left: 50%; transform: translateX(-50%);">

                        <div class="card-header">
                            <h4>LIST OF RIGESTERED GROUPS</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped text-small" id="table-2">
                                    <thead>
                                        <tr>
                                            <th>NO.</th>
                                            <th>GROUP NAME</th>
                                            <th>DATE CREATED</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($groups as $index => $group)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <input type="text" class="form-control bordered group-input"
                                                        data-id="{{ $group->id }}" data-field="group_name"
                                                        value="{{ $group->group_name }}">
                                                </td>
                                                <td>{{ $group->created_at ? $group->created_at->format('F d, Y') : '' }}
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group" aria-label="Group Actions">
                                                        <button type="button" class="btn btn-danger btn-sm" title="Delete"
                                                            onclick="deleteGroup({{ $group->id }})">
                                                            Delete <i class="fas fa-trash"></i>
                                                        </button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Save original value on focus
        $(document).on('focus', '.group-input', function() {
            $(this).data('original-value', $(this).val());
        });

        // Update group name on blur if changed
        $(document).on('blur', '.group-input', function() {
            let groupId = $(this).data('id');
            let field = $(this).data('field');
            let value = $(this).val();
            let originalValue = $(this).data('original-value');

            if (value === originalValue) return;

            $.ajax({
                url: "{{ url('groups') }}/" + groupId,
                type: 'POST',
                data: {
                    _method: 'PUT',
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    [field]: value
                },
                success: function(response) {
                    console.log('Updated successfully:', response);
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    alert('Error updating group.');
                }
            });
        });

        // Delete group
        function deleteGroup(id) {
            if (confirm('Are you sure you want to delete this group?')) {
                $.ajax({
                    url: "{{ url('groups') }}/" + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert(response.success);
                        location.reload();
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            }
        }
    </script>




    @include('modal.addGroup')
@endsection
