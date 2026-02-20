<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    private function role(User $user): string
    {
        return strtolower((string) $user->role);
    }

    private function isAdmin(User $user): bool
    {
        return $this->role($user) === 'admin';
    }

    private function isManager(User $user): bool
    {
        return $this->role($user) === 'manager';
    }

    private function isStaff(User $user): bool
    {
        return in_array($this->role($user), ['staff', 'employee']);
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $this->isManager($user) || $this->isStaff($user);
    }

    public function view(User $user, Document $document): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            return $document->access_level === 'public'
                || (int) $document->department_id === (int) $user->department_id;
        }

        // staff / employee: hanya dokumen public
        return $document->access_level === 'public';
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $this->isManager($user);
    }

    public function update(User $user, Document $document): bool
    {
        if ($this->isAdmin($user)) {
            return true;
        }

        if ($this->isManager($user)) {
            // hanya dokumen yang dia sendiri upload
            return (int) $document->uploaded_by === (int) $user->id;
        }

        return false;
    }

    public function delete(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    public function download(User $user, Document $document): bool
    {
        return $this->view($user, $document);
    }
}