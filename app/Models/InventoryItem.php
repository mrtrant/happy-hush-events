<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use SoftDeletes;

    protected $table = 'inventory_items';

    protected $fillable = [
        'uuid', 'inventory_code', 'category_id', 'name', 'description',
        'quantity', 'available_quantity', 'unit_type', 'purchase_date',
        'purchase_price', 'condition_status', 'image', 'location', 'is_active'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InventoryLog::class, 'inventory_item_id');
    }
}

?>
