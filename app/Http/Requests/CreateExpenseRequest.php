<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Group;

class CreateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // For usage within Service where we manually merge data, 
        // we expect 'group_id' to be present in the data merging.
        // If used as standard Controller request, it might come from route or input.
        
        $groupId = $this->input('group_id') ?? $this->route('group');
        
        // If group_id is not provided, validation rules will catch it, 
        // but for auth check we return false (or let it pass to validation? usually false).
        if (!$groupId) return true; // Let validation handle missing field

        $group = Group::find($groupId); // Don't failOrFail here to allow validation to say "invalid"
        if (!$group) return false;
        
        $user = $this->user() ?? auth()->user();
        if (!$user) return false;

        // User must be group member
        if (!$group->users()->where('users.id', $user->id)->exists()) {
            return false;
        }
        
        // Check policy
        return $user->can('create-expense', $group);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required|integer|exists:groups,id',
            'title' => 'required|string|min:1|max:255',
            'total_amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/' // Exact 2 decimal places
            ],
            'paid_by' => 'required|integer|exists:users,id',
            'splits' => 'required|array|min:1|max:100',
            'splits.*.user_id' => 'required|integer|exists:users,id',
            'splits.*.share_amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:999999.99',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $groupId = $this->input('group_id');
            if (!$groupId) return;
            
            $group = Group::find($groupId);
            if (!$group) return;
            
            // Validate all participants are group members
            foreach ($this->input('splits', []) as $index => $split) {
                $userId = $split['user_id'] ?? null;
                if ($userId && !$group->users()->where('users.id', $userId)->exists()) {
                    $validator->errors()->add(
                        "splits.$index.user_id",
                        "User {$userId} is not a member of this group."
                    );
                }
            }
            
            // EXACT split total validation (High Precision)
            $splitTotal = collect($this->input('splits', []))->sum('share_amount');
            $totalAmount = (float) $this->input('total_amount');
            
            // Use epsilon for float comparison
            if (abs($splitTotal - $totalAmount) > 0.001) { 
                $validator->errors()->add(
                    'splits',
                    sprintf(
                        'Split total ($%s) must EXACTLY equal expense amount ($%s). Difference: $%s',
                        number_format($splitTotal, 2),
                        number_format($totalAmount, 2),
                        number_format(abs($splitTotal - $totalAmount), 2)
                    )
                );
            }
        });
    }
}
