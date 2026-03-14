<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $nama_alat
 * @property string|null $kode_aspak
 * @property string|null $merk
 * @property string|null $tipe
 * @property string|null $no_seri
 * @property int $ruangan_id
 * @property string|null $lokasi_detail
 * @property string|null $tahun_perolehan
 * @property string $kondisi
 * @property string|null $foto
 * @property string|null $sertifikat
 * @property bool $status_kalibrasi
 * @property \Illuminate\Support\Carbon|null $tgl_kalibrasi_terakhir
 * @property \Illuminate\Support\Carbon|null $tgl_kalibrasi_selanjutnya
 * @property \Illuminate\Support\Carbon|null $tgl_kadaluarsa
 * @property string|null $kategori_utilitas
 * @property string|null $catatan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kalibrasi> $kalibrasis
 * @property-read int|null $kalibrasis_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LaporanKerusakan> $laporan_kerusakans
 * @property-read int|null $laporan_kerusakans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pemeliharaan> $pemeliharaans
 * @property-read int|null $pemeliharaans_count
 * @property-read \App\Models\PenghapusanAsset|null $penghapusan
 * @property-read \App\Models\Ruangan $ruangan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCatatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereFoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereKategoriUtilitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereKodeAspak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereKondisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereLokasiDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereMerk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereNamaAlat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereNoSeri($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereRuanganId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereSertifikat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereStatusKalibrasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTahunPerolehan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTglKadaluarsa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTglKalibrasiSelanjutnya($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTglKalibrasiTerakhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTipe($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 */
	class Asset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property string $tgl_kalibrasi
 * @property string $tgl_kadaluarsa
 * @property string $pelaksana
 * @property string|null $no_sertifikat
 * @property string $hasil
 * @property string|null $file_sertifikat
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereFileSertifikat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereHasil($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereNoSertifikat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi wherePelaksana($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereTglKadaluarsa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereTglKalibrasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kalibrasi whereUpdatedAt($value)
 */
	class Kalibrasi extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property string $pelapor
 * @property string $deskripsi_kerusakan
 * @property string $status
 * @property \Illuminate\Support\Carbon $tgl_lapor
 * @property \Illuminate\Support\Carbon|null $tgl_selesai
 * @property string|null $tindakan_perbaikan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereDeskripsiKerusakan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan wherePelapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereTglLapor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereTglSelesai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereTindakanPerbaikan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LaporanKerusakan whereUpdatedAt($value)
 */
	class LaporanKerusakan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property string $tanggal_servis
 * @property string $jenis_tindakan
 * @property string|null $teknisi
 * @property string|null $detail_pekerjaan
 * @property int $biaya
 * @property string|null $file_nota
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereBiaya($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereDetailPekerjaan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereFileNota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereJenisTindakan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereTanggalServis($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereTeknisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceLog whereUpdatedAt($value)
 */
	class MaintenanceLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset|null $asset
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MfkPemeliharaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MfkPemeliharaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MfkPemeliharaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MfkPemeliharaan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MfkPemeliharaan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MfkPemeliharaan whereUpdatedAt($value)
 */
	class MfkPemeliharaan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $jenis_utilitas
 * @property \Illuminate\Support\Carbon $tgl_cek
 * @property \Illuminate\Support\Carbon $waktu_cek
 * @property string $status
 * @property string $petugas
 * @property array<array-key, mixed>|null $check_listrik
 * @property array<array-key, mixed>|null $detail_apar
 * @property int $air_jernih
 * @property int $air_tidak_berbau
 * @property int $pompa_aman
 * @property int|null $tekanan_oksigen
 * @property string|null $stok_cadangan
 * @property numeric|null $debit_air_limbah
 * @property string|null $kondisi_bak
 * @property string $parameter_cek
 * @property string|null $foto_bukti
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $status_color
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk masalahHariIni()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereAirJernih($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereAirTidakBerbau($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereCheckListrik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereDebitAirLimbah($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereDetailApar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereFotoBukti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereJenisUtilitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereKondisiBak($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereParameterCek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk wherePetugas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk wherePompaAman($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereStokCadangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereTekananOksigen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereTglCek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitoringMfk whereWaktuCek($value)
 */
	class MonitoringMfk extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property string $kegiatan
 * @property string $tgl_jadwal
 * @property string|null $tgl_realisasi
 * @property string|null $petugas
 * @property string $status
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereKegiatan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan wherePetugas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereTglJadwal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereTglRealisasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemeliharaan whereUpdatedAt($value)
 */
	class Pemeliharaan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property \Illuminate\Support\Carbon $tgl_penghapusan
 * @property string $alasan
 * @property string|null $no_sk
 * @property string $metode
 * @property string|null $keterangan
 * @property string|null $foto_bukti
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereAlasan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereFotoBukti($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereMetode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereNoSk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereTglPenghapusan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PenghapusanAsset whereUpdatedAt($value)
 */
	class PenghapusanAsset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $nama_ruangan
 * @property string|null $lantai
 * @property string|null $kode_ruangan
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read mixed $total_assets
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereKodeRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereLantai($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereNamaRuangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ruangan whereUpdatedAt($value)
 */
	class Ruangan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $lokasi
 * @property string $ukuran
 * @property int $jumlah_masuk
 * @property int $jumlah_keluar
 * @property int $stok_akhir
 * @property string $petugas
 * @property string|null $keterangan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen tabungBesar()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen tabungKecil()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereJumlahKeluar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereJumlahMasuk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereKeterangan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereLokasi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen wherePetugas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereStokAkhir($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereUkuran($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StokOksigen whereUpdatedAt($value)
 */
	class StokOksigen extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Filament\Models\Contracts\FilamentUser {}
}

