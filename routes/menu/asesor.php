<?php

Route::prefix('asesor')->group(function () {

    Route::namespace('Pages')->group(function () {

        Route::get('/', [
            'uses' => 'AsesorPageController@index',
            'as' => 'asesor'
        ]);

        Route::get('edit/{asesor}', [
            'uses' => 'AsesorPageController@edit',
            'as' => 'asesor.edit'
        ]);

        Route::get('daftaruji/{asesor}', [
            'uses' => 'AsesorPageController@daftarUji',
            'as' => 'asesor.daftar.uji'
        ]);

    });

    Route::post('edit/{asesor}', [
        'uses' => 'AsesorController@update',
        'as' => 'asesor.edit'
    ]);

    Route::prefix('berkas')->group(function () {

        Route::get('lihat/{asesor}/{dir}', [
            'uses' => 'AsesorController@lihatBerkas',
            'as' => 'asesor.berkas.lihat'
        ]);

        Route::post('unggah/{asesor}', [
            'uses' => 'AsesorController@unggahBerkas',
            'as' => 'asesor.berkas.unggah'
        ]);

        Route::delete('hapus', [
            'uses' => 'AsesorController@hapusBerkas',
            'as' => 'asesor.berkas.hapus'
        ]);

    });

});