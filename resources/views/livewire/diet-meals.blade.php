<div>
    {{-- Totals bar --}}
    @if($totalKcal || $totalProt || $totalCarbs || $totalFat)
    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:16px;">
        @foreach([
            ['Calories','🔥',$totalKcal,'kcal','#ef4444'],
            ['Protein','💪',round($totalProt).'g','','#0abf8e'],
            ['Carbs','🌾',round($totalCarbs).'g','','#f97316'],
            ['Fat','🥑',round($totalFat).'g','','#8b5cf6']
        ] as [$l,$icon,$v,$unit,$col])
        @if($v !== '0' && $v !== '0g' && $v != 0)
        <div style="text-align:center; background:#f8fafc; border-radius:10px; padding:12px 6px;">
            <div style="font-size:18px; margin-bottom:2px;">{{ $icon }}</div>
            <div style="font-size:16px; font-weight:700; color:{{ $col }};">{{ $v }}{{ $unit }}</div>
            <div style="font-size:10px; color:#9ca3af; margin-top:2px;">{{ $l }}/day</div>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    {{-- Meals list --}}
    @if($meals->count())
    <div style="display:flex; flex-direction:column; gap:6px; margin-bottom:16px;">
        @foreach($meals as $meal)
        <div wire:key="meal-{{ $meal->id }}"
             style="display:flex; align-items:center; gap:12px; padding:10px 14px; background:#f8fafc; border-radius:8px; position:relative;">

            {{-- Row loading overlay --}}
            <div wire:loading.flex wire:target="deleteMeal({{ $meal->id }})"
                 style="position:absolute; inset:0; background:rgba(255,255,255,0.7); border-radius:8px;
                        align-items:center; justify-content:center; z-index:5;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                     style="animation:spin 0.8s linear infinite;">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
            </div>

            <div style="font-size:18px; flex-shrink:0;">🍽️</div>
            <div style="flex:1; min-width:0;">
                <div style="font-size:13px; font-weight:600; color:#111827;">{{ $meal->name }}</div>
                @if($meal->time)<div style="font-size:11px; color:#9ca3af; margin-top:1px;">🕐 {{ $meal->time }}</div>@endif
                @if($meal->foods && is_array($meal->foods) && count($meal->foods) > 0)
                <div style="margin-top:6px;">
                    @foreach($meal->foods as $food)
                    <div style="font-size:11px; color:#6b7280; margin-top:2px;">
                        • {{ $food['name'] ?? 'Unknown food' }}
                        @if(isset($food['quantity']) && $food['quantity'] > 1)
                        ({{ $food['quantity'] }}×)
                        @endif
                        @if(isset($food['serving_size']) && isset($food['serving_unit']))
                        - {{ $food['serving_size'] }} {{ $food['serving_unit'] }}
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            <div style="display:flex; gap:8px; align-items:center; flex-shrink:0;">
                @if($meal->total_calories)<span style="font-size:12px; font-weight:600; color:#ef4444;">{{ $meal->total_calories }} kcal</span>@endif
                @if($meal->protein_g)<span style="font-size:11px; color:#0abf8e; background:#f0fdf9; padding:2px 7px; border-radius:10px;">P {{ $meal->protein_g }}g</span>@endif
                @if($meal->carbs_g)<span style="font-size:11px; color:#f97316; background:#fff7ed; padding:2px 7px; border-radius:10px;">C {{ $meal->carbs_g }}g</span>@endif
                @if($meal->fat_g)<span style="font-size:11px; color:#8b5cf6; background:#f5f3ff; padding:2px 7px; border-radius:10px;">F {{ $meal->fat_g }}g</span>@endif
                <button wire:click="deleteMeal({{ $meal->id }})"
                        wire:confirm="Remove this meal?"
                        style="background:none; border:none; color:#d1d5db; cursor:pointer; font-size:14px; padding:2px 4px; line-height:1;"
                        onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#d1d5db'"
                        title="Remove">✕</button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p style="font-size:13px; color:#9ca3af; margin-bottom:14px;">No meals added yet. Use the form below.</p>
    @endif

    {{-- Inline add meal form --}}
    <div style="border-top:1px solid #e5e7eb; padding-top:14px;">
        <div style="font-size:12px; font-weight:700; color:#374151; margin-bottom:10px; text-transform:uppercase; letter-spacing:0.4px;">+ Add Meal</div>

        @error('name')<p style="font-size:12px; color:#ef4444; margin-bottom:6px;">{{ $message }}</p>@enderror

        <form wire:submit.prevent="addMeal">
            <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr 1fr auto; gap:8px; align-items:end;">
                <div>
                    <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Meal Name *</label>
                    <input wire:model="name" type="text" class="gh-input" placeholder="e.g. Oats with banana" style="font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Time</label>
                    <input wire:model="time" type="text" class="gh-input" placeholder="e.g. 8:00 AM" style="font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Calories</label>
                    <input wire:model="total_calories" type="number" class="gh-input" placeholder="kcal" min="0" style="font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Protein (g)</label>
                    <input wire:model="protein_g" type="number" class="gh-input" placeholder="g" step="0.1" min="0" style="font-size:13px;">
                </div>
                <div>
                    <label style="font-size:11px; color:#6b7280; display:block; margin-bottom:4px;">Carbs (g)</label>
                    <input wire:model="carbs_g" type="number" class="gh-input" placeholder="g" step="0.1" min="0" style="font-size:13px;">
                </div>
                <div style="padding-bottom:1px; position:relative;">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="addMeal"
                            class="gh-btn gh-btn-primary gh-btn-sm"
                            style="height:38px; padding:0 14px; position:relative;">
                        <span wire:loading.remove wire:target="addMeal">Add</span>
                        <span wire:loading.inline-flex wire:target="addMeal"
                              style="align-items:center; gap:5px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                 style="animation:spin 0.8s linear infinite; flex-shrink:0;">
                                <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                            </svg>
                            Adding…
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</div>
