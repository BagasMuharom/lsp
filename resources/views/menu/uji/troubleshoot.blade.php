@extends('layouts.carbon.app', [
    'sidebar' => true
])

@push('css')
    <meta name="uji" content="{{ $uji->id }}">
@endpush

@section('title', 'Troubleshoot')

@section('content')

    @include('layouts.include.alert')

    @card()
        @slot('title', 'Detail')
            @row
                @col(['size' => 6])
                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>ID</b></label>
                    <div class="col-lg-8">
                        <p>{{ $uji->id }}</p>
                    </div>
                    @endformgroup

                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>Skema</b></label>
                    <div class="col-lg-8">
                        <p>{{ $uji->getSkema(false)->nama }}</p>
                    </div>
                    @endformgroup
        
                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>Nama Mahasiswa</b></label>
                    <div class="col-lg-8">
                        <p>{{ $uji->getMahasiswa(false)->nama }}</p>
                    </div>
                    @endformgroup
        
                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>Prodi</b></label>
                    <div class="col-lg-8">
                        <p>{{ $uji->getMahasiswa(false)->getProdi(false)->nama }}</p>
                    </div>
                    @endformgroup
        
                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>Jurusan</b></label>
                    <div class="col-lg-8">
                        <p>{{ $uji->getMahasiswa(false)->getJurusan(false)->nama }}</p>
                    </div>
                    @endformgroup
                @endcol

                @col(['size' => 6])
                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>Fakultas</b></label>
                    <div class="col-lg-8">
                        <p>{{ $uji->getMahasiswa(false)->getFakultas(false)->nama }}</p>
                    </div>
                    @endformgroup
        
                    @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-4"><b>Asesor</b></label>
                    <div class="col-lg-8">
                        @if($uji->hasAsesor())
                        <ol class="pl-3">
                        @foreach ($uji->getAsesorUji(false) as $asesor)
                            <li>{{ $asesor->nama }} {!! $loop->last ? '' : '<br/>' !!}</li>
                        @endforeach
                        </ol>
                        @else
                        -
                        @endif
                    </div>
                    @endformgroup
                    
                    @formgroup(['row' => true, 'class' => 'mt-3'])
                    <label class="col-lg-4"><b>Tanggal Uji</b></label>
                    <div class="col-lg-8">
                        {{ !is_null($uji->tanggal_uji) ? formatDate(Carbon\Carbon::parse($uji->tanggal_uji), true, false) : '-' }}
                    </div>
                    @endformgroup
        
                    @formgroup(['row' => true, 'class' => 'mb-0 mt-3'])
                    <label class="col-lg-4"><b>Status</b></label>
                    <div class="col-lg-8">
                        <span class="badge badge-{{ $uji->getStatus()['color'] }}" style="font-size: 15px">
                            {{ $uji->getStatus()['status'] }}
                        </span>
                    </div>
                    @endformgroup
                @endcol
            @endrow
    @endcard

    @row
        @col(['size' => 6])
            @card(['id' => 'penilaian_diri'])
            @slot('title', 'Penilaian Diri')

            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Memiliki data pada tabel <code>penilaian_diri</code></b></label>
                <div class="col-lg-6">
                    <p>{{ booleanPrint($uji->getPenilaianDiri()->count() > 0) }}</p>
                    <button class="btn btn-outline-danger btn-block" @click="inisialisasiUlangPenilaianDiri">Inisialisasi Ulang Penilaian Diri</button>
                </div>
            @endformgroup
            <hr>

            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Mahasiswa telah mengisi asesmen diri</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->hasPenilaianDiri()) }}
                    </p>
                    <button class="btn btn-outline-danger btn-block" @click="resetPenilaianDiri">Reset Penilaian Diri</button>
                </div>
            @endformgroup

            <hr>
            
            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Terdapat kolom yang belum diisi</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ $uji->getPenilaianDiri()->whereNull('nilai')->count() > 0 ? 'Ya' : 'Tidak' }}
                    </p>
                    <a target="_blank" class="btn btn-warning" href="{{ route('sertifikasi.asesmen.diri', ['uji' => encrypt($uji->id)]) }}">Isi Asesmen Diri (Mahasiswa)</a>
                </div>
            @endformgroup

            <hr>
            
            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Asesor telah mengisi asesmen diri</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->getPenilaianDiri()
                        ->whereNotNull('v')
                        ->orWhereNotNull('a')
                        ->orWhereNotNull('t')
                        ->orWhereNotNull('bukti')->count() > 0) }}
                    </p>
                    <a target="_blank" class="btn btn-warning" href="{{ route('uji.asesmendiri.asesor', ['uji' => encrypt($uji->id)]) }}">Isi Asesmen Diri (Asesor)</a>
                </div>
            @endformgroup

            <hr>
            
            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Terdapat kolom yang belum diisi oleh asesor</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->getPenilaianDiri()
                            ->whereNull('v')
                            ->WhereNull('a')
                            ->WhereNull('t')
                            ->WhereNull('bukti')->count() > 0) }}
                    </p>
                </div>
            @endformgroup

            <hr>
            
            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Asesor telah mengonfirmasi asesmen diri</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->konfirmasi_asesmen_diri)}}
                    </p>
                </div>
            @endformgroup

            <hr>

            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Jumlah kriteria pada skema (saat ini)</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ $uji->getSkema(false)->getKriteria(false)->count() }}
                    </p>
                </div>
            @endformgroup

            <hr>

            @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Jumlah kriteria pada penilaian diri</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ $uji->getPenilaianDiri()->count() }}
                    </p>
                </div>
            @endformgroup

        @endcard
        @endcol
        
        @col(['size' => 6])
            @card(['id' => 'penilaian'])
                @slot('title', 'Penilaian')

                @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Memiliki data pada tabel <code>penilaian</code></b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->getPenilaian()->count() > 0) }}
                    </p>

                    @if($uji->getPenilaian()->count() > 0)
                        <button class="btn btn-block btn-outline-danger" @click="hapusData">Hapus Data</button>
                    @endif

                    @if($uji->getPenilaian()->count() == 0)
                        <button class="mt-1 btn btn-block btn-outline-danger" @click="inisialisasiPenilaian">Inisialisasi Penilaian</button>
                    @endif

                    @if($uji->getPenilaian()->count() > 0)
                        <button class="mt-1 btn btn-block btn-outline-danger" @click="resetPenilaian">Reset Penilaian</button>
                    @endif
                </div>
                @endformgroup
                
                <hr>

                @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Terdapat kriteria yang belum diisi oleh asesor</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->getPenilaian()->whereNull('nilai')->count() > 0) }}
                    </p>
                    <a target="_blank" href="{{ route('penilaian.nilai', ['uji' => encrypt($uji->id)]) }}" class="btn btn-warning">Isi Penilaian</a>
                </div>
                @endformgroup
                
                <hr>

                @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Penilaian sudah dikonfirmasi</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->konfirmasi_penilaian_asesor) }}
                    </p>

                    @can('konfirmasiPenilaian', $uji)
                    <form action="{{ route('penilaian.konfirmasi', ['uji' => encrypt($uji->id)]) }}" method="POST">
                        @csrf
                        <button class="btn btn-success">Konfirmasi Penilaian</button>
                    </form>
                    @endif

                    @if($uji->konfirmasi_penilaian_asesor)
                    <form action="{{ route('penilaian.batalkan.konfirmasi', ['uji' => encrypt($uji->id)]) }}" method="POST">
                        @csrf
                        <button class="btn btn-success">Batalkan Konfirmasi</button>
                    </form>
                    @endif
                </div>
                @endformgroup

                <hr>

                @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Jumlah kriteria pada skema (saat ini)</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ $uji->getSkema(false)->getKriteria(false)->count() }}
                    </p>
                </div>
                @endformgroup

                <hr>
                
                @formgroup(['row' => true, 'class' => 'mb-0'])
                    <label class="col-lg-6"><b>Jumlah kriteria pada penilaian</b></label>
                    <div class="col-lg-6">
                        <p>
                            {{ $uji->getPenilaian()->count() }}
                        </p>
                    </div>
                @endformgroup
            @endcard

            {{-- @card(['id' => 'lainnya'])
                @slot('title', 'Lainnya')

                @formgroup(['row' => true, 'class' => 'mb-0'])
                <label class="col-lg-6"><b>Telah mengisi form MAK 04</b></label>
                <div class="col-lg-6">
                    <p>
                        {{ booleanPrint($uji->isMengisiMak4()) }}
                    </p>

                    @if($uji->isMengisiMak4())
                        <button class="btn btn-block btn-outline-danger" @click="resetMak4">Reset Isian Form</button>
                    @endif

                    <a href="{{ route('uji.isi.mak4', ['uji' => encrypt($uji->id)]) }}" class="btn btn-block btn-warning">Isi Kembali Form MAK 04</a>
                </div>
                @endformgroup
            @endcard --}}

            @card
                @slot('title', 'Status Kelulusan')

                <form action="{{ route('uji.ubah.status.kelulusan', ['uji' => encrypt($uji->id)]) }}" method="POST">

                    @csrf

                    @formgroup(['row' => true, 'class' => 'mb-0'])
                        <label class="col-lg-4">Status Kelulusan</label>
                        <div class="col-lg-8">
                            <select name="status" class="custom-select">
                                <option value="0" {{ $uji->lulus === false ? 'selected' : '' }}>Tidak Lulus</option>
                                <option value="1" {{ $uji->lulus === true ? 'selected' : '' }}>Lulus</option>
                                <option value="2" {{ $uji->lulus === null ? 'selected' : '' }}>NULL</option>
                            </select>

                            <button class="mt-3 btn btn-primary">Simpan</button>
                        </div>
                    @endformgroup
                </form>
            @endcard
        @endcol
    @endrow
