<?php
declare(strict_types=1);

/**
 * Calculate pagination metadata.
 *
 * @param int $totalItems Total number of records.
 * @param int $currentPage Current page (1-based).
 * @param int $perPage Items per page.
 * @return array{total_pages:int,current_page:int,per_page:int,total_items:int,offset:int,has_prev:bool,has_next:bool}
 */
function paginate(int $totalItems, int $currentPage, int $perPage): array
{
    $perPage = max(1, $perPage);
    $totalPages = (int)max(1, ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = getOffset($currentPage, $perPage);

    return [
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total_items' => $totalItems,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
    ];
}

/**
 * Compute SQL offset for pagination.
 *
 * @param int $currentPage 1-based page number.
 * @param int $perPage Items per page.
 * @return int
 */
function getOffset(int $currentPage, int $perPage): int
{
    $currentPage = max(1, $currentPage);
    $perPage = max(1, $perPage);
    return ($currentPage - 1) * $perPage;
}
