<?php

namespace Database\Seeders;

use App\Models\ContactInquiry;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Ahmed Raza', 'gender' => 'male', 'phone' => '+92 300 1234567', 'email' => 'ahmed@example.com', 'measurements' => ['chest' => 40, 'waist' => 34, 'shoulder' => 18, 'sleeve' => 25, 'shirt_length' => 42, 'neck' => 16], 'measurement_unit' => 'in', 'measured_at' => today()->subDays(20)],
            ['name' => 'Bilal Khan', 'gender' => 'male', 'phone' => '+92 321 7654321', 'email' => null, 'measurements' => ['chest' => 102, 'waist' => 90, 'shoulder' => 47, 'sleeve' => 64, 'shirt_length' => 108], 'measurement_unit' => 'cm', 'measured_at' => today()->subDays(10)],
            ['name' => 'Usman Tariq', 'gender' => 'male', 'phone' => '+92 333 1112223', 'email' => 'usman@example.com', 'measurements' => null, 'measurement_unit' => 'in', 'measured_at' => null],
            ['name' => 'Ayesha Noor', 'gender' => 'female', 'phone' => '+92 345 9998887', 'email' => null, 'measurements' => ['chest' => 38, 'waist' => 32, 'seat' => 39, 'trouser_length' => 40], 'measurement_unit' => 'in', 'measured_at' => today()->subDays(3)],
        ];

        $orders = [
            [0, ['garment' => 'Shalwar kameez', 'package' => 'everyday', 'fabric' => 'Lawn', 'standard_size' => 'custom', 'status' => 'stitching', 'due_date' => today()->addDays(5), 'total' => 4500, 'advance' => 2000, 'style_notes' => 'Collar band, no front pocket.']],
            [0, ['garment' => 'Waistcoat', 'package' => 'premium', 'fabric' => 'Blended suiting', 'standard_size' => 'custom', 'status' => 'delivered', 'due_date' => today()->subDays(12), 'total' => 6500, 'advance' => 6500, 'style_notes' => null]],
            [1, ['garment' => 'Three-piece suit', 'package' => 'occasion', 'fabric' => 'My own fabric', 'standard_size' => 'custom', 'status' => 'cutting', 'due_date' => today()->addDays(18), 'total' => 28000, 'advance' => 10000, 'style_notes' => 'Slim fit, double vent, ticket pocket.']],
            [2, ['garment' => 'Sherwani', 'package' => 'occasion', 'fabric' => 'Karandi', 'standard_size' => 'L', 'status' => 'new', 'due_date' => today()->addDays(30), 'total' => 0, 'advance' => 0, 'style_notes' => 'Wedding on the 15th — needs fitting first.']],
            [3, ['garment' => 'Two-piece suit', 'package' => 'premium', 'fabric' => 'Linen', 'standard_size' => 'custom', 'status' => 'ready', 'due_date' => today()->addDays(1), 'total' => 18000, 'advance' => 9000, 'style_notes' => null]],
            [3, ['garment' => 'Blazer', 'package' => 'everyday', 'fabric' => 'Cotton', 'standard_size' => 'M', 'status' => 'confirmed', 'due_date' => today()->addDays(10), 'total' => 9000, 'advance' => 3000, 'style_notes' => null]],
        ];

        $created = [];
        foreach ($customers as $data) {
            $created[] = Customer::firstOrCreate(['phone' => $data['phone']], $data);
        }

        foreach ($orders as [$index, $data]) {
            $customer = $created[$index];
            $created[$index]->orders()->create($data + [
                'measurement_unit' => $customer->measurement_unit ?? 'in',
                'measurements' => $data['standard_size'] === 'custom' ? ($customer->measurements ?? []) : [],
            ]);
        }

        ContactInquiry::create(['name' => 'Farhan Siddiqui', 'phone' => '+92 301 5556667', 'message' => 'Do you stitch kids shalwar kameez too? Need 2 pieces for Eid.', 'status' => 'new']);
        ContactInquiry::create(['name' => 'Zain Malik', 'phone' => '+92 312 4443332', 'message' => 'Can I bring my own fabric for a waistcoat?', 'status' => 'handled']);
    }
}
