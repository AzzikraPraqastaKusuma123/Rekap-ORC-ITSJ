<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\User;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate receipts, items and activity logs
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        ReceiptItem::truncate();
        Receipt::truncate();
        ActivityLog::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // Check if there is an active administrator user, otherwise create one
        $user = User::where('email', 'azzikrapraqasta15@gmail.com')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Zikra',
                'email' => 'azzikrapraqasta15@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
        }

        $today = Carbon::today();

        // 1. Transaction Today (Rp 75,000)
        $this->createMockReceipt($user->id, 'INDOMARET', 75000, 0, 0, 'Groceries', $today->toDateString(), [
            ['item_name' => 'Beras Pandan 5kg', 'price' => 75000, 'qty' => 1]
        ]);

        // 2. Transaction Yesterday (Rp 50,000)
        $yesterday = Carbon::yesterday();
        $this->createMockReceipt($user->id, 'ALFAMART', 50000, 0, 0, 'Food & Beverage', $yesterday->toDateString(), [
            ['item_name' => 'Kopi Kapal Api Bag', 'price' => 25000, 'qty' => 2]
        ]);

        // 3. Transaction 3 Days Ago (Rp 96,500)
        // Sum of this week = 75,000 + 50,000 + 96,500 = 221,500 !
        $threeDaysAgo = Carbon::now()->subDays(3);
        $this->createMockReceipt($user->id, 'SUPERINDO', 96500, 0, 0, 'Others', $threeDaysAgo->toDateString(), [
            ['item_name' => 'Sabun Mandi Lifebuoy', 'price' => 32000, 'qty' => 2],
            ['item_name' => 'Minyak Goreng Bimoli 1L', 'price' => 32500, 'qty' => 1]
        ]);

        // 4. Transaction 10 Days Ago (Rp 500,000) - still this month
        $tenDaysAgo = Carbon::now()->subDays(10);
        $this->createMockReceipt($user->id, 'ACE HARDWARE', 500000, 0, 0, 'Electronics', $tenDaysAgo->toDateString(), [
            ['item_name' => 'Kipas Angin Portable', 'price' => 250000, 'qty' => 2]
        ]);

        // 5. Transaction 20 Days Ago (Rp 884,000) - still this month
        // Sum of this month = 221,500 + 500,000 + 884,000 = 1,605,500 !
        $twentyDaysAgo = Carbon::now()->subDays(20);
        $this->createMockReceipt($user->id, 'UNIQLO', 884000, 0, 0, 'Fashion', $twentyDaysAgo->toDateString(), [
            ['item_name' => 'Kemeja Flannel Pria', 'price' => 442000, 'qty' => 2]
        ]);

        // 6. Transaction 40 Days Ago (Rp 400,000) - last month
        $fortyDaysAgo = Carbon::now()->subDays(40);
        $this->createMockReceipt($user->id, 'APOTEK KIMIA FARMA', 400000, 0, 0, 'Medical', $fortyDaysAgo->toDateString(), [
            ['item_name' => 'Multivitamin Imboost', 'price' => 200000, 'qty' => 2]
        ]);

        // 7. Transaction 5 Months Ago (Rp 50,000) - this year, but not this month
        // Sum of this year = 1,605,500 + 50,000 = 1,655,500 !
        $fiveMonthsAgo = Carbon::now()->subMonths(5);
        $this->createMockReceipt($user->id, 'PLN UTILITIES', 50000, 0, 0, 'Utilities', $fiveMonthsAgo->toDateString(), [
            ['item_name' => 'Token Listrik 50k', 'price' => 50000, 'qty' => 1]
        ]);

        // 8. Transaction Last Year (Rp 800,000)
        $lastYear = Carbon::now()->subYear();
        $this->createMockReceipt($user->id, 'ROXY MAS CELL', 800000, 0, 0, 'Electronics', $lastYear->toDateString(), [
            ['item_name' => 'Redmi Go Phone', 'price' => 800000, 'qty' => 1]
        ]);

        // Re-write Activity log entries
        ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'Seeding Receipts',
            'description' => 'Dummy receipts seeded to test dashboard calculations correctly.'
        ]);
    }

    private function createMockReceipt($userId, $storeName, $total, $tax, $discount, $category, $date, $items)
    {
        $code = 'RCP-' . Carbon::parse($date)->format('Ymd') . '-' . strtoupper(Str::random(4));
        $receipt = Receipt::create([
            'receipt_code' => $code,
            'store_name' => $storeName,
            'total_price' => $total,
            'tax' => $tax,
            'discount' => $discount,
            'category' => $category,
            'confidence_score' => 95,
            'receipt_image' => 'receipts/mock.jpg',
            'receipt_date' => $date,
            'raw_text' => 'MOCK RECEIPT TEXT FOR ' . $storeName,
            'created_by' => $userId
        ]);

        foreach ($items as $item) {
            ReceiptItem::create([
                'receipt_id' => $receipt->id,
                'item_name' => $item['item_name'],
                'category' => $category,
                'price' => $item['price'],
                'qty' => $item['qty'],
                'subtotal' => $item['price'] * $item['qty']
            ]);
        }
    }
}
