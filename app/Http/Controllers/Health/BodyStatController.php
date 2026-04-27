<?php

namespace App\Http\Controllers\Health;

use App\Http\Controllers\Controller;
use App\Models\BodyStat;
use App\Models\BodyStatPhoto;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BodyStatController extends Controller
{
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $stats = BodyStat::where('gym_id', $gymId)
            ->with('member')
            ->orderByDesc('date')
            ->paginate(20);
        return view('body-stats.index', compact('stats'));
    }

    public function gallery(Request $request)
    {
        $gymId = auth()->user()->gym_id;
        $photos = BodyStatPhoto::with(['bodyStat.member'])
            ->whereHas('bodyStat', function ($query) use ($gymId) {
                $query->where('gym_id', $gymId);
            })
            ->when($request->member_id, function ($query, $memberId) {
                $query->whereHas('bodyStat', function ($query) use ($memberId) {
                    $query->where('member_id', $memberId);
                });
            })
            ->orderByDesc('created_at')
            ->paginate(24);

        return view('health.progress-photos.index', compact('photos'));
    }

    public function create()
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('body-stats.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'    => 'required|exists:members,id',
            'date'         => 'required|date',
            'weight'       => 'nullable|numeric|min:0',
            'height'       => 'nullable|numeric|min:50|max:250',
            'body_fat_pct' => 'nullable|numeric|min:0|max:100',
            'waist'        => 'nullable|numeric|min:0',
            'chest'        => 'nullable|numeric|min:0',
            'arms'         => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string',
            'photos'       => 'nullable|array',
            'photos.*'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        $data['gym_id'] = auth()->user()->gym_id;
        if (!empty($data['weight']) && !empty($data['height'])) {
            $h = $data['height'] / 100;
            $data['bmi'] = round($data['weight'] / ($h * $h), 1);
        }
        $bodyStat = BodyStat::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo && $photo->isValid()) {
                    $path = $photo->store('body-stats/photos', 'public');
                    BodyStatPhoto::create([
                        'body_stat_id' => $bodyStat->id,
                        'photo_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('body-stats.index')->with('success', 'Body stats recorded.');
    }

    public function edit(BodyStat $bodyStat)
    {
        $gymId   = auth()->user()->gym_id;
        $members = Member::where('gym_id', $gymId)->orderBy('name')->get();
        return view('body-stats.edit', ['stat' => $bodyStat, 'members' => $members]);
    }

    public function update(Request $request, BodyStat $bodyStat)
    {
        $data = $request->validate([
            'member_id'             => 'required|exists:members,id',
            'date'                  => 'required|date',
            'weight'                => 'nullable|numeric|min:0',
            'height'                => 'nullable|numeric|min:50|max:250',
            'body_fat_pct'          => 'nullable|numeric|min:0|max:100',
            'waist'                 => 'nullable|numeric|min:0',
            'chest'                 => 'nullable|numeric|min:0',
            'arms'                  => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string',
            'photos'                => 'nullable|array',
            'photos.*'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_existing_photos' => 'nullable|array',
            'delete_existing_photos.*' => 'nullable|integer|exists:body_stat_photos,id',
        ]);
        if (!empty($data['weight']) && !empty($data['height'])) {
            $h = $data['height'] / 100;
            $data['bmi'] = round($data['weight'] / ($h * $h), 1);
        }
        $bodyStat->update($data);

        if (!empty($data['delete_existing_photos'])) {
            $photosToDelete = BodyStatPhoto::whereIn('id', $data['delete_existing_photos'])
                ->where('body_stat_id', $bodyStat->id)
                ->get();
            foreach ($photosToDelete as $photo) {
                Storage::disk('public')->delete($photo->photo_path);
                $photo->delete();
            }
        }

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                if ($photo && $photo->isValid()) {
                    $path = $photo->store('body-stats/photos', 'public');
                    BodyStatPhoto::create([
                        'body_stat_id' => $bodyStat->id,
                        'photo_path' => $path,
                    ]);
                }
            }
        }

        return redirect()->route('body-stats.index')->with('success', 'Body stats updated.');
    }

    public function destroy(BodyStat $bodyStat)
    {
        $bodyStat->delete();
        return redirect()->route('body-stats.index')->with('success', 'Body stats deleted.');
    }
}
