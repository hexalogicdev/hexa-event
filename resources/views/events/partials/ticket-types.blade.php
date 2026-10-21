@php
    $savedTickets = old('ticket_types', isset($event) ? $event->ticketTypes->toArray() : []);
    if (empty($savedTickets)) {
        $savedTickets = [['name' => 'General admission', 'description' => '', 'price' => '', 'quantity' => '']];
    }
    $selectedTicketingType = old('ticketing_type', $event->ticketing_type ?? 'free');
@endphp

<div class="ems-form-span-12">
    <div class="border rounded-4 p-3 p-lg-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-3">
            <div>
                <label for="ticketing_type" class="form-label mb-1">Admission type</label>
                <p class="ems-form-hint mb-0">Use free admission or create priced ticket categories with optional limits.</p>
            </div>
            <select class="form-select" id="ticketing_type" name="ticketing_type" style="max-width: 220px">
                <option value="free" @selected($selectedTicketingType === 'free')>Free admission</option>
                <option value="ticketed" @selected($selectedTicketingType === 'ticketed')>Ticketed event</option>
            </select>
        </div>

        <div id="ticket-types-builder" class="{{ $selectedTicketingType === 'ticketed' ? '' : 'd-none' }}">
            <div id="ticket-type-rows" class="d-grid gap-3">
                @foreach ($savedTickets as $index => $ticket)
                    <div class="ems-ticket-row border rounded-4 p-3" data-ticket-row>
                        <input type="hidden" name="ticket_types[{{ $index }}][id]" value="{{ $ticket['id'] ?? '' }}">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">Category name</label>
                                <input class="form-control" name="ticket_types[{{ $index }}][name]" value="{{ $ticket['name'] ?? '' }}" placeholder="VIP, General admission...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Price</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="ticket_types[{{ $index }}][price]" value="{{ $ticket['price'] ?? '' }}" placeholder="0.00">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" min="1" class="form-control" name="ticket_types[{{ $index }}][quantity]" value="{{ $ticket['quantity'] ?? '' }}" placeholder="Unlimited">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-danger w-100" data-remove-ticket aria-label="Remove ticket"><i class="ti ti-trash"></i></button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="2" name="ticket_types[{{ $index }}][description]" placeholder="What is included with this ticket?">{{ $ticket['description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary mt-3" id="add-ticket-type"><i class="ti ti-plus me-2"></i>Add ticket category</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const type = document.getElementById('ticketing_type');
    const builder = document.getElementById('ticket-types-builder');
    const rows = document.getElementById('ticket-type-rows');
    const add = document.getElementById('add-ticket-type');
    if (!type || !builder || !rows || !add) return;

    const refresh = () => {
        builder.classList.toggle('d-none', type.value !== 'ticketed');
        rows.querySelectorAll('input, textarea').forEach(input => input.disabled = type.value !== 'ticketed');
    };
    const reindex = () => rows.querySelectorAll('[data-ticket-row]').forEach((row, index) => {
        row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/ticket_types\[\d+\]/, `ticket_types[${index}]`));
    });
    type.addEventListener('change', refresh);
    rows.addEventListener('click', event => {
        const button = event.target.closest('[data-remove-ticket]');
        if (!button) return;
        if (rows.querySelectorAll('[data-ticket-row]').length === 1) {
            button.closest('[data-ticket-row]').querySelectorAll('input:not([type=hidden]), textarea').forEach(input => input.value = '');
            return;
        }
        button.closest('[data-ticket-row]').remove();
        reindex();
    });
    add.addEventListener('click', () => {
        const row = rows.querySelector('[data-ticket-row]').cloneNode(true);
        row.querySelectorAll('input, textarea').forEach(input => input.value = '');
        rows.appendChild(row);
        reindex();
    });
    refresh();
});
</script>
@endpush
