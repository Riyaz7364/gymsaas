<x-trainer.layout>
    <x-slot:name>Trainer Dashboard</x-slot:name>
    <x-slot:header>{{ $trainer->name }}</x-slot:header>
    <x-slot:subheader>Welcome back. Review your assigned members, plans, and feedback.</x-slot:subheader>

    <div class="grid gap-6 lg:grid-cols-3 mb-8">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="text-sm uppercase tracking-wide text-gray-500">Assigned Members</div>
            <div class="mt-4 text-3xl font-semibold text-gray-900">{{ $stats['assigned_members'] }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="text-sm uppercase tracking-wide text-gray-500">Workout Plans</div>
            <div class="mt-4 text-3xl font-semibold text-gray-900">{{ $stats['workout_plans'] }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="text-sm uppercase tracking-wide text-gray-500">Renewals</div>
            <div class="mt-4 text-3xl font-semibold text-gray-900">{{ $stats['renewals'] }}</div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Assigned Members</h2>
                    <p class="text-sm text-gray-500">Members currently under your care.</p>
                </div>
                <a href="{{ route('trainer.workout-plans.index') }}" class="text-blue-600 hover:underline">Workouts</a>
            </div>
            @if($trainer->members->isEmpty())
                <p class="text-gray-500">No members assigned yet.</p>
            @else
                <div class="space-y-3">
                    @foreach($trainer->members as $member)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $member->name }}</div>
                                    <div class="text-sm text-gray-500">{{ ucfirst($member->status) }} member</div>
                                </div>
                                <div class="text-sm text-gray-500">{{ $member->activePlan?->plan->name ?? 'No active plan' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Recent Feedback</h2>
                    <p class="text-sm text-gray-500">Reviews from your clients.</p>
                </div>
                <div class="text-sm text-gray-500">{{ $stats['reviews'] }} reviews</div>
            </div>
            @if($trainer->reviews->isEmpty())
                <p class="text-gray-500">No reviews yet.</p>
            @else
                <div class="space-y-4">
                    @foreach($trainer->reviews->sortByDesc('created_at')->take(5) as $review)
                        <div class="rounded-xl border border-gray-200 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="font-medium text-gray-900">{{ $review->member->name ?? 'Member' }}</div>
                                <div class="text-sm text-gray-600">{{ $review->rating }}/5</div>
                            </div>
                            <p class="mt-2 text-sm text-gray-600">{{ $review->comment ?? 'No comment provided.' }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="mt-8 bg-white rounded-2xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Performance Events</h2>
        @if($trainer->memberHistories->isEmpty())
            <p class="text-gray-500">No performance records available yet.</p>
        @else
            <div class="space-y-3">
                @foreach($trainer->memberHistories->sortByDesc('occurred_at')->take(8) as $history)
                    <div class="rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center justify-between gap-4 text-sm text-gray-500">
                            <span>{{ ucfirst($history->action) }}</span>
                            <span>{{ $history->occurred_at?->format('M d, Y') ?? $history->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="mt-2 text-sm text-gray-700">{{ $history->notes ?? 'No extra details.' }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-trainer.layout>
