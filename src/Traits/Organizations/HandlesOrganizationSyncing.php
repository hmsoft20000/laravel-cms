<?php

namespace HMsoft\Cms\Traits\Organizations;

use Illuminate\Database\Eloquent\Model;

trait HandlesOrganizationSyncing
{
    protected function syncOrganizations(Model $model, array $data): void
    {
        if (!method_exists($model, 'organizations')) return;

        $organizationsToSync = [];
        // Assuming role 1 = Partner
        foreach ($data['partner_ids'] ?? [] as $partnerId) {
            $organizationsToSync[$partnerId] = ['role' => 'partner'];
        }
        // Assuming role 2 = Sponsor
        foreach ($data['sponsor_ids'] ?? [] as $sponsorId) {
            $organizationsToSync[$sponsorId] = ['role' => 'sponsor'];
        }

        $model->organizations()->sync($organizationsToSync);
    }
}
