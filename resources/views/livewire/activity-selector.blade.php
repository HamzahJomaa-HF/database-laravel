<div>
    {{-- Activity Search --}}
    <div class="form-group">
        <input type="text" 
               class="form-control" 
               wire:model.live.debounce.300ms="activitySearch" 
               placeholder="Type to search..."
               autocomplete="off">
    </div>

    {{-- Activity Search Results - EXACTLY like the original --}}
    @if($showResults && count($results) > 0)
        <div class="mb-3" style="border: 1px solid #ddd; max-height: 350px; overflow-y: auto; background: white; position: absolute; z-index: 1000; width: 90%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            @foreach($results as $activity)
                <div class="p-3 border-bottom" style="cursor: pointer; transition: all 0.2s;" 
                    wire:click="selectActivity('{{ $activity['id'] }}')"
                    wire:key="activity-{{ $activity['id'] }}"
                    onmouseover="this.style.backgroundColor='#f0f7ff'; this.style.borderLeft='3px solid #28a745'"
                    onmouseout="this.style.backgroundColor='white'; this.style.borderLeft='3px solid transparent'">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong class="text-success" style="font-size: 1.1rem;">
                            <i class="fas fa-calendar-alt"></i> {{ $activity['title'] }}
                        </strong>
                        <span class="badge bg-info" style="font-size: 0.8rem; padding: 5px 10px;">
                            <i class="fas fa-tag"></i> {{ $activity['type'] ?? 'No Type' }}
                        </span>
                    </div>
                    
                    <div class="d-flex flex-column gap-2">
                        <div><small class="text-muted"><i class="fas fa-tag fa-fw"></i> <strong>Type:</strong> {{ $activity['type'] ?? 'Not specified' }}</small></div>
                        <div><small class="text-muted"><i class="fas fa-calendar fa-fw"></i> <strong>Start Date:</strong> {{ $activity['start_date_formatted'] ?? 'No date' }}</small></div>
                        <div><small class="text-muted"><i class="fas fa-map-marker-alt fa-fw"></i> <strong>Venue:</strong> {{ $activity['venue'] ?? 'No venue' }}</small></div>
                        <div><small class="text-muted"><i class="fas fa-users fa-fw"></i> <strong>Max Capacity:</strong> {{ $activity['max_capacity'] ?? 'Not set' }}</small></div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($showResults && strlen($activitySearch) > 0)
        <div class="mb-3 p-4 text-center" style="border: 1px solid #ddd; background: white; position: absolute; z-index: 1000; width: 90%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">No activities found matching</h6>
            <p class="text-success font-weight-bold">"{{ $activitySearch }}"</p>
            <small class="text-muted">Try different search terms</small>
        </div>
    @endif

    {{-- Selected Activity - EXACTLY like the original --}}
    <div class="mt-3" style="margin-top: 60px;">
        <label>Selected Activity:</label>
        <div class="p-3 border bg-light">
            @if($selectedActivity)
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong class="text-success">{{ $selectedActivity['title'] }}</strong>
                        <br>
                        <span class="badge bg-info mb-2">{{ $selectedActivity['type'] ?? 'No Type' }}</span>
                        <div class="mt-2">
                            @if($selectedActivity['start_date_formatted'])
                                <small class="d-block text-muted"><i class="fas fa-calendar"></i> Start: {{ $selectedActivity['start_date_formatted'] }}</small>
                            @endif
                            @if($selectedActivity['venue'])
                                <small class="d-block text-muted"><i class="fas fa-map-marker-alt"></i> Venue: {{ $selectedActivity['venue'] }}</small>
                            @endif
                            @if($selectedActivity['max_capacity'])
                                <small class="d-block text-muted"><i class="fas fa-users"></i> Max Capacity: {{ $selectedActivity['max_capacity'] }}</small>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger" wire:click="clearSelection">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @else
                <div class="text-center text-muted py-2">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <p class="mb-0">None selected</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden input for form submission --}}
    <input type="hidden" name="activity_id" id="activity_id_hidden" value="{{ $selectedActivityId }}">

    <script>
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[wire\\:model\\.live\\.debounce\\.300ms="activitySearch"]') && 
                !e.target.closest('[wire\\:click*="selectActivity"]')) {
                @this.hideResults();
            }
        });

        // Listen for activity selection to update hidden input
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('activity-selected', function(event) {
                const hiddenInput = document.getElementById('activity_id_hidden');
                if (hiddenInput && event.detail.activityId) {
                    hiddenInput.value = event.detail.activityId;
                }
            });
            
            window.addEventListener('activity-cleared', function() {
                const hiddenInput = document.getElementById('activity_id_hidden');
                if (hiddenInput) {
                    hiddenInput.value = '';
                }
            });
        });
    </script>
</div>