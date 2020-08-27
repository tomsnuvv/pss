<?php

namespace App\Libs\Contracts\Modules\Abstracts;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Carbon\Carbon;

/**
 * Info Module abstract.
 */
abstract class Info extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->updateLastestInfoDate();
    }

    /**
     * Update the model latest info check field.
     */
    protected function updateLastestInfoDate()
    {
        $this->model->latest_info_check = Carbon::now();
        $this->model->save();
    }
}
