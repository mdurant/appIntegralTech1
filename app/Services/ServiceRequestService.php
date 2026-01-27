<?php

namespace App\Services;

use App\Models\ServiceCategory;
use App\Models\ServiceFormField;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestFieldAnswer;
use App\Models\SystemSetting;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;

class ServiceRequestService
{
    public function createDraft(
        User $actor,
        Tenant $tenant,
        ServiceCategory $category,
        string $title,
        string $description,
        array $answers = [],
        ?int $regionId = null,
        ?int $communeId = null,
    ): ServiceRequest {
        $serviceRequest = ServiceRequest::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'created_by_user_id' => $actor->id,
            'title' => $title,
            'description' => $description,
            'region_id' => $regionId,
            'commune_id' => $communeId,
            'status' => ServiceRequestStatus::Draft,
        ]);

        if (count($answers) > 0) {
            $allowedFieldIds = ServiceFormField::query()
                ->where('service_category_id', $category->id)
                ->pluck('id')
                ->all();

            $allowed = array_fill_keys($allowedFieldIds, true);

            foreach ($answers as $fieldId => $value) {
                $fieldId = (int) $fieldId;

                if (! isset($allowed[$fieldId])) {
                    continue;
                }

                if ($value === null || $value === '') {
                    continue;
                }

                ServiceRequestFieldAnswer::updateOrCreate(
                    [
                        'service_request_id' => $serviceRequest->id,
                        'service_form_field_id' => $fieldId,
                    ],
                    [
                        'value' => is_array($value) ? json_encode($value) : (string) $value,
                    ],
                );
            }
        }

        return $serviceRequest;
    }

    public function updateDraft(ServiceRequest $serviceRequest, string $title, string $description, ServiceCategory $category): ServiceRequest
    {
        $serviceRequest->update([
            'title' => $title,
            'description' => $description,
            'category_id' => $category->id,
        ]);

        return $serviceRequest;
    }

    public function publish(ServiceRequest $serviceRequest): ServiceRequest
    {
        $expiryDays = (int) SystemSetting::get('service_request_expiry_days', 15);

        $serviceRequest->update([
            'status' => ServiceRequestStatus::Published,
            'published_at' => now(),
            'expires_at' => now()->addDays($expiryDays),
        ]);

        return $serviceRequest;
    }

    public function reopen(ServiceRequest $serviceRequest): ServiceRequest
    {
        $expiryDays = (int) SystemSetting::get('service_request_expiry_days', 15);

        $serviceRequest->update([
            'status' => ServiceRequestStatus::Published,
            'published_at' => $serviceRequest->published_at ?? now(),
            'expires_at' => now()->addDays($expiryDays),
        ]);

        return $serviceRequest;
    }
}
