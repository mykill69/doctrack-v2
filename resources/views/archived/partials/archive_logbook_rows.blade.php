@foreach ($routingSlips as $slip)
    @php
        $rUsersNames = collect(explode(',', $slip->routed_users ?? ''))
            ->map(function ($id) {
                $user = \App\Models\User::find($id);
                return $user ? $user->fname . ' ' . $user->lname : null;
            })
            ->filter()
            ->implode(', ');
    @endphp

    <tr>
        <td>{{ $slip->rslip_id }}</td>
        <td>{{ $slip->date_received }}</td>
        <td>{{ $slip->source }}</td>
        <td>{{ $slip->subject }}</td>
        <td>{{ $slip->pres_dept }}</td>
        <td><small>{{ $slip->created_at->format('F j, Y') }}</small></td>
        <td>{{ $rUsersNames ?: '—' }}</td>
        <td>{{ $slip->created_at ? $slip->created_at->format('m-d-Y h:i:s A') : '—' }}</td>
        <td>{{ $slip->trans_remarks }}</td>
        <td>
            @if ($slip->file)
                @php
                    $displayName = preg_replace('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}_/', '', $slip->file);
                @endphp
                <a href="{{ asset('storage/documents/' . $slip->file) }}" target="_blank">
                    {{ $displayName }}
                </a>
            @else
                N/A
            @endif
        </td>
        <td>{{ $slip->updated_at ? $slip->updated_at->format('m-d-Y h:i:s A') : 'N/A' }}</td>
        <td>{{ $slip->validity }}</td>
        <td>
            <span class="badge bg-{{ $slip->validity_status == 0 ? 'success' : 'danger' }}">
                {{ $slip->validity_status == 0 ? 'Valid' : 'Invalid' }}
            </span>
        </td>
    </tr>
@endforeach