@endsection

@push('js') 
<script>
new Vue({
    el: '#penilaian_diri',
    methods: {
        inisialisasiUlangPenilaianDiri: function (e) {
            swal({
                title: 'Apa anda yakin ?',
                text: 'Data sebelumnya akan hilang',
                buttons: {
                    confirm: {
                        text: 'Yakin',
                        closeModal: false
                    },
                    cancel: {
                        text: 'Batal',
                        visible: true
                    }
                },
                dangerMode: true
            }).then(function (confirm) {
                if (!confirm)
                    return

                axios.post('{{ route('uji.inisialisasi.ulang.penilaiandiri', ['uji' => encrypt($uji->id)]) }}')
                    .then(function (response) {
                        if (response.data.success) {
                            swal({
                                title: 'Berhasil !',
                                text: 'Berhasil mereset penilaian diri'
                            }).then(function () {
                                window.location.reload()
                            })
                        }
                    }).catch(function (error) {
                        if (error.response) {
                            if (error.response.status == 500) {
                                swal({
                                    title: 'Ups !',
                                    text: 'Terdapat gangguan pada server !',
                                    icon: 'danger'
                                })
                            }
                        }
                    })
        
            })
        },
        // End function

        resetPenilaianDiri: function (e) {
            e.preventDefault()

            swal({
                title: 'Apa anda yakin ?',
                text: 'Aksi ini tidak bisa dibatalkan',
                buttons: {
                    confirm: {
                        text: 'Yakin',
                        closeModal: false
                    },
                    cancel: {
                        text: 'Batal',
                        visible: true
                    }
                },
                dangerMode: true
            }).then(function (confirm) {
                if (!confirm)
                    return

                axios.post('{{ route('uji.reset.penilaiandiri', ['uji' => encrypt($uji->id)]) }}')
                    .then(function (response) {
                        if (response.data.success) {
                            swal({
                                title: 'Berhasil !',
                                text: 'Berhasil mereset penilaian diri'
                            }).then(function () {
                                window.location.reload()
                            })
                        }
                    }).catch(function (error) {
                        if (error.response) {
                            if (error.response.status == 500) {
                                swal({
                                    title: 'Ups !',
                                    text: 'Terdapat gangguan pada server !',
                                    icon: 'danger'
                                })
                            }
                        }
                    })
        
            })
        }
        // End function
    
    }
})

