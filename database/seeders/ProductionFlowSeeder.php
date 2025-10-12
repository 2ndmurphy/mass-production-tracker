<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ProductionFlowSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $driver = DB::getDriverName();

        // disable FK checks depending on driver
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }

        // Clear tables (use delete() for sqlite compatibility)
        $tables = [
            'q_c_inspections',
            'quality_control_results',
            'finished_good_stocks',
            'stock_movements',
            'production_materials',
            'productions',
            'raw_material_batches',
            'warehouses',
            'contracts',
            'suppliers',
            'materials',
            'users',
            'roles',
            'departments',
        ];

        foreach ($tables as $t) {
            if ($driver === 'sqlite') {
                DB::table($t)->delete();
                // reset sqlite sequence if exists
                DB::statement("DELETE FROM sqlite_sequence WHERE name='{$t}';");
            } else {
                DB::table($t)->truncate();
            }
        }

        // re-enable FK checks
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        //
        // 1) Roles & Departments
        //
        $roles = [
            ['name' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'manager', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'staff', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('roles')->insert($roles);
        $rolesMap = DB::table('roles')->pluck('id', 'name')->toArray();

        $departments = [
            ['name' => 'management', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'warehouse', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'production', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'qc', 'created_at' => $now, 'updated_at' => $now],
        ];
        DB::table('departments')->insert($departments);
        $deptMap = DB::table('departments')->pluck('id', 'name')->toArray();

        //
        // 2) Users (admin, manager, warehouse, production, qc)
        //
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => Hash::make('password'),
                'role_id' => $rolesMap['admin'],
                'department_id' => $deptMap['management'],
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@test.com',
                'password' => Hash::make('password'),
                'role_id' => $rolesMap['manager'],
                'department_id' => $deptMap['management'],
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'name' => 'Warehouse Staff',
                'email' => 'warehouse@test.com',
                'password' => Hash::make('password'),
                'role_id' => $rolesMap['staff'],
                'department_id' => $deptMap['warehouse'],
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'name' => 'Production Staff',
                'email' => 'production@test.com',
                'password' => Hash::make('password'),
                'role_id' => $rolesMap['staff'],
                'department_id' => $deptMap['production'],
                'created_at' => $now, 'updated_at' => $now
            ],
            [
                'name' => 'QC Staff',
                'email' => 'qc@test.com',
                'password' => Hash::make('password'),
                'role_id' => $rolesMap['staff'],
                'department_id' => $deptMap['qc'],
                'created_at' => $now, 'updated_at' => $now
            ],
        ];
        DB::table('users')->insert($users);
        $usersMap = DB::table('users')->pluck('id', 'email')->toArray();

        //
        // 3) Materials
        //
        $materialNames = ['Flour', 'Salt', 'Kansui', 'Sugar', 'Oil'];
        $materials = [];
        foreach ($materialNames as $m) {
            $materials[] = [
                'name' => $m,
                'unit' => 'kg',
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        DB::table('materials')->insert($materials);
        $materialsAll = DB::table('materials')->get()->keyBy('name');

        //
        // 4) Suppliers + Contracts
        //
        $supplierId = DB::table('suppliers')->insertGetId([
            'name' => 'PT. Food Supplier',
            'contact_person' => 'Budi',
            'phone' => '081234567890',
            'email' => 'supplier@test.com',
            'address' => 'Jl. Example No.1',
            'status' => 'Active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // create contracts for first 3 materials
        $materialIds = $materialsAll->pluck('id')->toArray();
        for ($i = 0; $i < 3; $i++) {
            DB::table('contracts')->insert([
                'supplier_id' => $supplierId,
                'material_id' => $materialIds[$i],
                'price' => rand(5000, 20000),
                'delivery_schedule' => 'Weekly',
                'start_date' => $now->subMonths(1)->toDateString(),
                'end_date' => $now->addMonths(6)->toDateString(),
                'payment_status' => 'Pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        //
        // 5) Warehouses
        //
        $rawWarehouseId = DB::table('warehouses')->insertGetId([
            'name' => 'Raw Material Warehouse',
            'type' => 'RawMaterial',
            'location' => 'Plant A',
            'created_at' => $now, 'updated_at' => $now
        ]);
        $finishedWarehouseId = DB::table('warehouses')->insertGetId([
            'name' => 'Finished Goods Warehouse',
            'type' => 'FinishedGoods',
            'location' => 'Plant A - FG',
            'created_at' => $now, 'updated_at' => $now
        ]);

        //
        // 6) Raw Material Batches (5 batches)
        //
        $rawBatchIds = [];
        $materialCollection = $materialsAll->values()->all();
        for ($i = 0; $i < 5; $i++) {
            $mat = $materialCollection[$i % count($materialCollection)];
            $batchCode = 'RB-' . $now->format('Ymd') . '-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT);
            $qty = rand(200, 600);
            $rawId = DB::table('raw_material_batches')->insertGetId([
                'batch_code' => $batchCode,
                'supplier_id' => $supplierId,
                'material_id' => $mat->id,
                'received_date' => $now->subDays(rand(1, 7))->toDateString(),
                'quantity' => $qty,
                'unit' => $mat->unit,
                'status' => 'in_use',
                'received_by' => $usersMap['warehouse@test.com'] ?? null,
                'notes' => 'Seed batch ' . $batchCode,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $rawBatchIds[] = $rawId;

            // initial stock movement IN to represent receiving
            DB::table('stock_movements')->insert([
                'material_id' => $mat->id,
                'raw_batch_id' => $rawId,
                'warehouse_id' => $rawWarehouseId,
                'type' => 'in',
                'quantity' => $qty,
                'unit' => $mat->unit,
                'related_production_id' => null,
                'created_by' => $usersMap['warehouse@test.com'],
                'note' => 'Initial receive seed',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        //
        // 7) Productions (5) — create an estafet: some planned, some in_progress, some qc_pending/passed/failed
        //
        $productionIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $code = 'PRD-' . $now->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            // cycle statuses to create variety
            $statusCycle = ['planned', 'in_progress', 'qc_pending', 'qc_passed', 'qc_failed'];
            $status = $statusCycle[$i - 1];

            $prodDate = $now->subDays(6 - $i)->toDateString();

            $prodId = DB::table('productions')->insertGetId([
                'production_code' => $code,
                'production_date' => $prodDate,
                'shift' => ($i % 3 === 0) ? 'C' : (($i % 3 === 1) ? 'A' : 'B'),
                'quantity_carton' => rand(50, 150),
                'status' => $status,
                'started_by' => ($status === 'in_progress' || $status === 'qc_pending' || $status === 'qc_passed' || $status === 'qc_failed') ? $usersMap['production@test.com'] : null,
                'completed_by' => in_array($status, ['qc_pending','qc_passed','qc_failed']) ? $usersMap['production@test.com'] : null,
                'completed_at' => in_array($status, ['qc_pending','qc_passed','qc_failed']) ? $now->subDays(6 - $i) : null,
                'notes' => "Seed production $code",
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $productionIds[] = $prodId;

            // Add production_materials: pick 2 random raw batches and consume quantity
            $usedRaw = (array) array_rand($rawBatchIds, 2);
            foreach ($usedRaw as $idxKey) {
                // array_rand returns index keys; map back to ids
                $rawId = $rawBatchIds[$idxKey];
                // find material id for that raw batch
                $rawRow = DB::table('raw_material_batches')->where('id', $rawId)->first();
                $useQty = round(rand(10, 80) * (1.0), 4);

                DB::table('production_materials')->insert([
                    'production_id' => $prodId,
                    'raw_batch_id' => $rawId,
                    'material_id' => $rawRow->material_id,
                    'quantity_used' => $useQty,
                    'unit' => $rawRow->unit,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // create stock movement OUT (consumption)
                DB::table('stock_movements')->insert([
                    'material_id' => $rawRow->material_id,
                    'raw_batch_id' => $rawId,
                    'warehouse_id' => $rawWarehouseId,
                    'type' => 'out',
                    'quantity' => $useQty,
                    'unit' => $rawRow->unit,
                    'related_production_id' => $prodId,
                    'created_by' => $usersMap['production@test.com'],
                    'note' => "Consumed for production $code",
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // If production ended in QC states, create QC entries and finished goods when pass
            if ($status === 'qc_passed' || $status === 'qc_failed' || $status === 'qc_pending') {
                $qcBy = $usersMap['qc@test.com'] ?? null;
                // quality_control_results (legacy)
                DB::table('quality_control_results')->insert([
                    'production_id' => $prodId,
                    'qc_by' => $qcBy,
                    'sample_count' => rand(1, 10),
                    'status' => ($status === 'qc_passed') ? 'pass' : (($status === 'qc_failed') ? 'fail' : 'rework'),
                    'defect_type' => ($status === 'qc_failed') ? 'visual_defect' : null,
                    'action_taken' => ($status === 'qc_failed') ? 'Send to rework' : null,
                    'checked_at' => $now->subDays(6 - $i),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // also add q_c_inspections table row (if you use that table in code)
                DB::table('q_c_inspections')->insert([
                    'production_batch_id' => $prodId,
                    'inspector_id' => $qcBy,
                    'result' => ($status === 'qc_passed') ? 'pass' : (($status === 'qc_failed') ? 'fail' : 'rework'),
                    'sample_count' => rand(1, 10),
                    'defect_type' => ($status === 'qc_failed') ? 'visual_defect' : null,
                    'notes' => ($status === 'qc_failed') ? 'Found defects' : 'OK',
                    'checked_at' => $now->subDays(6 - $i),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // if pass -> create finished goods stock record
                if ($status === 'qc_passed') {
                    DB::table('finished_good_stocks')->insert([
                        'production_id' => $prodId,
                        'warehouse_id' => $finishedWarehouseId,
                        'available_carton' => rand(30, 120),
                        'entry_date' => $now->subDays(6 - $i)->toDateString(),
                        'added_by' => $usersMap['production@test.com'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // Done
        $this->command->info('✅ Full production flow seeded (warehouse → production → QC).');
        $this->command->info('Users: admin@test.com / manager@test.com / warehouse@test.com / production@test.com / qc@test.com (password: password)');
    }
}
