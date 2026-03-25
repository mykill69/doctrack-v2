@extends('home.main')

@section('body')
<div class="main-content text-center">
    <div style="margin-top:150px;">
        <i class="fas fa-spinner fa-spin" style="font-size:50px;"></i>
        <h3 class="mt-3">Processing Inter-Office Transaction...</h3>
        <p>Please wait, do not refresh.</p>
    </div>
</div>

<!-- Auto-submit form to storeInterOffice -->
<form id="processForm" method="POST" action="{{ route('storeInterOffice') }}" enctype="multipart/form-data">
    @csrf

    @foreach(request()->all() as $key => $value)
        @if(is_array($value))
            @foreach($value as $v)
                <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
</form>

<script>
    window.onload = function() {
        document.getElementById('processForm').submit();
    }
</script>
@endsection