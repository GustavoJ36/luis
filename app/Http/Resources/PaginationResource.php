<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    /**
     * The resource class to use for the items.
     *
     * @var string|null
     */
    protected $resourceClass;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @param  string|null  $resourceClass
     * @return void
     */
    public function __construct($resource, $resourceClass = null)
    {
        parent::__construct($resource);
        $this->resourceClass = $resourceClass;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "total" => $this->total(),
            "per_page" => $this->perPage(),
            "current_page" => $this->currentPage(),
            "last_page" => $this->lastPage(),
            "first_page_url" => $this->url(1),
            "last_page_url" => $this->url($this->lastPage()),
            "next_page_url" => $this->nextPageUrl(),
            "prev_page_url" => $this->previousPageUrl(),
            "path" => $this->path(),
            "from" => $this->firstItem(),
            "to" => $this->lastItem(),
            "data" => $this->resourceClass 
                        ? $this->resourceClass::collection($this->getCollection()) 
                        : $this->getCollection(),
        ];
    }
}
