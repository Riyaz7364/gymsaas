<x-trainer.layout>
    <x-slot:name>My Workout Plans</x-slot:name>
    <x-slot:header>Workout Plans</x-slot:header>
    <x-slot:subheader>Manage workout plans you have created for your members.</x-slot:subheader>

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">My Workouts</h2>
            <p class="text-sm text-gray-500">Only plans assigned to you are shown here.</p>
        </div>
        <a href="{{ route('trainer.workout-plans.create') }}" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">New Workout Plan</a>
    </div>

    @if($plans->count())
        <div class="space-y-4">
            @foreach($plans as $plan)
                <div class="rounded-2xl border border-gray-200 bg-white p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $plan->name }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $plan->description ?? 'No description' }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500">
                            <span class="rounded-full bg-gray-100 px-3 py-1">Member: {{ $plan->member?->name ?? 'Default' }}</span>
                            <span class="rounded-full bg-gray-100 px-3 py-1">Status: {{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('trainer.workout-plans.edit', $plan) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form method="POST" action="{{ route('trainer.workout-plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">{{ $plans->links() }}</div>
    @else
        <div class="rounded-2xl border border-gray-200 bg-white p-6 text-gray-600">No workout plans have been created yet.</div>
    @endif
</x-trainer.layout>
