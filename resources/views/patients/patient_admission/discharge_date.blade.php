<div class="align-items-center mt-3">
    @if(!empty($row->discharge_date))
        <div class="badge bg-light-info">
            <div class="mb-2">{{ \Carbon\Carbon::parse($row->discharge_date)->translatedFormat('g:i A') }}</div>
            <div>{{ \Carbon\Carbon::parse($row->discharge_date)->translatedFormat('jS M, Y') }}</div>
        </div>
    @else
        N/A
    @endif
</div>
