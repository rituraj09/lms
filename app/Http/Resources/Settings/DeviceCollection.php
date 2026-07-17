<?php
// app/Http/Resources/Settings/DeviceCollection.php
namespace App\Http\Resources\Settings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DeviceCollection extends ResourceCollection
{
    public int $currentTokenId;

    public function __construct($resource, int $currentTokenId)
    {
        parent::__construct($resource);
        $this->currentTokenId = $currentTokenId;
    }

    public function toArray(Request $request): array
    {
        return $this->collection->map(function ($item) {
            $resource = new DeviceResource($item);
            $resource->currentTokenId = $this->currentTokenId;
            return $resource->toArray(request());
        })->all();
    }
}
