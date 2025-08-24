<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class UiSetting extends Model
{
    use HasFactory;

    /**
     * IMPORTANT: Previously forced to 'mysql' which caused tenant writes to go to main DB,
     * leaking customization across all websites. We now defer to the current default
     * connection so when TenantService switches database.default to 'tenant' the same
     * model operates on the tenant DB.
     */
    public function getConnectionName()
    {
        return config('database.default');
    }

    protected $fillable = [
        'section',
        'setting_key',
        'setting_value',
        'setting_type',
        'client_id'
    ];

    protected static function hasClientColumn(): bool
    {
        static $cache=null; if($cache!==null) return $cache;
    try { $cache=Schema::hasColumn((new self)->getTable(),'client_id'); } catch(\Throwable $e){ $cache=false; }
        return $cache;
    }

    public static function get($section, $key, $default = null, $clientId = null)
    {
        $query = self::where('section',$section)->where('setting_key',$key);
        if($clientId && self::hasClientColumn()) {
            $query->where(function($q) use ($clientId,$section,$key){
                $q->where('client_id',$clientId)
                  ->orWhere(function($q2){ $q2->whereNull('client_id'); });
            });
            $settings = $query->orderByRaw('CASE WHEN client_id IS NULL THEN 0 ELSE 1 END')->get();
            if($settings->isEmpty()) return $default;
            return optional($settings->last())->setting_value ?? $default; // client override last
        }
        $setting=$query->first();
        return $setting? $setting->setting_value : $default;
    }

    public static function set($section, $key, $value, $type = 'text', $clientId = null)
    {
        $attributes = ['section'=>$section,'setting_key'=>$key];
        if($clientId && self::hasClientColumn()) $attributes['client_id']=$clientId; else if(self::hasClientColumn()) $attributes['client_id']=null;
        return self::updateOrCreate($attributes,[
            'setting_value'=>$value,
            'setting_type'=>$type,
        ]);
    }

    public static function getSection($section, $clientId = null)
    {
        $query = self::where('section',$section);
        if($clientId && self::hasClientColumn()) {
            $all = $query->where(function($q) use ($clientId){
                $q->whereNull('client_id')->orWhere('client_id',$clientId);
            })->orderByRaw('CASE WHEN client_id IS NULL THEN 0 ELSE 1 END')->get();
            $merged=[]; foreach($all as $row){ $merged[$row->setting_key]=$row->setting_value; }
            return collect($merged);
        }
        return $query->pluck('setting_value','setting_key');
    }
}
