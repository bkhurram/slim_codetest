<?php

namespace App\Application\Response;

class PaginateResponse {

	public $totalItems;
	public $currentPage;
	public $totalPages;
	public $items;

	public function __construct(\Illuminate\Pagination\LengthAwarePaginator $paginator)
	{
		$this->items = $paginator->items();
		$this->totalItems = $paginator->total();
		$this->totalPages = $paginator->lastPage();
		$this->currentPage = $paginator->currentPage();
	}
}
