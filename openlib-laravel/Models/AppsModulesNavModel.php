<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class AppsModulesNavModel extends Model
{
    use SoftDeletes;

    protected $table = 'apps_modules_nav';
    protected $primaryKey = 'amn_id';

    public function group()
    {
        return $this->belongsTo(AppsModulesGroupModel::class, 'amn_item_id', 'amg_id');
    }

    public function module()
    {
        return $this->belongsTo(AppsModulesModel::class, 'amn_item_id', 'am_id');
    }

    public function scopeViewQuery()
    {
        return DB::table($this->table)
            ->select(DB::raw("amn_id, amn_item_id, amn_item_type, amn_parent, amn_order, apps_modules_nav.deleted_at, am_name_id as item_module_id, am_name_en as item_module_en, amg_name_id as item_group_id, amg_name_en as item_group_en, am_url as item_module_url, am_display as item_module_display, am_slug as item_module_slug, am_icon as item_module_icon, amg_icon as item_group_icon, amg_slug as item_group_slug"))
            ->leftJoin('apps_modules', function ($leftJoin) {
                $leftJoin->on('amn_item_id', '=', 'am_id');
                $leftJoin->on('amn_item_type', '=', DB::raw("'module'"));
            })
            ->leftJoin('apps_modules_group', function ($leftJoin) {
                $leftJoin->on('amn_item_id', '=', 'amg_id');
                $leftJoin->on('amn_item_type', '=', DB::raw("'group'"));
            })
            ->where('amn_id', '>', 0)
            ->orderBy('amn_parent', 'asc')
            ->orderBy('amn_order', 'asc')
            ->get();
    }
}
