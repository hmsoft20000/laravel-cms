<?php

namespace HMsoft\Cms\Traits\Faqs;

use HMsoft\Cms\Models\Shared\Faq;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFaqs
{
    /**
     * Get all of the model's FAQs (Polymorphic).
     */
    public function faqs(): MorphMany
    {
        return $this->morphMany(Faq::class, 'owner')->orderBy('sort_number');
    }
}
