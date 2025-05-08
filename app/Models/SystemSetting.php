<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'setting_id';
    protected $table = 'system_settings';
    
    protected $fillable = [
        'setting_name',
        'setting_value',
        'description'
    ];
    
    /**
     * Get a setting value by name
     * 
     * @param string $settingName
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $settingName, $default = null)
    {
        $setting = self::where('setting_name', $settingName)->first();
        
        return $setting ? $setting->setting_value : $default;
    }
    
    /**
     * Update or create a setting
     * 
     * @param string $settingName
     * @param mixed $value
     * @param string|null $description
     * @return void
     */
    public static function updateOrCreateSetting(string $settingName, $value, $description = null)
    {
        self::updateOrCreate(
            ['setting_name' => $settingName],
            [
                'setting_value' => $value,
                'description' => $description
            ]
        );
    }
} 