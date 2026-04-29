<x-trainer.layout>
    <x-slot:name>New Workout Plan</x-slot:name>
    <x-slot:header>New Workout Plan</x-slot:header>
    <x-slot:subheader>Create a workout plan for one of your assigned members.</x-slot:subheader>

    <div class="bg-white rounded-2xl border border-gray-200 p-6 max-w-3xl">
        @if($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('trainer.workout-plans.store') }}">
            @csrf
            <div class="grid gap-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                    <input name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Member</label>
                    <select name="member_id" class="w-full rounded-xl border border-gray-300 px-4 py-2">
                        <option value="">Default plan (no member)</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="4" class="w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        Mark as default workout
                    </label>
                </div>

                <div class="space-y-2">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                        Active
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-2 text-white hover:bg-blue-700">Create Plan</button>
                <a href="{{ route('trainer.workout-plans.index') }}" class="rounded-xl border border-gray-300 px-5 py-2 text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
</x-trainer.layout>
