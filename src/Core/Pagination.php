<?php
// This is a helper class to make paginating 
// records easy.



namespace ACHD\Core
class Pagination{
	public $currentPage;
	public $baseUrl;
	public $perPage;
	public $totalCount;
	public function __construct($baseURL,$page = 1, $perPage = ITEM_PER_PAGE, $totalCount = 0){
		$this->currentPage = $page;
		$this->perPage     = $perPage;
		$this->totalCount  = $totalCount;
		$this->baseUrl		=$baseURL;
	}
	public function offset(){
		// Assuming 20 items per page:
		// page 1 has an offset of 0    (1-1) * 20
		// page 2 has an offset of 20   (2-1) * 20
		//   in other words, page 2 starts with item 21
		return ($this->currentPage - 1) * $this->perPage;
	}
	public function totalPages(){
		return ceil($this->totalCount / $this->perPage);
	}
	public function previousPage(){
		return $this->currentPage - 1;
	}
	public function nextPage(){
		return $this->currentPage + 1;
	}
	public function hasPreviousPage(){
		return $this->previousPage() >= 1 ? true : false;
	}
	public function hasNextPage(){
		return $this->nextPage() <= $this->totalPages() ? true : false;
	}
	public function outputPagination(){
		$output = "<div class=\"pagination\" style=\"clear: both;\">";
		if ($this->totalPages() > 1) {
			if ($this->hasPreviousPage()) {
				$output .= "<a class=\"paginationPrevNext\" href=\"{$this->baseUrl}?page=";
				$output .= u($this->previousPage());
				$output .= "\">&laquo; Previous</a> ";
			}
			for ($i = 1; $i <= $this->totalPages(); $i++) {
				if ($i == $this->currentPage) {
					$output .= " <span class=\"selectedPage\"> {$i} </span> ";
				} else {
					$output .= " <a class=\"moreInfoLinkInline\" href=\"{$this->baseUrl}?page={$i}\">{$i}</a> ";
				}
			}
			if ($this->hasNextPage()) {
				$output .= " <a class=\"paginationPrevNext\" href=\"{$this->baseUrl}?page=";
				$output .= u($this->nextPage());
				$output .= "\">Next &raquo;</a> ";
			}
			$output .= "</div>";
			echo $output;
		}
	}
}
