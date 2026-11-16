@php
    // Detailed support options, grouped. Stored in the same operational_support
    // JSON as the top-level checkboxes, under "<group>__<option>" keys.
    $supportGroups = config('support_options');
@endphp

@foreach($supportGroups as $groupKey => $group)
    @php
        $groupHasChecked = collect($group['options'])->keys()
            ->contains(fn ($opt) => !empty($operationalSupport[$groupKey . '__' . $opt]));
    @endphp
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-light border-bottom py-3"
             data-bs-toggle="collapse" data-bs-target="#support_group_{{ $groupKey }}"
             style="cursor: pointer;">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-semibold">{{ $group['title'] }}</h6>
                <i class="bi bi-chevron-down"></i>
            </div>
        </div>
        <div class="collapse {{ $groupHasChecked ? 'show' : '' }}" id="support_group_{{ $groupKey }}">
            <div class="card-body p-4">
                @if(!empty($group['note']))
                    <p class="text-muted small mb-3">{{ $group['note'] }}</p>
                @endif
                <div class="row">
                    @foreach($group['options'] as $optKey => $optLabel)
                        @php $fullKey = $groupKey . '__' . $optKey; @endphp
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="operational_support[{{ $fullKey }}]"
                                    id="support_{{ $fullKey }}"
                                    @if($fullKey === 'comms__other') onchange="document.getElementById('comms_other_text').classList.toggle('d-none', !this.checked)" @endif
                                    value="1"
                                    {{ !empty($operationalSupport[$fullKey]) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="support_{{ $fullKey }}">{{ $optLabel }}</label>
                            </div>
                            @if($fullKey === 'comms__other')
                                <input
                                    type="text"
                                    id="comms_other_text"
                                    class="form-control form-control-sm mt-2 {{ !empty($operationalSupport['comms__other']) ? '' : 'd-none' }}"
                                    name="operational_support[comms__other_text]"
                                    value="{{ $operationalSupport['comms__other_text'] ?? '' }}"
                                    placeholder="Click to specify"
                                    maxlength="255"
                                >
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endforeach
