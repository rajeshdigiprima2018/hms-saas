<div class="d-flex align-items-center">
    @if ($row->date === null)
        N/A
    @else
        <div class="badge bg-light-info">
            <div>
                {{ \Carbon\Carbon::parse($row->date)->isoFormat('Do MMM, Y')}}
            </div>
        </div>
    @endif
</div>

