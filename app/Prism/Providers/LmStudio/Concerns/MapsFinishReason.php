<?php

declare(strict_types=1);

namespace App\Prism\Providers\LmStudio\Concerns;

use App\Prism\Providers\LmStudio\Maps\FinishReasonMap;
use Prism\Prism\Enums\FinishReason;

trait MapsFinishReason
{
    /**
     * @param  array<string, mixed>  $data
     */
    protected function mapFinishReason(array $data): FinishReason
    {
        return FinishReasonMap::map(data_get($data, 'choices.0.finish_reason', ''));
    }
}
