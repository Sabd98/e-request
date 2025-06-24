<?php

namespace Database\Seeders;
// database/seeders/DatabaseSeeder.php

use App\Models\User;
use App\Models\Request;
use App\Models\ApprovalLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Solusi 1: Nonaktifkan foreign key check
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Hapus data
        ApprovalLog::truncate();
        Request::truncate();
        User::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Buat admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);


        // Buat approver
        $approver1 = User::create([
            'name' => 'John Approver',
            'email' => 'approver1@example.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
        ]);

        $approver2 = User::create([
            'name' => 'Jane Manager',
            'email' => 'approver2@example.com',
            'password' => Hash::make('password'),
            'role' => 'approver',
        ]);

        // Kumpulkan semua approver dalam satu array
        $allApprovers = [$approver1, $approver2];

        // Buat requestor
        $requestors = [];
        foreach (['Finance', 'HRD', 'IT', 'Marketing', 'Operations'] as $i => $dept) {
            $requestors[] = User::create([
                'name' => "{$dept} Staff",
                'email' => strtolower($dept) . '@example.com',
                'password' => Hash::make('password'),
                'role' => 'requestor',
            ]);
        }

        // Buat request dummy
        $requestTypes = ['cuti', 'atk', 'akses', 'reimbursement'];
        $statuses = ['draft', 'submitted', 'approved', 'rejected'];
        $descriptions = [
            'Permintaan cuti tahunan',
            'Pengajuan alat tulis kantor',
            'Permintaan akses sistem payroll',
            'Reimbursement biaya perjalanan dinas',
            'Permintaan cuti mendesak',
            'Pengadaan printer baru',
            'Akses ke database pelanggan',
            'Pengembalian biaya seminar'
        ];

        $requests = [];
        foreach (range(1, 50) as $i) {
            $status = $statuses[array_rand($statuses)];
            $requestor = $requestors[array_rand($requestors)];

            $requestData = [
                'title' => 'Request #' . $i . ' - ' . $requestTypes[array_rand($requestTypes)],
                'description' => $descriptions[array_rand($descriptions)],
                'request_type' => $requestTypes[array_rand($requestTypes)],
                'status' => $status,
                'created_by' => $requestor->id,
                'created_at' => now()->subDays(rand(1, 30)),
            ];

            // Buat beberapa request yang dihapus (soft delete)
            if ($i > 40) {
                $requestData['deleted_at'] = now()->subDays(rand(1, 5));
            }

            $request = Request::create($requestData);
            $requests[] = $request;

            // Buat approval log untuk request yang sudah disubmit
            if (in_array($status, ['approved', 'rejected', 'submitted'])) {
                $action = $status === 'approved' ? 'approve' : ($status === 'rejected' ? 'reject' : null);

                if ($action) {
                    // Pilih approver secara acak dari daftar approver
                    $approver = $allApprovers[array_rand($allApprovers)];

                    ApprovalLog::create([
                        'request_id' => $request->id,
                        'user_id' => $approver->id,
                        'action' => $action,
                        'notes' => $action === 'approve'
                            ? 'Permintaan disetujui'
                            : 'Ditolak karena melebihi budget',
                        'created_at' => $request->created_at->addHours(rand(1, 48)),
                    ]);
                }
            }
        }

        // Buat beberapa request dengan multiple approval logs
        $multiStepRequest = Request::create([
            'title' => 'Pengajuan cuti panjang',
            'description' => 'Cuti 14 hari untuk perjalanan keluarga',
            'request_type' => 'cuti',
            'status' => 'approved',
            'created_by' => $requestors[0]->id,
            'created_at' => now()->subDays(10),
        ]);

        ApprovalLog::create([
            'request_id' => $multiStepRequest->id,
            'user_id' => $approver1->id,
            'action' => 'approve',
            'notes' => 'Disetujui atasan langsung',
            'created_at' => $multiStepRequest->created_at->addHours(2),
        ]);

        ApprovalLog::create([
            'request_id' => $multiStepRequest->id,
            'user_id' => $approver2->id,
            'action' => 'approve',
            'notes' => 'Disetujui HRD',
            'created_at' => $multiStepRequest->created_at->addHours(24),
        ]);

        // Buat request dengan attachment dummy
        $attachmentRequest = Request::create([
            'title' => 'Reimbursement seminar teknologi',
            'description' => 'Biaya pendaftaran seminar Laravel Advanced',
            'request_type' => 'reimbursement',
            'status' => 'submitted',
            'attachment' => 'seminar_invoice.pdf',
            'created_by' => $requestors[2]->id,
            'created_at' => now()->subDays(3),
        ]);

        $this->command->info('Dummy data berhasil dibuat!');
        $this->command->info('Admin: admin@example.com / password');
        $this->command->info('Approver: approver1@example.com / password');
        $this->command->info('Requestor: finance@example.com / password');
    }
}