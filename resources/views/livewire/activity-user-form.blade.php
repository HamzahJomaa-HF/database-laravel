{{-- resources/views/livewire/activity-user-form.blade.php --}}
<div>
    {{-- Flash Messages --}}
    @if(!$embedMode)
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <form wire:submit.prevent="save">
        <div class="row">
            {{-- User Selection Section - hidden in embed mode --}}
            @if(!$embedMode)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Select User</h5>
                    </div>
                    <div class="card-body">
                        {{-- User Search --}}
                        <div class="form-group">
                            <label>Search User</label>
                            <input type="text" 
                                   class="form-control" 
                                   wire:model.live.debounce.300ms="userSearch" 
                                   placeholder="Type to search..."
                                   autocomplete="off">
                        </div>

                        {{-- User Search Results --}}
                        @if($showUserResults && count($userResults) > 0)
                            <div class="mb-3" style="border: 1px solid #ddd; max-height: 400px; overflow-y: auto; background: white; position: absolute; z-index: 1000; width: 90%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                @foreach($userResults as $user)
                                    <div class="p-3 border-bottom" style="cursor: pointer; transition: all 0.2s;" 
                                        wire:click="selectUser('{{ $user['id'] }}')"
                                        wire:key="user-{{ $user['id'] }}"
                                        onmouseover="this.style.backgroundColor='#f0f7ff'; this.style.borderLeft='3px solid #0d6efd'"
                                        onmouseout="this.style.backgroundColor='white'; this.style.borderLeft='3px solid transparent'">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <strong class="text-primary" style="font-size: 1.1rem;">
                                                <i class="fas fa-user-circle"></i> 
                                                {{ $user['first_name'] }} 
                                                @if($user['middle_name'])
                                                    {{ $user['middle_name'] }} 
                                                @endif
                                                {{ $user['last_name'] }}
                                            </strong>
                                            <span class="badge {{ $user['type_badge_class'] }}" style="font-size: 0.8rem; padding: 5px 10px;">
                                                <i class="fas {{ $user['type_icon'] }}"></i> {{ $user['type'] }}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex flex-column gap-2">
                                            <div><small class="text-muted"><i class="fas fa-envelope fa-fw"></i> <strong>Email:</strong> {{ $user['email'] }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-phone fa-fw"></i> <strong>Phone:</strong> {{ $user['phone_number'] }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-id-card fa-fw"></i> <strong>First Name:</strong> {{ $user['first_name'] }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-id-card fa-fw"></i> <strong>Middle Name:</strong> {{ $user['middle_name'] }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-id-card fa-fw"></i> <strong>Last Name:</strong> {{ $user['last_name'] }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-birthday-cake fa-fw"></i> <strong>DOB:</strong> {{ $user['dob'] ?? 'Not set' }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-passport fa-fw"></i> <strong>Passport:</strong> {{ $user['passport_number'] }}</small></div>
                                            <div><small class="text-muted"><i class="fas fa-fingerprint fa-fw"></i> <strong>ID:</strong> {{ $user['identification_id'] }}</small></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($showUserResults && strlen($userSearch) > 0)
                            <div class="mb-3 p-4 text-center" style="border: 1px solid #ddd; background: white; position: absolute; z-index: 1000; width: 90%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No users found matching</h6>
                                <p class="text-primary font-weight-bold">"{{ $userSearch }}"</p>
                                <small class="text-muted">Try different search terms</small>
                            </div>
                        @endif

                        {{-- Selected User --}}
                        <div class="mt-3" style="margin-top: 60px;">
                            <label>Selected User:</label>
                            <div class="p-3 border bg-light">
                                @if($selectedUserDisplay)
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-primary">{{ $selectedUserDisplay['full_name'] }}</strong>
                                            <br>
                                            <span class="badge {{ $selectedUserDisplay['type'] == 'Beneficiary' ? 'bg-success' : 'bg-primary' }} mb-2">
                                                {{ $selectedUserDisplay['type'] }}
                                            </span>
                                            <div class="mt-2">
                                                @if($selectedUserDisplay['email'])
                                                    <small class="d-block text-muted"><i class="fas fa-envelope"></i> {{ $selectedUserDisplay['email'] }}</small>
                                                @endif
                                                @if($selectedUserDisplay['phone_number'])
                                                    <small class="d-block text-muted"><i class="fas fa-phone"></i> {{ $selectedUserDisplay['phone_number'] }}</small>
                                                @endif
                                                @if($selectedUserDisplay['dob'])
                                                    <small class="d-block text-muted"><i class="fas fa-birthday-cake"></i> {{ $selectedUserDisplay['dob'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="clearUserSelection">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-2">
                                        <i class="fas fa-user fa-2x mb-2"></i>
                                        <p class="mb-0">None selected</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <input type="hidden" wire:model="user_id">
                        @error('user_id') 
                            <span class="text-danger small">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- Activity Selection Section - ALWAYS visible --}}
            <div class="{{ $embedMode ? 'col-md-12' : 'col-md-6' }}">
                @if(!$embedMode)
                <div class="card">
                    <div class="card-header">
                        <h5>Select Activity</h5>
                    </div>
                    <div class="card-body">
                @endif

                        {{-- Activity Search --}}
                        <div class="form-group">
                            @if(!$embedMode)
                                <label>Search Activity</label>
                            @endif
                            <input type="text" 
                                   class="form-control" 
                                   wire:model.live.debounce.300ms="activitySearch" 
                                   placeholder="Type to search..."
                                   autocomplete="off">
                        </div>

                        {{-- Activity Search Results --}}
                        @if($showActivityResults && count($activityResults) > 0)
                            <div class="mb-3" style="border: 1px solid #ddd; max-height: 350px; overflow-y: auto; background: white; position: absolute; z-index: 1000; width: 90%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                @foreach($activityResults as $activity)
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
                                            <div><small class="text-muted"><i class="fas fa-users fa-fw"></i> <strong>Max Capacity:</strong> {{ $activity['maximum_capacity'] ?? 'Not set' }}</small></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($showActivityResults && strlen($activitySearch) > 0)
                            <div class="mb-3 p-4 text-center" style="border: 1px solid #ddd; background: white; position: absolute; z-index: 1000; width: 90%; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">No activities found matching</h6>
                                <p class="text-success font-weight-bold">"{{ $activitySearch }}"</p>
                                <small class="text-muted">Try different search terms</small>
                            </div>
                        @endif

                        {{-- Selected Activity --}}
                        <div class="mt-3" style="margin-top: 60px;">
                            <label>Selected Activity:</label>
                            <div class="p-3 border bg-light">
                                @if($selectedActivityDisplay)
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-success">{{ $selectedActivityDisplay['title'] }}</strong>
                                            <br>
                                            <span class="badge bg-info mb-2">{{ $selectedActivityDisplay['type'] ?? 'No Type' }}</span>
                                            <div class="mt-2">
                                                @if($selectedActivityDisplay['start_date_formatted'])
                                                    <small class="d-block text-muted"><i class="fas fa-calendar"></i> Start: {{ $selectedActivityDisplay['start_date_formatted'] }}</small>
                                                @endif
                                                @if($selectedActivityDisplay['venue'])
                                                    <small class="d-block text-muted"><i class="fas fa-map-marker-alt"></i> Venue: {{ $selectedActivityDisplay['venue'] }}</small>
                                                @endif
                                                @if($selectedActivityDisplay['maximum_capacity'])
                                                    <small class="d-block text-muted"><i class="fas fa-users"></i> Max Capacity: {{ $selectedActivityDisplay['maximum_capacity'] }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" wire:click="clearActivitySelection">
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

                        {{-- Hidden input --}}
                        {{-- In embed mode this feeds the PARENT regular form --}}
                        @if($embedMode)
                            <input type="hidden" name="activity_id" value="{{ $activity_id }}">
                        @else
                            <input type="hidden" wire:model="activity_id">
                        @endif

                        @error('activity_id') 
                            <span class="text-danger small">{{ $message }}</span> 
                        @enderror

                @if(!$embedMode)
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Everything below hidden in embed mode --}}
        @if(!$embedMode)
            {{-- Additional Fields --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="cop_id">COP</label>
                        <select wire:model="cop_id" id="cop_id" class="form-control">
                            <option value="">Select COP</option>
                            @foreach($cops as $cop)
                                <option value="{{ $cop->cop_id }}">{{ $cop->cop_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="mb-4"></div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-center">
                    <div class="form-check mx-4">
                        <input type="checkbox" class="form-check-input" id="invited" wire:model="invited" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                        <label class="form-check-label" for="invited" style="font-size: 1.1rem; padding-left: 8px; cursor: pointer;">Invited</label>
                    </div>
                    <div class="form-check mx-4">
                        <input type="checkbox" class="form-check-input" id="attended" wire:model="attended" value="1" style="width: 20px; height: 20px; cursor: pointer;">
                        <label class="form-check-label" for="attended" style="font-size: 1.1rem; padding-left: 8px; cursor: pointer;">Attended</label>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-primary" @unless($user_id && $activity_id) disabled @endunless>
                    <i class="fas fa-save"></i> {{ $isEditing ? 'Update' : 'Create' }} Assignment
                </button>
                <button type="button" class="btn btn-secondary" wire:click="resetForm">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <a href="{{ route('activity-users.index') }}" class="btn btn-default">
                    Cancel
                </a>
            </div>
        @endif
    </form>

    <script>
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[wire\\:model\\.live\\.debounce\\.300ms="userSearch"]') && 
                !e.target.closest('[wire\\:click*="selectUser"]') &&
                !e.target.closest('[wire\\:model\\.live\\.debounce\\.300ms="activitySearch"]') &&
                !e.target.closest('[wire\\:click*="selectActivity"]')) {
                @this.hideResults();
            }
        });
    </script>
</div>