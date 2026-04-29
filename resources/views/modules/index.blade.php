<x-layouts.app>
    <x-slot:title>Modules - {{ config('app.name') }}</x-slot:title>
    <x-slot:header>Modules</x-slot:header>
    <x-slot:topbarTitle>Module Access</x-slot:topbarTitle>
    <x-slot:breadcrumb>
        <span style="color:#0abf8e;">Home</span> / Modules
    </x-slot:breadcrumb>

    <div style="display:flex;flex-direction:column;gap:20px;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px 22px;">
            <div style="display:flex;justify-content:space-between;gap:16px;align-items:flex-start;flex-wrap:wrap;">
                <div>
                    <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0 0 6px;">{{ $gym?->name ?? 'Your Gym' }} Module Access</h2>
                    <p style="font-size:14px;color:#64748b;margin:0;">Each module is managed independently. A module can use shared gym data, but access is still controlled separately by its own module key.</p>
                </div>
                <div style="padding:10px 14px;border-radius:12px;background:#f8fafc;border:1px solid #e2e8f0;font-size:13px;color:#475569;">
                    Enabled: <strong style="color:#0f172a;">{{ $modules->flatten(1)->where('enabled', true)->count() }}</strong>
                    of {{ $modules->flatten(1)->count() }}
                </div>
            </div>
        </div>

        @foreach($modules as $groupName => $groupModules)
            <section style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;">
                <div style="padding:18px 22px;border-bottom:1px solid #f1f5f9;">
                    <h3 style="font-size:16px;font-weight:800;color:#0f172a;margin:0;">{{ $groupName }}</h3>
                </div>
                <div style="padding:20px 22px;display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
                    @foreach($groupModules as $module)
                        <div style="border:1px solid {{ $module['enabled'] ? '#bbf7d0' : '#e2e8f0' }};border-radius:14px;padding:16px;background:{{ $module['enabled'] ? '#f0fdf4' : '#f8fafc' }};">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:10px;">
                                <div style="font-size:15px;font-weight:800;color:#0f172a;">{{ $module['label'] }}</div>
                                <span style="padding:4px 9px;border-radius:999px;background:{{ $module['enabled'] ? '#dcfce7' : '#fee2e2' }};color:{{ $module['enabled'] ? '#166534' : '#991b1b' }};font-size:11px;font-weight:800;">
                                    {{ $module['enabled'] ? 'Enabled' : 'Locked' }}
                                </span>
                            </div>
                            <p style="font-size:13px;line-height:1.6;color:#64748b;margin:0 0 14px;">{{ $module['description'] }}</p>

                            @if($module['enabled'] && $module['entry_route'] && Route::has($module['entry_route']))
                                <a href="{{ route($module['entry_route']) }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 12px;background:#0abf8e;color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;">Open Module</a>
                            @elseif(!$module['enabled'])
                                <div style="font-size:12px;color:#991b1b;font-weight:700;">This module is not enabled for your current subscription.</div>
                            @else
                                <div style="font-size:12px;color:#64748b;font-weight:700;">This module is enabled and reserved for a dedicated workflow.</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-layouts.app>
