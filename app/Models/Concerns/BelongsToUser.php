<?php

namespace App\Models\Concerns;

trait BelongsToUser
{
    protected string $ownerColumn = 'user_id';

    public function scopeOwned(?string $column = null)
    {
        $column = $column ?: $this->table . '.' . $this->ownerColumn;

        return apply_owner_scope($this, $column);
    }

    public function applyOwnerToBuilder($builder, ?string $column = null)
    {
        $column = $column ?: $this->table . '.' . $this->ownerColumn;

        return apply_owner_scope($builder, $column);
    }

    public function withOwner(array $data): array
    {
        return owned_create_data($data, $this->ownerColumn);
    }

    public function isOwnedByCurrentUser(?array $row): bool
    {
        return user_owns_row($row, $this->ownerColumn);
    }
}
