@extends('layouts.carbon.app', [
    'sidebar' => true
])

@section('title', $menu->nama.' > Elemen | '.kustomisasi('nama'))

@section('content')
    @include('layouts.include.alert')

    @card()
    @slot('title')
        Daftar elemen dari unit <b>{{ $unit->kode }} ({{ $unit->nama }})</b>
    @endslot

    @alert(['type' => 'info'])
    Terdapat {{ $data->count() }} elemen
    @endalert

    @slot('table')
    <table class="table">
        <thead>
        <tr>
            <th width="4%">No</th>
            <th width="55%">Nama</th>
            <th width="15%">Benchmark</th>
            <th width="18%">Jumlah Kriteria</th>
            <th width="18%">Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($data as $elemen)
            <tr>
                <td>
                    {{ ++$no }}
                </td>
                <td>
                    <input type="text" class="form-control" value="{{ $elemen->nama }}" id="nama-elemen-{{ $elemen->id }}" required>
                </td>
                <td>
                    <input type="text" class="form-control" value="{{ $elemen->benchmark }}" id="benchmark-elemen-{{ $elemen->id }}" required>
                </td>
                <td>
                    {{ $elemen->getKriteria()->count() }}
                </td>
                <td>
                    <div class="btn-group">
                        <button onclick="event.preventDefault(); update('{{ $ei = encrypt($elemen->id) }}', $('#nama-elemen-{{ $elemen->id }}').val(), $('#benchmark-elemen-{{ $elemen->id }}').val())" class="btn btn-success btn-sm">Simpan</button>
                        <a href="" href="#" id="show_{{ $elemen->id }}" class="btn btn-info btn-sm">Lihat Kriteria</a>
                        <button onclick="event.preventDefault(); hapus('{{ $ei }}')" class="btn btn-danger btn-sm">Hapus</button>
                    </div>
                </td>
            </tr>
            <tr id="extra_{{ $elemen->id }}" style="display: none">
                <td colspan="4">
                    <form action="{{ route('unit.elemen.kriteria.update') }}" method="post">
                        @csrf
                        <input type="hidden" name="elemen_id" value="{{ encrypt($elemen->id) }}">
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Kriteria Unjuk Kerja</th>
                                <th>Pertanyaan</th>
                                <th>Kalimat Aktif</th>
                                <th>Aksi</th>
                            </tr>
                            </thead>
                            <tbody id="listkriteria-{{ $elemen->id }}">
                            @foreach($elemen->getKriteria()->orderBy('id')->get() as $kriteria)
                                <tr>
                                    <th>
                                        <textarea name="unjuk_kerja[]" class="form-control" rows="4"
                                                  required>{{ $kriteria->unjuk_kerja }}</textarea>
                                    </th>
                                    <th>
                                        <textarea name="pertanyaan[]" class="form-control" rows="4"
                                                  readonly>{{ $kriteria->pertanyaan }}</textarea>
                                    </th>
                                    <th>
                                        <textarea name="kalimat_aktif[]" class="form-control" rows="4"
                                                  required>{{ $kriteria->kalimat_aktif }}</textarea>
                                    </th>
                                    <th>
                                        <input type="hidden" name="kriteria_id[]" value="{{ $kriteria->id }}">
                                        <button class="btn btn-danger btn-sm"
                                                onclick="$(this).parent().parent().remove()">Hapus
                                        </button>
                                    </th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="btn-group">
                            <button class="btn btn-info" type="button"
                                    onclick="event.preventDefault();tambah('#listkriteria-{{ $elemen->id }}')">Tambah
                            </button>
                            <button class="btn btn-success" type="submit">
                                Simpan
                            </button>
                        </div>
                        </div>
                    </form>
                </td>
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td>
                <form action="{{ route('unit.elemen.add') }}" method="post" id="formtambahelemen">
                    @csrf
                    @method('put')
                    <input name="unit_id" type="hidden" value="{{ $unit->id }}">
                    <textarea class="form-control" name="namas" rows="5" placeholder="Pisahkan dengan enter untuk menambahkan banyak elemen..." required></textarea>
                </form>
            </td>
            <td><button class="btn btn-info btn-sm" onclick="event.preventDefault(); $('#formtambahelemen').submit()">Tambahkan elemen baru</button></td>
        </tr>
        </tbody>
    </table>
    @endslot
    @endcard

    <form action="{{ route('unit.elemen.update') }}" method="post" id="update">
        @csrf
        {{ method_field('patch') }}
        <input type="hidden" name="id" id="update_id" required>
        <input type="hidden" name="nama" id="update_nama" required>
        <input type="hidden" name="benchmark" id="update_benchmark" required>
    </form>

    <form id="hapus" action="{{ route('unit.elemen.delete') }}" method="post">
        @csrf
        {{ method_field('delete') }}
        <input type="hidden" name="id" id="id-delete">
    </form>
@endsection

@push('js')
    <script>
        $("a[id^=show_]").click(function (event) {
            $("#extra_" + $(this).attr('id').substr(5)).toggle();
            if ($("#extra_" + $(this).attr('id').substr(5)).is(":visible")) {
                $(this).text('Tutup kriteria');
            }
            else {
                $(this).text('Lihat kriteria');
            }
            event.preventDefault();
        });

        function tambah(id) {
            $(id).append('' +
                '<tr>\n' +
                '    <th>\n' +
                '        <textarea name="unjuk_kerja_baru[]" class="form-control" rows="4" required></textarea>\n' +
                '    </th>\n' +
                '    <th>\n' +
                '        <textarea name="pertanyaan_baru[]" class="form-control" rows="4" readonly></textarea>\n' +
                '    </th>\n' +
                '    <th>\n' +
                '        <textarea name="kalimat_aktif_baru[]" class="form-control" rows="4" required></textarea>\n' +
                '    </th>\n' +
                '    <th>\n' +
                '        <button class="btn btn-danger btn-sm" onclick="$(this).parent().parent().remove()">Hapus</button>\n' +
                '    </th>\n' +
                '</tr>' +
                '')
        }

        function update(id, nama, benchmark) {
            $('#update_id').val(id)
            $('#update_nama').val(nama)
            $('#update_benchmark').val(benchmark)
            $('#update').submit()
        }

        function hapus(id) {
            swal({
                title: "Anda yakin ingin menghapus elemen tersebut ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((choice) => {
                if (choice) {
                    swal('Sedang memuat. . .',{
                        buttons:false ,
                        closeOnClickOutside: false
                    })
                    $('#id-delete').val(id)
                    $('#hapus').submit()
                }
            })
        }
    </script>
@endpush