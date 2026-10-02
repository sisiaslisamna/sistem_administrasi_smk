<?php

namespace Database\Seeders;

use App\Models\JenisPembayaran;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ---- User ----
        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@sekolah.test',
            'password' => 'password',
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Kepala Sekolah',
            'username' => 'kepsek',
            'email'    => 'kepsek@sekolah.test',
            'password' => 'password',
            'role'     => 'kepala_sekolah',
        ]);

        // ---- Tahun ajaran ----
        $ta = TahunAjaran::create(['nama' => '2026/2027', 'aktif' => true]);
        
        // ---- Jenis pembayaran (hanya SPP) ----
        $spp = JenisPembayaran::create(['nama' => 'SPP', 'tipe' => 'bulanan', 'nominal' => 0]);
        $sppJurusan = ['DKV' => 220000, 'BC' => 220000, 'TB' => 250000, 'MP' => 200000];

        // ---- Data siswa (NIS, kelas, dan jurusan dalam satu daftar) ----
        // [nis, nama, kelas, jurusan, jk, tahun_masuk, alamat, no_telepon, email, nama_wali, no_telepon_wali, alamat_wali]
        $daftarSiswa = [
            ['26010001', 'Shinichi Kudo',      'X DKV',   'Desain Komunikasi Visual', 'L', 2026, 'Jln Tokyo', '082113071878', 'shinichikudo@stb.sch.id',      'Yusaku Kudo',       '081514117646', 'Jln Tokyo'],
            ['26020001', 'Kaito Kid',          'X BC',    'Broadcasting',             'L', 2026, 'Jln Tokyo', '082113071878', 'kaitokid@stb.sch.id',          'Teuchi Kuroba',     '081514117646', 'Jln Tokyo'],
            ['26030001', 'Ran Mouri',          'X TB',    'Tata Boga',                'P', 2026, 'Jln Tokyo', '082113071878', 'ranmouri@stb.sch.id',          'Kogoro Mouri',      '081514117646', 'Jln Tokyo'],
            ['26040001', 'Ai Haibara',         'X MP',    'Manajemen Perkantoran',    'P', 2026, 'Jln Tokyo', '082113071878', 'aihaibara@stb.sch.id',         'Agase',             '081514117646', 'Jln Tokyo'],
            ['25010001', 'Kiyotaka Ayanokoji', 'XI DKV',  'Desain Komunikasi Visual', 'L', 2025, 'Jln Kyoto', '082113071878', 'kiyotakaayanokoji@stb.sch.id', 'Atsuomi Ayanokoji', '081514117646', 'Jln Kyoto'],
            ['25020001', 'Kyo Ishigami',       'XI BC',   'Broadcasting',             'L', 2025, 'Jln Tokyo', '082113071878', 'kyoishigami@stb.sch.id',       'Ishigami',          '081514117646', 'Jln Tokyo'],
            ['25030001', 'Suzune Horikita',    'XI TB',   'Tata Boga',                'P', 2025, 'Jln Tokyo', '082113071878', 'suzunehorikita@stb.sch.id',    'Manabu Horikita',   '081514117646', 'Jln Tokyo'],
            ['25040001', 'Arisu Sakayanagi',   'XI MP',   'Manajemen Perkantoran',    'P', 2025, 'Jln Tokyo', '082113071878', 'arisusakayanagi@stb.sch.id',   'Sakayanagi',        '081514117646', 'Jln Tokyo'],
            ['24010001', 'Yukina Minato',      'XII DKV', 'Desain Komunikasi Visual', 'P', 2024, 'Jln Tokyo', '082113071878', 'yukinaminato@stb.sch.id',      'Minato',            '081514117646', 'Jln Tokyo'],
            ['24020001', 'Sayo Hikawa',        'XII BC',  'Broadcasting',             'P', 2024, 'Jln Tokyo', '082113071878', 'sayohikawa@stb.sch.id',        'Hikawa',            '081514117646', 'Jln Tokyo'],
            ['24030001', 'Lisa Imai',          'XII TB',  'Tata Boga',                'P', 2024, 'Jln Tokyo', '082113071878', 'lisaimai@stb.sch.id',          'Imai',              '081514117646', 'Jln Tokyo'],
            ['24040001', 'Rinko Shirokane',    'XII MP',  'Manajemen Perkantoran',    'P', 2024, 'Jln Tokyo', '082113071878', 'rinkoshirokane@stb.sch.id',    'Shirokane',         '081514117646', 'Jln Tokyo'],
        ];

        // ---- Jurusan dan kelas dibuat dari daftar siswa di atas ----
        $tingkatKelas = ['X' => 10, 'XI' => 11, 'XII' => 12];
        $jurusan = [];
        $kelas = [];

        foreach ($daftarSiswa as [, , $namaKelas, $namaJurusan]) {
            [$romawi, $kode] = explode(' ', $namaKelas);   // "XI DKV" -> "XI" dan "DKV"

            $jurusan[$kode] ??= Jurusan::create([
                'kode' => $kode,
                'nama' => $namaJurusan,
                'spp'  => $sppJurusan[$kode],
            ]);

            $kelas[$namaKelas] ??= Kelas::create([
                'jurusan_id' => $jurusan[$kode]->id,
                'tingkat'    => $tingkatKelas[$romawi],
                'nama'       => $namaKelas,
            ]);
        }

        // ---- Siswa, tagihan, dan pembayaran ----
        $admin = User::where('role', 'admin')->first();

        foreach ($daftarSiswa as $i => [$nis, $nama, $namaKelas, , $jk, $tahunMasuk, $alamat, $telp, $email, $namaWali, $telpWali, $alamatWali]) {
            $siswa = Siswa::create([
                'nis'             => $nis,
                'nama'            => $nama,
                'jenis_kelamin'   => $jk,
                'kelas_id'        => $kelas[$namaKelas]->id,
                'tahun_masuk'     => $tahunMasuk,
                'alamat'          => $alamat,
                'no_telepon'      => $telp,
                'email'           => $email,
                'nama_wali'       => $namaWali,
                'no_telepon_wali' => $telpWali,
                'alamat_wali'     => $alamatWali,
                'status'          => 'aktif',
            ]);

            foreach (['Juli' => 7, 'Agustus' => 8, 'September' => 9] as $bulan => $noBulan) {
                $nominal = $jurusan[explode(' ', $namaKelas)[1]]->spp;
                
                $tagihan = Tagihan::create([
                    'siswa_id'            => $siswa->nis,
                    'jenis_pembayaran_id' => $spp->id,
                    'tahun_ajaran_id'     => $ta->id,
                    'periode'             => $bulan,
                    'jumlah'              => $nominal,
                    'jatuh_tempo'         => now()->startOfYear()->setMonth($noBulan)->day(10),
                    ]);
                    
                    //Sebagian siswa lunas, sebagian cicil (hanya bulan Juli), sebagian belum bayar//
                    if ($i % 3 === 0) {
                        Pembayaran::create(['tagihan_id' => $tagihan->id, 'user_id' => $admin->id, 'tanggal_bayar' => now(), 'jumlah_bayar' => $nominal]);
                        } elseif ($i % 3 === 1 && $bulan === 'Juli') {
                            Pembayaran::create(['tagihan_id' => $tagihan->id, 'user_id' => $admin->id, 'tanggal_bayar' => now(), 'jumlah_bayar' => (int) ($nominal / 2)]);
                        }
                        
                $tagihan->refreshStatus();
            }
        }
    }
}