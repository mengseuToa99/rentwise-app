<?php

namespace App\Traits;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity in the system
     * 
     * @param string $action The action being performed
     * @param string $description Description of the activity
     * @param int|null $userId The user ID performing the action (uses authenticated user if null)
     * @return \App\Models\Log|null
     */
    protected function logActivity(string $action, string $description, ?int $userId = null)
    {
        try {
            $userId = $userId ?? Auth::id() ?? null;
            
            if (!$userId) {
                return null;
            }
            
            return Log::createLog($userId, $action, $description);
        } catch (\Exception $e) {
            // Fail silently in production, log error in development
            if (config('app.env') !== 'production') {
                \Illuminate\Support\Facades\Log::error('Failed to log activity: ' . $e->getMessage());
            }
            return null;
        }
    }
    
    /**
     * Log when a record is created
     * 
     * @param string $modelName The name of the model being created
     * @param string|int $identifier A unique identifier for the record
     * @param string|null $additionalInfo Additional information about the record
     * @return \App\Models\Log|null
     */
    protected function logCreated(string $modelName, $identifier, ?string $additionalInfo = null)
    {
        $description = "Created new {$modelName} ({$identifier})";
        
        if ($additionalInfo) {
            $description .= ": {$additionalInfo}";
        }
        
        return $this->logActivity("create_{$modelName}", $description);
    }
    
    /**
     * Log when a record is updated
     * 
     * @param string $modelName The name of the model being updated
     * @param string|int $identifier A unique identifier for the record
     * @param string|null $additionalInfo Additional information about the update
     * @return \App\Models\Log|null
     */
    protected function logUpdated(string $modelName, $identifier, ?string $additionalInfo = null)
    {
        $description = "Updated {$modelName} ({$identifier})";
        
        if ($additionalInfo) {
            $description .= ": {$additionalInfo}";
        }
        
        return $this->logActivity("update_{$modelName}", $description);
    }
    
    /**
     * Log when a record is deleted
     * 
     * @param string $modelName The name of the model being deleted
     * @param string|int $identifier A unique identifier for the record
     * @param string|null $additionalInfo Additional information about the deletion
     * @return \App\Models\Log|null
     */
    protected function logDeleted(string $modelName, $identifier, ?string $additionalInfo = null)
    {
        $description = "Deleted {$modelName} ({$identifier})";
        
        if ($additionalInfo) {
            $description .= ": {$additionalInfo}";
        }
        
        return $this->logActivity("delete_{$modelName}", $description);
    }
} 