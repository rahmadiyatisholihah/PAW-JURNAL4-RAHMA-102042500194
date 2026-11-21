<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\MataKuliah;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Dosen Pengampu Default
        $dosen = User::updateOrCreate(
            ['email' => 'dosen@celoe.test'],
            [
                'name' => 'Dr. Ir. Budi Santoso, M.T.',
                'password' => Hash::make('dosen123'),
                'role' => 'dosen',
                'nomor_induk' => '198501012015041001',
            ]
        );

        // 2. Akun Mahasiswa Default
        $mahasiswa1 = User::updateOrCreate(
            ['email' => 'mahasiswa@celoe.test'],
            [
                'name' => 'Ahmad Pratama',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210001',
            ]
        );

        // 3. Mata Kuliah Dasar (Modul 3)
        $course1 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF301'],
            [
                'nama_mk' => 'Pengembangan Aplikasi Website',
                'deskripsi' => 'Mata kuliah yang membahas arsitektur aplikasi web modern menggunakan framework Laravel, MVC, Eloquent ORM, dan keamanan web.',
                'sks' => 4,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 4. Daftarkan Mahasiswa ke Mata Kuliah Pertama (status approved)
        $course1->students()->syncWithoutDetaching([
            $mahasiswa1->id => ['status' => 'approved'],
        ]);

        // 5. Tambahkan Materi Contoh
        Material::updateOrCreate(
            ['course_id' => $course1->id, 'minggu_ke' => 1, 'urutan' => 1],
            [
                'judul' => 'Pengenalan Arsitektur MVC & Routing di Laravel 12',
                'konten' => 'Mempelajari alur HTTP Request, Route Middleware, Controller, dan pembuatan View menggunakan Blade Template.',
            ]
        );

        // TODO 8: Tambahkan minimal 2 mata kuliah lagi dengan semester berbeda, 1 mahasiswa tambahan, 2 tugas, dan contoh data submission
        // 6. Akun Mahasiswa Tambahan
        $mahasiswa2 = User::updateOrCreate(
            ['email' => 'siti@celoe.test'],
            [
                'name' => 'Siti Nurhaliza',
                'password' => Hash::make('mahasiswa123'),
                'role' => 'mahasiswa',
                'nomor_induk' => '1301210002',
            ]
        );

        // 7. Tambahkan Mata Kuliah Kedua
        $course2 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF204'],
            [
                'nama_mk' => 'Basis Data',
                'deskripsi' => 'Mempelajari konsep database, perancangan ERD, relasi tabel, dan penggunaan SQL.',
                'sks' => 3,
                'semester' => 'Ganjil 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 8. Tambahkan Mata Kuliah Ketiga
        $course3 = MataKuliah::updateOrCreate(
            ['kode_mk' => 'IF402'],
            [
                'nama_mk' => 'Analisis dan Perancangan Sistem Informasi',
                'deskripsi' => 'Mempelajari analisis kebutuhan sistem dan perancangan sistem informasi.',
                'sks' => 2,
                'semester' => 'Genap 2026/2027',
                'dosen_id' => $dosen->id,
            ]
        );

        // 9. Daftarkan Mahasiswa ke Seluruh Mata Kuliah
        foreach ([$course1, $course2, $course3] as $course) {
            $course->students()->syncWithoutDetaching([
                $mahasiswa1->id => ['status' => 'approved'],
                $mahasiswa2->id => ['status' => 'approved'],
            ]);
        }

        // 10. Tambahkan Tugas Pertama
        $assignment1 = Assignment::updateOrCreate(
            [
                'course_id' => $course1->id,
                'judul' => 'Tugas Praktikum Laravel Lanjutan',
            ],
            [
                'deskripsi' => 'Membuat fitur pengelolaan data menggunakan Laravel dan menerapkan konsep middleware.',
                'deadline' => now()->addDays(7),
            ]
        );

        // 11. Tambahkan Tugas Kedua
        $assignment2 = Assignment::updateOrCreate(
            [
                'course_id' => $course2->id,
                'judul' => 'Tugas Perancangan Basis Data',
            ],
            [
                'deskripsi' => 'Membuat rancangan database menggunakan ERD dan mengimplementasikannya dengan SQL.',
                'deadline' => now()->addDays(10),
            ]
        );

        // 12. Contoh Pengumpulan Tugas Mahasiswa Pertama (Sudah Dinilai)
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'user_id' => $mahasiswa1->id,
            ],
            [
                'jawaban' => 'Saya telah menyelesaikan tugas praktikum Laravel dengan menerapkan middleware dan pengelolaan data.',
                'nilai' => 88,
                'catatan_dosen' => 'Hasil pengerjaan sudah baik dan sesuai dengan instruksi. Pertahankan!',
                'submitted_at' => now()->subDay(),
            ]
        );

        // 13. Contoh Pengumpulan Tugas Mahasiswa Kedua (Belum Dinilai)
        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment1->id,
                'user_id' => $mahasiswa2->id,
            ],
            [
                'jawaban' => 'Saya telah mengerjakan tugas praktikum dan menyelesaikan penerapan fitur Laravel sesuai materi.',
                'nilai' => null,
                'catatan_dosen' => null,
                'submitted_at' => now()->subHours(3),
            ]
        );
    }
}
