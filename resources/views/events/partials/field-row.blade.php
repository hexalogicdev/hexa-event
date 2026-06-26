@php
    $fieldTypes = ['text', 'email', 'phone', 'number', 'date', 'textarea', 'dropdown', 'radio', 'checkbox', 'file'];
    $field = array_merge([
        'id' => '',
        'label' => '',
        'key' => '',
        'type' => 'text',
        'required' => false,
        'options' => [],
    ], $field ?? []);
    $optionsValue = is_array($field['options']) ? implode("\n", $field['options']) : (string) $field['options'];
@endphp

<div class="ems-card">
    <div class="ems-card-body">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <div class="ems-badge ems-badge--info mb-2">Field {{ $index + 1 }}</div>
                <input type="hidden" name="fields[{{ $index }}][id]" value="{{ $field['id'] }}">
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
                <input type="text" class="form-control" name="fields[{{ $index }}][label]" value="{{ $field['label'] }}" placeholder="Company name">
            </div>
            <div class="ems-form-span-6">
                <label class="form-label">Key</label>
                <input type="text" class="form-control" name="fields[{{ $index }}][key]" value="{{ $field['key'] }}" placeholder="company_name">
            </div>
            <div class="ems-form-span-6">
                <label class="form-label">Type</label>
                <select class="form-select" name="fields[{{ $index }}][type]">
                    @foreach ($fieldTypes as $type)
                        <option value="{{ $type }}" @selected($field['type'] === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="ems-form-span-6">
                <label class="form-label">Options</label>
                <textarea class="form-control" name="fields[{{ $index }}][options]" rows="3" placeholder="Enter one option per line">{{ $optionsValue }}</textarea>
                <div class="ems-form-hint mt-2">Use one line per option for dropdown, radio, and checkbox fields.</div>
            </div>
            <div class="ems-form-span-12">
                <label class="form-check border rounded-4 p-3 w-100">
                    <input class="form-check-input me-2" type="checkbox" name="fields[{{ $index }}][required]" value="1" @checked($field['required'])>
                    <span class="form-check-label">Required field</span>
                </label>
            </div>
        </div>
    </div>
</div>
