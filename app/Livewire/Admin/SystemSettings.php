<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\CheckPermission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\LogsActivity;

class SystemSettings extends Component
{
    use AuthorizesRequests;
    use LogsActivity;
    
    public $settings = [];
    public $newSetting = [
        'setting_name' => '',
        'setting_value' => '',
        'description' => ''
    ];
    public $editSettingId = null;
    
    protected $rules = [
        'settings.*.setting_value' => 'required',
        'newSetting.setting_name' => 'required|unique:system_settings,setting_name',
        'newSetting.setting_value' => 'required',
    ];
    
    protected $messages = [
        'settings.*.setting_value.required' => 'The setting value is required.',
        'newSetting.setting_name.required' => 'Setting name is required.',
        'newSetting.setting_name.unique' => 'This setting name already exists.',
        'newSetting.setting_value.required' => 'Setting value is required.',
    ];
    
    public function mount()
    {
        if (!Auth::check() || !Auth::user()->hasPermission('manage_system_settings')) {
            abort(403, 'Unauthorized action');
        }

        // Load existing settings from database
        $this->loadSystemSettings();
    }
    
    public function loadSystemSettings()
    {
        $this->refreshSettings();
    }
    
    public function refreshSettings()
    {
        $this->settings = SystemSetting::all()->toArray();
    }
    
    public function updateSetting($index)
    {
        $this->validate([
            "settings.{$index}.setting_value" => 'required',
        ]);
        
        $setting = SystemSetting::find($this->settings[$index]['setting_id']);
        $setting->setting_value = $this->settings[$index]['setting_value'];
        $setting->save();
        
        // Log the activity
        $this->logUpdated('setting', $setting->setting_name, 'Updated setting value to: ' . $setting->setting_value);
        
        session()->flash('success', 'Setting updated successfully.');
    }
    
    public function editSetting($id)
    {
        $this->editSettingId = $id;
    }
    
    public function cancelEdit()
    {
        $this->editSettingId = null;
        $this->refreshSettings();
    }
    
    public function deleteSetting($id)
    {
        $setting = SystemSetting::find($id);
        $settingName = $setting->setting_name;
        
        SystemSetting::destroy($id);
        
        // Log the activity
        $this->logDeleted('setting', $settingName);
        
        $this->refreshSettings();
        session()->flash('success', 'Setting deleted successfully.');
    }
    
    public function addNewSetting()
    {
        $this->validate([
            'newSetting.setting_name' => 'required|unique:system_settings,setting_name',
            'newSetting.setting_value' => 'required',
        ]);
        
        $setting = SystemSetting::create([
            'setting_name' => $this->newSetting['setting_name'],
            'setting_value' => $this->newSetting['setting_value'],
            'description' => $this->newSetting['description'],
        ]);
        
        // Log the activity
        $this->logCreated('setting', $setting->setting_name, 'Initial value: ' . $setting->setting_value);
        
        $this->newSetting = [
            'setting_name' => '',
            'setting_value' => '',
            'description' => ''
        ];
        
        $this->refreshSettings();
        session()->flash('success', 'New setting added successfully.');
    }
    
    public function render()
    {
        return view('livewire.admin.system-settings')
            ->layout('layouts.admin');
    }
} 