new Vue({
    el: '#penilaian',
    methods: {
        hapusData: function(e) {
            swal({
                title: 'Apa anda yakin ?',
                text: 'Aksi ini tidak bisa dibatalkan',
                buttons: {
                    confirm: {
                        text: 'Yakin',
                        closeModal: false
                    },
                    cancel: {
                        text: 'Batal',
                        visible: true
                    }
                },
                dangerMode: true
            }).then(function (confirm) {
                if (!confirm)
                    return

                axios.post('{{ route('uji.hapus.penilaian', ['uji' => encrypt($uji->id)]) }}')
                    .then(function (response) {
                        if (response.data.success) {
                            swal({
                                title: 'Berhasil !',
                                text: 'Berhasil mereset penilaian diri'
                            }).then(function () {
                                window.location.reload()
                            })
                        }
                    }).catch(function (error) {
                        if (error.response) {
                            if (error.response.status == 500) {
                                swal({
                                    title: 'Ups !',
                                    text: 'Terdapat gangguan pada server !',
                                    icon: 'danger'
                                })
                            }
                        }
                    })
        
            })
        },
        // end function

        inisialisasiPenilaian: function(e) {
            swal({
                title: 'Apa anda yakin ?',
                text: 'Aksi ini tidak bisa dibatalkan',
                buttons: {
                    confirm: {
                        text: 'Yakin',
                        closeModal: false
                    },
                    cancel: {
                        text: 'Batal',
                        visible: true
                    }
                },
                dangerMode: true
            }).then(function (confirm) {
                if (!confirm)
                    return

                axios.post('{{ route('uji.inisialisasi.ulang.penilaian', ['uji' => encrypt($uji->id)]) }}')
                    .then(function (response) {
                        if (response.data.success) {
                            swal({
                                title: 'Berhasil !',
                                text: 'Berhasil mereset penilaian diri'
                            }).then(function () {
                                window.location.reload()
                            })
                        }
                    }).catch(function (error) {
                        if (error.response) {
                            if (error.response.status == 500) {
                                swal({
                                    title: 'Ups !',
                                    text: 'Terdapat gangguan pada server !',
                                    icon: 'danger'
                                })
                            }
                        }
                    })
        
            })
        },
        // end function

        resetPenilaian: function (e) {
            e.preventDefault()

            swal({
                title: 'Apa anda yakin ?',
                text: 'Aksi ini tidak bisa dibatalkan',
                buttons: {
                    confirm: {
                        text: 'Yakin',
                        closeModal: false
                    },
                    cancel: {
                        text: 'Batal',
                        visible: true
                    }
                },
                dangerMode: true
            }).then(function (confirm) {
                if (!confirm)
                    return

                axios.post('{{ route('uji.reset.penilaian', ['uji' => encrypt($uji->id)]) }}')
                    .then(function (response) {
                        if (response.data.success) {
                            swal({
                                title: 'Berhasil !',
                                text: 'Berhasil mereset penilaian'
                            }).then(function () {
                                window.location.reload()
                            })
                        }
                    }).catch(function (error) {
                        swal({
                            title: 'Ups !',
                            text: 'Terdapat Gangguan'
                        })
                    })
            })
        },
        // end function
    }
})

new Vue({
    el: '#lainnya',
    methods: {
        resetMak4() {
            swal({
                title: 'Apa anda yakin ?',
                text: 'Aksi ini tidak bisa dibatalkan',
                buttons: {
                    confirm: {
                        text: 'Yakin',
                        closeModal: false
                    },
                    cancel: {
                        text: 'Batal',
                        visible: true
                    }
                },
                dangerMode: true
            }).then(function (confirm) {
                if (!confirm)
                    return

                axios.post('{{ route('uji.reset.mak4', ['uji' => encrypt($uji->id)]) }}')
                    .then(function (response) {
                        if (response.data.success) {
                            swal({
                                title: 'Berhasil !',
                                text: 'Berhasil mereset MAK 04'
                            }).then(function () {
                                window.location.reload()
                            })
                        }
                    }).catch(function (error) {
                        swal({
                            title: 'Ups !',
                            text: 'Terdapat Gangguan'
                        })
                    })
            })
        }
    }
})
</script>
@endpush