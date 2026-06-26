@extends('layouts.layoutMaster')
@section('title', 'Manage Fields')

@section('content')
    <div class="ems-page-section">
        <div class="ems-dashboard-hero ems-fade-up">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <span class="ems-badge ems-badge--info mb-3">Organizer > Events > Manage Fields</span>
                    <h5 class="ems-dashboard-hero__text mb-3">{{ $event->name }}</h5>
                    <p class="ems-dashboard-hero__text mb-0">
                        Add, edit, reorder, and require custom attendee fields without mixing them into the basic event
                        details form.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-light">Overview</a>
                    <a href="{{ route('events.attendees', $event->id) }}" class="btn btn-outline-light">Attendees</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="ems-surface">
                <div class="ems-card-header d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <h2 class="ems-section-title h4 mb-1">Registration fields</h2>
                        <p class="ems-section-subtitle mb-0">These fields appear on the public event registration form.</p>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="addFieldRow">Add new field</button>
                </div>
                <div class="ems-card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-4">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('events.fields.update', $event->id) }}" method="POST" id="fieldForm">
                        @csrf
                        <div id="fieldsContainer" class="d-grid gap-3">
                            @forelse ($fields as $index => $field)
                                @include('events.partials.field-row', ['index' => $index, 'field' => $field])
                            @empty
                                @include('events.partials.field-row', ['index' => 0, 'field' => ['label' => '', 'key' => '', 'type' => 'text', 'required' => false, 'options' => []]])
                            @endforelse
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Save fields</button>
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-primary">Back to overview</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="d-grid gap-4">
                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Supported field types</h2>
                            <p>Use the right input type for each question.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body">
                        <div class="ems-grid ems-grid--2">
                            @foreach (['Text', 'Email', 'Phone', 'Number', 'Date', 'Textarea', 'Dropdown', 'Radio', 'Checkbox', 'File'] as $type)
                                <span class="ems-badge ems-badge--muted justify-content-center">{{ $type }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="ems-dashboard-card">
                    <div class="ems-dashboard-card__head">
                        <div>
                            <h2>Field rules</h2>
                            <p>Keep the form easy to use.</p>
                        </div>
                    </div>
                    <div class="ems-dashboard-card__body">
                        <ul class="ems-support-list">
                            <li><i class="ti ti-point"></i><div><div class="fw-semibold">Required fields</div><div class="text-white-50">Mark important questions as required.</div></div></li>
                            <li><i class="ti ti-point"></i><div><div class="fw-semibold">Dropdown and radio options</div><div class="text-white-50">Enter each option on a new line.</div></div></li>
                            <li><i class="ti ti-point"></i><div><div class="fw-semibold">Reordering</div><div class="text-white-50">Use the arrows to move fields up or down.</div></div></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        const fieldsContainer = document.getElementById('fieldsContainer');
        const addFieldRowButton = document.getElementById('addFieldRow');
        const fieldTypes = ['text', 'email', 'phone', 'number', 'date', 'textarea', 'dropdown', 'radio', 'checkbox', 'file'];

        const createRow = (index) => {
            const row = document.createElement('div');
            row.className = 'ems-card';
            row.innerHTML = `
                <div class="ems-card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <div class="ems-badge ems-badge--info mb-2">Field ${index + 1}</div>
                            <input type="hidden" name="fields[${index}][id]" value="">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-light btn-sm" data-move="up">Up</button>
                            <button type="button" class="btn btn-outline-light btn-sm" data-move="down">Down</button>
                            <button type="button" class="btn btn-outline-light btn-sm" data-remove="true">Delete</button>
                        </div>
                    </div>
                    <div class="ems-form-grid">
                        <div class="ems-form-span-6">
                            <label class="form-label">Label</label>
                            <input type="text" class="form-control" name="fields[${index}][label]" placeholder="Company name">
                        </div>
                        <div class="ems-form-span-6">
                            <label class="form-label">Key</label>
                            <input type="text" class="form-control" name="fields[${index}][key]" placeholder="company_name">
                        </div>
                        <div class="ems-form-span-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="fields[${index}][type]">
                                ${fieldTypes.map((type) => `<option value="${type}">${type}</option>`).join('')}
                            </select>
                        </div>
                        <div class="ems-form-span-6">
                            <label class="form-label">Options</label>
                            <textarea class="form-control" name="fields[${index}][options]" rows="3" placeholder="Enter one option per line"></textarea>
                            <div class="ems-form-hint mt-2">Use one line per option for dropdown, radio, and checkbox fields.</div>
                        </div>
                        <div class="ems-form-span-12">
                            <label class="form-check border rounded-4 p-3 w-100">
                                <input class="form-check-input me-2" type="checkbox" name="fields[${index}][required]" value="1">
                                <span class="form-check-label">Required field</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            attachRowActions(row);
            return row;
        };

        const attachRowActions = (row) => {
            row.querySelector('[data-remove="true"]').addEventListener('click', () => {
                if (fieldsContainer.children.length > 1) {
                    row.remove();
                    renumberRows();
                }
            });

            row.querySelector('[data-move="up"]').addEventListener('click', () => {
                const previous = row.previousElementSibling;
                if (previous) {
                    fieldsContainer.insertBefore(row, previous);
                    renumberRows();
                }
            });

            row.querySelector('[data-move="down"]').addEventListener('click', () => {
                const next = row.nextElementSibling;
                if (next) {
                    fieldsContainer.insertBefore(next, row);
                    renumberRows();
                }
            });
        };

        const renumberRows = () => {
            [...fieldsContainer.children].forEach((row, index) => {
                row.querySelector('.ems-badge').textContent = `Field ${index + 1}`;
                row.querySelectorAll('input, select, textarea').forEach((input) => {
                    const name = input.getAttribute('name');
                    if (!name) return;
                    input.setAttribute('name', name.replace(/fields\[\d+\]/, `fields[${index}]`));
                });
            });
        };

        [...fieldsContainer.children].forEach((row) => attachRowActions(row));
        addFieldRowButton.addEventListener('click', () => {
            fieldsContainer.appendChild(createRow(fieldsContainer.children.length));
            renumberRows();
        });
    </script>
@endsection
