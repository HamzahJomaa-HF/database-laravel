<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class BulkDeleteUserRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Or add your authorization logic here
    }
    
    public function rules()
    {
        return [
            'user_ids' => ['required'],
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userIds = $this->getUserIdsAsArray();
            
            if (empty($userIds)) {
                $validator->errors()->add('user_ids', 'No valid user IDs provided.');
                return;
            }
            
            // Validate each user exists
            $existingCount = User::whereIn('user_id', $userIds)->count();
            if ($existingCount !== count($userIds)) {
                $validator->errors()->add('user_ids', 'One or more selected users do not exist.');
            }
        });
    }
    
    public function getUserIdsAsArray()
    {
        $userIds = $this->input('user_ids');
        
        if (is_array($userIds)) {
            return array_filter(array_map('trim', $userIds));
        }
        
        if (is_string($userIds)) {
            // Try JSON
            $decoded = json_decode($userIds, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return array_filter(array_map('trim', $decoded));
            }
            
            // Try comma-separated
            $exploded = explode(',', $userIds);
            return array_filter(array_map('trim', $exploded));
        }
        
        return [];
    }
}