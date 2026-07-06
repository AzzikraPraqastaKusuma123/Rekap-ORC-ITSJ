<?php

namespace App\Repositories;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceiptRepository implements ReceiptRepositoryInterface
{
    /**
     * Get filtered, sorted and paginated receipts.
     */
    public function getFiltered(array $filters, int $perPage = 10)
    {
        $query = Receipt::with('items');

        // Apply RBAC: Staff can only see their own.
        if (Auth::check() && Auth::user()->role === 'staff') {
            $query->where('created_by', Auth::id());
        }

        // Search in store name, receipt code or raw text
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                    ->orWhere('receipt_code', 'like', "%{$search}%")
                    ->orWhere('raw_text', 'like', "%{$search}%");
            });
        }

        // Filter by store
        if (!empty($filters['store'])) {
            $query->where('store_name', $filters['store']);
        }

        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->where('receipt_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('receipt_date', '<=', $filters['end_date']);
        }

        // Filter by tracking status
        if (!empty($filters['status'])) {
            $status = $filters['status'];
            if ($status === 'raw') {
                $query->whereColumn('created_at', 'updated_at'); // Raw / Belum Diedit
            } elseif ($status === 'edited') {
                $query->whereColumn('created_at', '!=', 'updated_at')->where('status', 'pending_approval');
            } elseif ($status === 'verified') {
                $query->where('status', 'verified');
            } elseif ($status === 'rejected') {
                $query->where('status', 'pending_correction'); // Ditolak / Reject
            }
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        if (in_array($sortBy, ['receipt_date', 'total_price', 'created_at', 'store_name'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a receipt by ID with its items loaded.
     */
    public function find(int $id)
    {
        return Receipt::with('items')->findOrFail($id);
    }

    /**
     * Create a new receipt with its items.
     */
    public function create(array $data, array $items = [])
    {
        return DB::transaction(function () use ($data, $items) {
            $receipt = Receipt::create($data);

            foreach ($items as $item) {
                $receipt->items()->create([
                    'item_name' => $item['item_name'],
                    'category' => $item['category'] ?? null,
                    'price' => $item['price'],
                    'qty' => $item['qty'] ?? 1,
                    'subtotal' => $item['subtotal'] ?? ($item['price'] * ($item['qty'] ?? 1)),
                ]);
            }

            return $receipt;
        });
    }

    /**
     * Update an existing receipt and its items.
     */
    public function update(int $id, array $data, array $items = [])
    {
        return DB::transaction(function () use ($id, $data, $items) {
            $receipt = Receipt::findOrFail($id);
            $receipt->update($data);

            if (!empty($items)) {
                // Remove existing items and recreate
                $receipt->items()->delete();

                foreach ($items as $item) {
                    $receipt->items()->create([
                        'item_name' => $item['item_name'],
                        'category' => $item['category'] ?? null,
                        'price' => $item['price'],
                        'qty' => $item['qty'] ?? 1,
                        'subtotal' => $item['subtotal'] ?? ($item['price'] * ($item['qty'] ?? 1)),
                    ]);
                }
            }

            return $receipt;
        });
    }

    /**
     * Delete a receipt by ID.
     */
    public function delete(int $id)
    {
        return DB::transaction(function () use ($id) {
            $receipt = Receipt::findOrFail($id);
            return $receipt->delete();
        });
    }

    /**
     * Get unique store names list.
     */
    public function getUniqueStores()
    {
        $query = Receipt::select('store_name')->distinct();

        if (Auth::check() && Auth::user()->role === 'staff') {
            $query->where('created_by', Auth::id());
        }

        return $query->orderBy('store_name')
            ->pluck('store_name')
            ->toArray();
    }

    /**
     * Get recent transactions with limit.
     */
    public function getRecent(int $limit = 5)
    {
        $query = Receipt::orderBy('created_at', 'desc')->limit($limit);

        if (Auth::check() && Auth::user()->role === 'staff') {
            $query->where('created_by', Auth::id());
        }

        return $query->get();
    }

    /**
     * Get top purchased products.
     */
    public function getTopProducts(int $limit = 5)
    {
        $query = ReceiptItem::select('receipt_items.item_name', DB::raw('SUM(receipt_items.qty) as total_qty'), DB::raw('SUM(receipt_items.subtotal) as total_spent'));

        if (Auth::check() && Auth::user()->role === 'staff') {
            $query->join('receipts', 'receipt_items.receipt_id', '=', 'receipts.id')
                ->where('receipts.created_by', Auth::id());
        }

        return $query->groupBy('receipt_items.item_name')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get spending breakdown by store.
     */
    public function getSpendingByStore()
    {
        $query = Receipt::select('store_name', DB::raw('SUM(total_price) as total_spent'), DB::raw('COUNT(id) as total_receipts'));

        if (Auth::check() && Auth::user()->role === 'staff') {
            $query->where('created_by', Auth::id());
        }

        return $query->groupBy('store_name')
            ->orderBy('total_spent', 'desc')
            ->get();
    }
}
