@row

@col(['size' => 4])
    <div class="card p-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="h4 d-block font-weight-normal mb-2">{{ GlobalAuth::user()->getUji()->filterByStatus(App\Models\Uji::MEMILIKI_SERTIFIKAT)->count() }}</span>
                <span class="font-weight-light">Sertifikat Saya</span>
            </div>

            <div class="h2 text-muted">
                <i class="icon icon-people"></i>
            </div>
        </div>
    </div>
@endcol

@col(['size' => 4])
    <div class="card p-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="h4 d-block font-weight-normal mb-2">{{ GlobalAuth::user()->getUji()->count() }}</span>
                <span class="font-weight-light">Total Uji Kompetensi</span>
            </div>

            <div class="h2 text-muted">
                <i class="icon icon-people"></i>
            </div>
        </div>
    </div>
@endcol

@col(['size' => 4])
    <div class="card p-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="h4 d-block font-weight-normal mb-2">{{ GlobalAuth::user()->getUji()->filterByStatus(App\Models\Uji::LULUS)->count() }}</span>
                <span class="font-weight-light">Lulus Uji Kompetensi</span>
            </div>

            <div class="h2 text-muted">
                <i class="icon icon-people"></i>
            </div>
        </div>
    </div>
@endcol

@col(['size' => 4])
    <div class="card p-3">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="h4 d-block font-weight-normal mb-2">{{ GlobalAuth::user()->getUji()->filterByStatus(App\Models\Uji::TIDAK_LULUS)->count() }}</span>
                <span class="font-weight-light">Tidak Lulus Uji Kompetensi</span>
            </div>

            <div class="h2 text-muted">
                <i class="icon icon-people"></i>
            </div>
        </div>
    </div>
@endcol

@endrow