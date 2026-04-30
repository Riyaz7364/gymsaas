<x-layouts.app>
    <div style="padding:24px;">
        <div class="gh-card">
            <div class="gh-card-header">
                <div>
                    <h3 class="gh-card-title">Body Stat Photos</h3>
                    @if(request('member_id'))
                        <p style="color:#6b7280; margin-top:4px;">Showing body stat photos for member ID {{ request('member_id') }}.</p>
                    @endif
                </div>
                <a href="{{ gym_route('gym.members.index') }}" class="gh-btn gh-btn-outline gh-btn-sm">Back to Members</a>
            </div>
            <div class="gh-card-body">
                @if($photos->count())
                    <div class="progress-gallery" style="display:grid; grid-gap:10px; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); grid-auto-rows:250px 150px; grid-auto-flow:dense;">
                        @foreach($photos as $photo)
                            <div class="item" style="overflow:hidden; border-radius:18px; box-shadow:0 18px 40px rgba(15,23,42,.12); background:#fff;">
                                <button type="button" onclick="openBodyStatPhoto(this)" style="all:unset; cursor:pointer; display:block; width:100%; height:100%;">
                                    <img
                                        src="{{ asset('storage/' . $photo->photo_path) }}"
                                        alt="{{ optional($photo->bodyStat->date)->format('d M Y') }} body stat photo"
                                        style="width:100%; height:100%; object-fit:cover; display:block;"
                                    />
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <div style="margin-top:20px; display:flex; justify-content:flex-end;">
                        {{ $photos->withQueryString()->links() }}
                    </div>
                @else
                    <div style="text-align:center; padding:60px 20px; color:#6b7280;">
                        <div style="font-size:50px; margin-bottom:12px;">📸</div>
                        <p style="font-size:16px; margin-bottom:10px;">No body stat photos available yet.</p>
                        <p style="max-width:420px; margin:0 auto;">Upload body stat photos from the member profile page and then return here to view them in a mobile-style gallery.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openBodyStatPhoto(button) {
            var img = button.querySelector('img');
            if (!img) return;
            var src = img.getAttribute('src');
            var alt = img.getAttribute('alt') || '';

            var overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed; inset:0; background:rgba(15,23,42,.88); display:flex; align-items:center; justify-content:center; padding:24px; z-index:100000;';
            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    overlay.remove();
                }
            });

            var frame = document.createElement('div');
            frame.style.cssText = 'position:relative; max-width:920px; width:100%; max-height:calc(100vh - 48px); background:#111; border-radius:24px; overflow:hidden; box-shadow:0 30px 70px rgba(0,0,0,.45);';

            var closeButton = document.createElement('button');
            closeButton.innerHTML = '×';
            closeButton.style.cssText = 'position:absolute; top:14px; right:14px; width:42px; height:42px; border:none; border-radius:50%; background:rgba(255,255,255,.18); color:#fff; font-size:24px; cursor:pointer; z-index:10;';
            closeButton.addEventListener('click', function () { overlay.remove(); });
            frame.appendChild(closeButton);

            var imageWrapper = document.createElement('div');
            imageWrapper.style.cssText = 'padding:18px; display:flex; align-items:center; justify-content:center; min-height:380px;';
            var image = document.createElement('img');
            image.src = src;
            image.alt = alt;
            image.style.cssText = 'max-width:100%; max-height:calc(100vh - 140px); object-fit:contain; border-radius:16px;';
            imageWrapper.appendChild(image);
            frame.appendChild(imageWrapper);

            if (alt) {
                var caption = document.createElement('div');
                caption.textContent = alt;
                caption.style.cssText = 'padding:14px 18px 20px; color:#e2e8f0; font-size:14px; background:#0f172a; text-align:center;';
                frame.appendChild(caption);
            }

            overlay.appendChild(frame);
            document.body.appendChild(overlay);
        }
    </script>
    <style>
        .progress-gallery .item img {
            width:100%;
            height:100%;
            object-fit:cover;
            transition:transform .2s ease, box-shadow .2s ease;
        }
        .progress-gallery .item:hover img {
            transform:scale(1.02);
            box-shadow:0 18px 36px rgba(15,23,42,.18);
        }
        .progress-gallery .item:first-child {
            grid-row: span 2;
            grid-column: span 2;
        }
        @media (min-width: 480px) {
            .progress-gallery .item:nth-child(3n) {
                grid-column: span 2;
            }
        }
    </style>
    @endpush
</x-layouts.app>
