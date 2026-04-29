<x-layouts.app>
    <x-slot:title>Edit Diet Plan — {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Edit Diet Plan</x-slot:header>
    <x-slot:topbarTitle>Diet & Nutrition</x-slot:topbarTitle>
    <x-slot:breadcrumb>Home / <a href="{{ route('diet-plans.index') }}" style="color:var(--gh-primary);text-decoration:none;">Diet Plans</a> / Edit</x-slot:breadcrumb>

    <div style="max-width:700px;">
        <div class="gh-card">
            <div class="gh-card-header">
                <h3 class="gh-card-title">Edit: {{ $dietPlan->name }}</h3>
                @if($dietPlan->is_default)
                <span class="gh-badge gh-badge-info">Template Plan</span>
                @else
                <span class="gh-badge gh-badge-success">Custom Plan</span>
                @endif
            </div>
            <div class="gh-card-body">
                @if($errors->any())
                <div class="gh-alert gh-alert-danger"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route('diet-plans.update', $dietPlan) }}">
                    @csrf @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div style="grid-column:1/-1;">
                            <label class="gh-label">Plan Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="name" class="gh-input" value="{{ old('name', $dietPlan->name) }}" placeholder="e.g., High Protein Bulk, Weight Loss 30 Days" required>
                            @error('name')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="gh-label">Goal <span style="color:#ef4444;">*</span></label>
                            <select name="goal" class="gh-input" required>
                                <option value="">— Select goal —</option>
                                @foreach(['weight_loss' => '🔥 Weight Loss', 'muscle_gain' => '💪 Muscle Gain', 'maintain' => '⚖️ Maintain', 'endurance' => '🏃 Endurance'] as $val => $label)
                                <option value="{{ $val }}" {{ old('goal', $dietPlan->goal) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('goal')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="gh-label">For Member (optional)</label>
                            <select name="member_id" class="gh-input">
                                <option value="">— No specific member —</option>
                                @foreach($members as $m)<option value="{{ $m->id }}" {{ old('member_id',$dietPlan->member_id)==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                            </select>
                            @error('member_id')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                        </div>
                        <div style="grid-column:1/-1;">
                            <label class="gh-label">Description</label>
                            <textarea name="description" class="gh-input" rows="3" placeholder="Add details about this plan...">{{ old('description', $dietPlan->description) }}</textarea>
                            @error('description')<div style="color:#ef4444;font-size:13px;">{{ $message }}</div>@enderror
                        </div>
                        <div style="grid-column:1/-1;background:#f9fafb;padding:12px;border-radius:6px;border-left:3px solid #3b82f6;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;">
                                <input type="checkbox" name="is_default" value="1" {{ old('is_default',$dietPlan->is_default) ? 'checked' : '' }}>
                                <span style="font-weight:600;color:#374151;">Save as Template</span>
                            </label>
                            <p style="margin:8px 0 0;padding-left:24px;color:#6b7280;font-size:13px;">
                                ✓ Check this to make it a reusable template for multiple members<br>
                                ✗ Leave unchecked to keep it as a custom plan for one member
                            </p>
                        </div>
                        <div style="grid-column:1/-1;background:#f0fdf4;padding:12px;border-radius:6px;border-left:3px solid #10b981;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin:0;">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active',$dietPlan->is_active) ? 'checked' : '' }}>
                                <span style="font-weight:600;color:#374151;">Active</span>
                            </label>
                            <p style="margin:8px 0 0;padding-left:24px;color:#6b7280;font-size:13px;">
                                ✓ Uncheck to deactivate this plan
                            </p>
                        </div>
                    </div>

                    {{-- Meals Section --}}
                    <div style="margin-top:24px;border-top:1px solid #e5e7eb;padding-top:24px;">
                        <h4 style="margin:0 0 16px;font-size:16px;font-weight:600;">🍽️ Meals & Nutrition</h4>
                        <p style="margin:0 0 20px;color:#6b7280;font-size:14px;">Edit meals in this diet plan by selecting from your <a href="{{ route('food-items.index') }}" style="color:var(--gh-primary);" target="_blank">Food & Drinks library</a> or enter manually.</p>

                        <div id="meals-container">
                            @foreach($dietPlan->meals as $index => $meal)
                            <div class="meal-item" style="border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px;background:#fafafa;">
                                <input type="hidden" name="meals[{{ $index }}][id]" value="{{ $meal->id }}">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                    <h5 style="margin:0;font-size:14px;font-weight:600;">Meal #{{ $index + 1 }}</h5>
                                    <button type="button" class="remove-meal-btn gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;display:{{ $dietPlan->meals->count() > 1 ? 'block' : 'none' }};" onclick="this.parentElement.parentElement.remove(); updateMealNumbers();">Remove Meal</button>
                                </div>

                                <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px;">
                                    <div>
                                        <label class="gh-label">Meal Name <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="meals[{{ $index }}][name]" class="gh-input" value="{{ $meal->name }}" placeholder="e.g., Breakfast, Lunch, Dinner" required>
                                    </div>
                                    <div>
                                        <label class="gh-label">Time</label>
                                        <input type="text" name="meals[{{ $index }}][time]" class="gh-input" value="{{ $meal->time }}" placeholder="8:00 AM">
                                    </div>
                                </div>

                                {{-- Food Items Selection --}}
                                <div class="food-items-section" style="margin-bottom:16px;">
                                    <label class="gh-label">Food Items</label>
                                    <div class="food-items-list" id="food-items-{{ $index }}">
                                        @if($meal->foods && is_array($meal->foods))
                                        @foreach($meal->foods as $foodIndex => $food)
                                        <div class="food-item-row" style="display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:12px;align-items:end;margin-bottom:8px;padding:8px;background:#fff;border-radius:4px;border:1px solid #e5e7eb;">
                                            <div>
                                                <label class="gh-label">Food Item</label>
                                                <select name="meals[{{ $index }}][food_items][{{ $foodIndex }}][food_item_id]" class="gh-input food-item-select" onchange="updateFoodItemData({{ $index }}, {{ $foodIndex }})" required>
                                                    <option value="">— Select food item —</option>
                                                    @foreach($foodItems as $item)
                                                    <option value="{{ $item->id }}" data-calories="{{ $item->calories }}" data-protein="{{ $item->protein_g }}" data-carbs="{{ $item->carbs_g }}" data-fat="{{ $item->fat_g }}" data-serving="{{ $item->serving_size }} {{ $item->serving_unit }}" {{ $food['food_item_id'] == $item->id ? 'selected' : '' }}>
                                                        {{ $item->name }} ({{ $item->category->name ?? 'Uncategorized' }})
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="gh-label">Quantity</label>
                                                <input type="number" name="meals[{{ $index }}][food_items][{{ $foodIndex }}][quantity]" class="gh-input quantity-input" placeholder="1" min="0.1" step="0.1" value="{{ $food['quantity'] ?? 1 }}" onchange="calculateMealNutrition({{ $index }})">
                                            </div>
                                            <div>
                                                <label class="gh-label">Serving Size</label>
                                                <input type="text" class="gh-input serving-size-display" readonly value="{{ $food['serving_size'] ?? '' }} {{ $food['serving_unit'] ?? '' }}">
                                            </div>
                                            <div>
                                                <button type="button" class="remove-food-item-btn gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;margin-top:20px;" onclick="this.parentElement.remove(); calculateMealNutrition({{ $index }});">×</button>
                                            </div>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                    <button type="button" class="add-food-item-btn gh-btn gh-btn-sm gh-btn-outline" style="margin-top:8px;" onclick="addFoodItem({{ $index }})">
                                        + Add Food Item
                                    </button>
                                </div>

                                {{-- Manual Entry Option --}}
                                <div style="margin-bottom:16px;">
                                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:8px;">
                                        <input type="checkbox" class="manual-entry-toggle" data-meal="{{ $index }}" onchange="toggleManualEntry({{ $index }})" {{ empty($meal->foods) ? 'checked' : '' }}>
                                        <span style="font-weight:500;">Or enter nutrition manually</span>
                                    </label>
                                    <div class="manual-entry" id="manual-entry-{{ $index }}" style="display:{{ empty($meal->foods) ? 'block' : 'none' }};">
                                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
                                            <div>
                                                <label class="gh-label">Calories</label>
                                                <input type="number" name="meals[{{ $index }}][total_calories]" class="gh-input manual-calories" placeholder="0" min="0" value="{{ $meal->total_calories }}">
                                            </div>
                                            <div>
                                                <label class="gh-label">Protein (g)</label>
                                                <input type="number" name="meals[{{ $index }}][protein_g]" class="gh-input manual-protein" placeholder="0.00" min="0" step="0.01" value="{{ $meal->protein_g }}">
                                            </div>
                                            <div>
                                                <label class="gh-label">Carbs (g)</label>
                                                <input type="number" name="meals[{{ $index }}][carbs_g]" class="gh-input manual-carbs" placeholder="0.00" min="0" step="0.01" value="{{ $meal->carbs_g }}">
                                            </div>
                                            <div>
                                                <label class="gh-label">Fat (g)</label>
                                                <input type="number" name="meals[{{ $index }}][fat_g]" class="gh-input manual-fat" placeholder="0.00" min="0" step="0.01" value="{{ $meal->fat_g }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Nutritional Summary --}}
                                <div class="nutrition-summary" style="background:#f0f9ff;border:1px solid #0ea5e9;border-radius:6px;padding:12px;">
                                    <h6 style="margin:0 0 8px;font-size:13px;font-weight:600;color:#0ea5e9;">📊 Nutritional Summary</h6>
                                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;font-size:13px;">
                                        <div><strong>Calories:</strong> <span id="total-calories-{{ $index }}">{{ $meal->total_calories ?? 0 }}</span></div>
                                        <div><strong>Protein:</strong> <span id="total-protein-{{ $index }}">{{ number_format($meal->protein_g ?? 0, 2) }}</span>g</div>
                                        <div><strong>Carbs:</strong> <span id="total-carbs-{{ $index }}">{{ number_format($meal->carbs_g ?? 0, 2) }}</span>g</div>
                                        <div><strong>Fat:</strong> <span id="total-fat-{{ $index }}">{{ number_format($meal->fat_g ?? 0, 2) }}</span>g</div>
                                    </div>
                                </div>

                                {{-- Hidden inputs for totals --}}
                                <input type="hidden" name="meals[{{ $index }}][calculated_calories]" id="hidden-calories-{{ $index }}" value="{{ $meal->total_calories ?? 0 }}">
                                <input type="hidden" name="meals[{{ $index }}][calculated_protein]" id="hidden-protein-{{ $index }}" value="{{ $meal->protein_g ?? 0 }}">
                                <input type="hidden" name="meals[{{ $index }}][calculated_carbs]" id="hidden-carbs-{{ $index }}" value="{{ $meal->carbs_g ?? 0 }}">
                                <input type="hidden" name="meals[{{ $index }}][calculated_fat]" id="hidden-fat-{{ $index }}" value="{{ $meal->fat_g ?? 0 }}">
                            </div>
                            @endforeach
                        </div>

                        <button type="button" id="add-meal-btn" class="gh-btn gh-btn-outline" style="margin-bottom:24px;">
                            + Add Another Meal
                        </button>
                    </div>

                    <div style="margin-top:24px;display:flex;gap:10px;">
                        <button type="submit" class="gh-btn gh-btn-primary">Save Changes</button>
                        <a href="{{ route('diet-plans.index') }}" class="gh-btn gh-btn-outline">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let mealIndex = {{ $dietPlan->meals->count() }};
        let foodItemIndex = {};

        // Initialize food item index for each meal
        function initMeal(mealId) {
            if (!foodItemIndex[mealId]) {
                foodItemIndex[mealId] = 0;
                // Count existing food items for this meal
                const existingItems = document.querySelectorAll(`#food-items-${mealId} .food-item-row`);
                foodItemIndex[mealId] = existingItems.length;
            }
        }

        // Add food item to a meal
        function addFoodItem(mealId) {
            initMeal(mealId);
            const container = document.getElementById(`food-items-${mealId}`);
            const itemIndex = foodItemIndex[mealId];

            const foodItemHtml = `
                <div class="food-item-row" style="display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:12px;align-items:end;margin-bottom:8px;padding:8px;background:#fff;border-radius:4px;border:1px solid #e5e7eb;">
                    <div>
                        <label class="gh-label">Food Item</label>
                        <select name="meals[${mealId}][food_items][${itemIndex}][food_item_id]" class="gh-input food-item-select" onchange="updateFoodItemData(${mealId}, ${itemIndex})" required>
                            <option value="">— Select food item —</option>
                            @foreach($foodItems as $foodItem)
                            <option value="{{ $foodItem->id }}" data-calories="{{ $foodItem->calories }}" data-protein="{{ $foodItem->protein_g }}" data-carbs="{{ $foodItem->carbs_g }}" data-fat="{{ $foodItem->fat_g }}" data-serving="{{ $foodItem->serving_size }} {{ $foodItem->serving_unit }}">{{ $foodItem->name }} ({{ $foodItem->category->name ?? 'Uncategorized' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="gh-label">Quantity</label>
                        <input type="number" name="meals[${mealId}][food_items][${itemIndex}][quantity]" class="gh-input quantity-input" placeholder="1" min="0.1" step="0.1" value="1" onchange="calculateMealNutrition(${mealId})">
                    </div>
                    <div>
                        <label class="gh-label">Serving Size</label>
                        <input type="text" class="gh-input serving-size-display" readonly>
                    </div>
                    <div>
                        <button type="button" class="remove-food-item-btn gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;margin-top:20px;" onclick="this.parentElement.remove(); calculateMealNutrition(${mealId});">×</button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', foodItemHtml);
            foodItemIndex[mealId]++;
        }

        // Update food item data when selection changes
        function updateFoodItemData(mealId, itemIndex) {
            const select = document.querySelector(`select[name="meals[${mealId}][food_items][${itemIndex}][food_item_id]"]`);
            const selectedOption = select.options[select.selectedIndex];
            const servingDisplay = select.closest('.food-item-row').querySelector('.serving-size-display');

            if (selectedOption.value) {
                const servingSize = selectedOption.getAttribute('data-serving');
                servingDisplay.value = servingSize;
            } else {
                servingDisplay.value = '';
            }

            calculateMealNutrition(mealId);
        }

        // Calculate nutrition for a meal
        function calculateMealNutrition(mealId) {
            let totalCalories = 0;
            let totalProtein = 0;
            let totalCarbs = 0;
            let totalFat = 0;

            const foodItemRows = document.querySelectorAll(`#food-items-${mealId} .food-item-row`);

            foodItemRows.forEach(row => {
                const select = row.querySelector('.food-item-select');
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;

                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    totalCalories += (parseFloat(selectedOption.getAttribute('data-calories')) || 0) * quantity;
                    totalProtein += (parseFloat(selectedOption.getAttribute('data-protein')) || 0) * quantity;
                    totalCarbs += (parseFloat(selectedOption.getAttribute('data-carbs')) || 0) * quantity;
                    totalFat += (parseFloat(selectedOption.getAttribute('data-fat')) || 0) * quantity;
                }
            });

            // Update display
            document.getElementById(`total-calories-${mealId}`).textContent = Math.round(totalCalories);
            document.getElementById(`total-protein-${mealId}`).textContent = totalProtein.toFixed(2);
            document.getElementById(`total-carbs-${mealId}`).textContent = totalCarbs.toFixed(2);
            document.getElementById(`total-fat-${mealId}`).textContent = totalFat.toFixed(2);

            // Update hidden inputs
            document.getElementById(`hidden-calories-${mealId}`).value = Math.round(totalCalories);
            document.getElementById(`hidden-protein-${mealId}`).value = totalProtein.toFixed(2);
            document.getElementById(`hidden-carbs-${mealId}`).value = totalCarbs.toFixed(2);
            document.getElementById(`hidden-fat-${mealId}`).value = totalFat.toFixed(2);

            // Update manual inputs if manual entry is enabled
            if (document.getElementById(`manual-entry-${mealId}`).style.display !== 'none') {
                document.querySelector(`input[name="meals[${mealId}][total_calories]"]`).value = Math.round(totalCalories);
                document.querySelector(`input[name="meals[${mealId}][protein_g]"]`).value = totalProtein.toFixed(2);
                document.querySelector(`input[name="meals[${mealId}][carbs_g]"]`).value = totalCarbs.toFixed(2);
                document.querySelector(`input[name="meals[${mealId}][fat_g]"]`).value = totalFat.toFixed(2);
            }
        }

        // Toggle manual entry
        function toggleManualEntry(mealId) {
            const checkbox = document.querySelector(`input[data-meal="${mealId}"].manual-entry-toggle`);
            const manualEntryDiv = document.getElementById(`manual-entry-${mealId}`);
            const foodItemsSection = document.querySelector(`#food-items-${mealId}`).closest('.food-items-section');

            if (checkbox.checked) {
                manualEntryDiv.style.display = 'block';
                foodItemsSection.style.display = 'none';
                // Copy calculated values to manual inputs
                const calories = document.getElementById(`hidden-calories-${mealId}`).value;
                const protein = document.getElementById(`hidden-protein-${mealId}`).value;
                const carbs = document.getElementById(`hidden-carbs-${mealId}`).value;
                const fat = document.getElementById(`hidden-fat-${mealId}`).value;

                document.querySelector(`input[name="meals[${mealId}][total_calories]"]`).value = calories;
                document.querySelector(`input[name="meals[${mealId}][protein_g]"]`).value = protein;
                document.querySelector(`input[name="meals[${mealId}][carbs_g]"]`).value = carbs;
                document.querySelector(`input[name="meals[${mealId}][fat_g]"]`).value = fat;
            } else {
                manualEntryDiv.style.display = 'none';
                foodItemsSection.style.display = 'block';
                calculateMealNutrition(mealId);
            }
        }

        // Update meal numbers
        function updateMealNumbers() {
            const mealItems = document.querySelectorAll('.meal-item');
            mealItems.forEach((item, index) => {
                const title = item.querySelector('h5');
                if (title) {
                    title.textContent = `Meal #${index + 1}`;
                }
                // Show remove button only if more than 1 meal
                const removeBtn = item.querySelector('.remove-meal-btn');
                if (removeBtn) {
                    removeBtn.style.display = mealItems.length > 1 ? 'block' : 'none';
                }
            });
        }

        // Add new meal
        document.getElementById('add-meal-btn').addEventListener('click', function() {
            const container = document.getElementById('meals-container');
            const mealHtml = `
                <div class="meal-item" style="border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:16px;background:#fafafa;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <h5 style="margin:0;font-size:14px;font-weight:600;">Meal #${mealIndex + 1}</h5>
                        <button type="button" class="remove-meal-btn gh-btn gh-btn-sm" style="background:#fee2e2;color:#dc2626;border:none;" onclick="this.parentElement.parentElement.remove(); updateMealNumbers();">Remove Meal</button>
                    </div>

                    <div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px;">
                        <div>
                            <label class="gh-label">Meal Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" name="meals[${mealIndex}][name]" class="gh-input" placeholder="e.g., Breakfast, Lunch, Dinner" required>
                        </div>
                        <div>
                            <label class="gh-label">Time</label>
                            <input type="text" name="meals[${mealIndex}][time]" class="gh-input" placeholder="8:00 AM">
                        </div>
                    </div>

                    <div class="food-items-section" style="margin-bottom:16px;">
                        <label class="gh-label">Food Items</label>
                        <div class="food-items-list" id="food-items-${mealIndex}"></div>
                        <button type="button" class="add-food-item-btn gh-btn gh-btn-sm gh-btn-outline" style="margin-top:8px;" onclick="addFoodItem(${mealIndex})">
                            + Add Food Item
                        </button>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;margin-bottom:8px;">
                            <input type="checkbox" class="manual-entry-toggle" data-meal="${mealIndex}" onchange="toggleManualEntry(${mealIndex})">
                            <span style="font-weight:500;">Or enter nutrition manually</span>
                        </label>
                        <div class="manual-entry" id="manual-entry-${mealIndex}" style="display:none;">
                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;">
                                <div>
                                    <label class="gh-label">Calories</label>
                                    <input type="number" name="meals[${mealIndex}][total_calories]" class="gh-input manual-calories" placeholder="0" min="0">
                                </div>
                                <div>
                                    <label class="gh-label">Protein (g)</label>
                                    <input type="number" name="meals[${mealIndex}][protein_g]" class="gh-input manual-protein" placeholder="0.00" min="0" step="0.01">
                                </div>
                                <div>
                                    <label class="gh-label">Carbs (g)</label>
                                    <input type="number" name="meals[${mealIndex}][carbs_g]" class="gh-input manual-carbs" placeholder="0.00" min="0" step="0.01">
                                </div>
                                <div>
                                    <label class="gh-label">Fat (g)</label>
                                    <input type="number" name="meals[${mealIndex}][fat_g]" class="gh-input manual-fat" placeholder="0.00" min="0" step="0.01">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nutrition-summary" style="background:#f0f9ff;border:1px solid #0ea5e9;border-radius:6px;padding:12px;">
                        <h6 style="margin:0 0 8px;font-size:13px;font-weight:600;color:#0ea5e9;">📊 Nutritional Summary</h6>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;font-size:13px;">
                            <div><strong>Calories:</strong> <span id="total-calories-${mealIndex}">0</span></div>
                            <div><strong>Protein:</strong> <span id="total-protein-${mealIndex}">0.00</span>g</div>
                            <div><strong>Carbs:</strong> <span id="total-carbs-${mealIndex}">0.00</span>g</div>
                            <div><strong>Fat:</strong> <span id="total-fat-${mealIndex}">0.00</span>g</div>
                        </div>
                    </div>

                    <input type="hidden" name="meals[${mealIndex}][calculated_calories]" id="hidden-calories-${mealIndex}" value="0">
                    <input type="hidden" name="meals[${mealIndex}][calculated_protein]" id="hidden-protein-${mealIndex}" value="0">
                    <input type="hidden" name="meals[${mealIndex}][calculated_carbs]" id="hidden-carbs-${mealIndex}" value="0">
                    <input type="hidden" name="meals[${mealIndex}][calculated_fat]" id="hidden-fat-${mealIndex}" value="0">
                </div>
            `;

            container.insertAdjacentHTML('beforeend', mealHtml);
            initMeal(mealIndex);
            mealIndex++;
            updateMealNumbers();
        });

        // Initialize existing meals
        @for($i = 0; $i < $dietPlan->meals->count(); $i++)
        initMeal({{ $i }});
        @endfor
        updateMealNumbers();
    </script>
</x-layouts.app>
