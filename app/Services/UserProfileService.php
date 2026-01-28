<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    /**
     * Actualiza el logo del usuario
     * Si se usa Spatie Media Library, se usará ese sistema
     * Si no, se usará el campo avatar_path tradicional
     */
    public function updateLogo(User $user, ?UploadedFile $file): void
    {
        if (! $file) {
            return;
        }

        // Si Spatie Media Library está disponible, usarlo
        if (method_exists($user, 'addMediaFromRequest')) {
            // Eliminar logo anterior si existe
            $user->clearMediaCollection('logo');

            // Agregar nuevo logo
            $user->addMediaFromRequest('logo')
                ->toMediaCollection('logo');
        } else {
            // Método tradicional: guardar en storage
            // Eliminar logo anterior si existe
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            // Guardar nuevo logo
            $path = $file->store('avatars', 'public');
            $user->update(['avatar_path' => $path]);
        }
    }

    /**
     * Obtiene la URL del logo del usuario
     */
    public function getLogoUrl(User $user): ?string
    {
        // Si Spatie Media Library está disponible, usarlo
        if (method_exists($user, 'getFirstMediaUrl')) {
            return $user->getFirstMediaUrl('logo') ?: null;
        }

        // Método tradicional
        return $user->avatarUrl();
    }

    /**
     * Actualiza los datos profesionales del usuario
     */
    public function updateProfessionalData(
        User $user,
        ?string $rut = null,
        ?string $giroSii = null,
        ?UploadedFile $logo = null,
    ): void {
        $data = [];

        if ($rut !== null) {
            $data['rut'] = $rut ?: null;
        }

        if ($giroSii !== null) {
            $data['giro_sii'] = $giroSii ?: null;
        }

        if (! empty($data)) {
            $user->update($data);
        }

        if ($logo) {
            $this->updateLogo($user, $logo);
        }
    }
}
