<?php

namespace App\Models\Concerns;

use App\Models\Kandang;

/**
 * Ensures the related kandang automatically flips between aktif and maintenance
 * whenever occupancy-changing models are created, updated, or deleted.
 */
trait SyncsKandangMaintenance
{
    /**
     * Keep track of the previously associated kandang for sync after updates/deletes.
     */
    protected ?int $originalKandangIdForSync = null;

    protected static function bootSyncsKandangMaintenance(): void
    {
        static::saving(function ($model) {
            $model->captureOriginalKandangIdForSync();
        });

        static::deleting(function ($model) {
            $model->captureOriginalKandangIdForSync();
        });

        static::saved(function ($model) {
            $model->syncRelatedKandangMaintenance();
        });

        static::deleted(function ($model) {
            $model->syncRelatedKandangMaintenance(forceOriginal: true);
        });
    }

    protected function captureOriginalKandangIdForSync(): void
    {
        if ($this->originalKandangIdForSync === null) {
            $original = $this->getOriginal('kandang_id');
            $this->originalKandangIdForSync = is_null($original) ? null : (int) $original;
        }
    }

    protected function syncRelatedKandangMaintenance(bool $forceOriginal = false): void
    {
        $currentId = $this->kandang_id ?? null;
        $previousId = $this->originalKandangIdForSync;

        if ($currentId) {
            Kandang::find($currentId)?->syncMaintenanceStatus();
        } elseif ($this->relationLoaded('kandang')) {
            $this->kandang?->syncMaintenanceStatus();
        }

        if ($previousId && ($forceOriginal || $previousId !== $currentId)) {
            Kandang::find($previousId)?->syncMaintenanceStatus();
        }

        $this->originalKandangIdForSync = null;
    }
}
