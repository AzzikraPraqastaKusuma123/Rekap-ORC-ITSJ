<?php

namespace App\Repositories;

interface ReceiptRepositoryInterface
{
    /**
     * Get filtered, sorted and paginated receipts.
     */
    public function getFiltered(array $filters, int $perPage = 10);

    /**
     * Get a receipt by ID with its items loaded.
     */
    public function find(int $id);

    /**
     * Create a new receipt with its items.
     */
    public function create(array $data, array $items = []);

    /**
     * Update an existing receipt and its items.
     */
    public function update(int $id, array $data, array $items = []);

    /**
     * Delete a receipt by ID.
     */
    public function delete(int $id);

    /**
     * Get unique store names list.
     */
    public function getUniqueStores();

    /**
     * Get recent transactions with limit.
     */
    public function getRecent(int $limit = 5);

    /**
     * Get top purchased products.
     */
    public function getTopProducts(int $limit = 5);

    /**
     * Get spending breakdown by store.
     */
    public function getSpendingByStore();
}
