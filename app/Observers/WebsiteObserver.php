<?php

namespace App\Observers;

use App\Models\Website;
use App\Libs\Helpers\Websites;
use App\Events\Website\Created;
use App\Libs\Helpers\Projects;

/**
 * Website Observer class.
 */
class WebsiteObserver
{
    /**
     * Handle the website "creating" event.
     *
     * This method doesn't use Events as
     * it updates current model fields.
     *
     * It will perform an HTTP request to the website,
     * following redirections, and will obtain the lastest
     * URL served.
     * From that URL, it will only keep the scheme and the host.
     *
     * @param  \App\Models\Website $website
     * @return void
     */
    public function creating(Website $website)
    {
        $website->url = Websites::getFinalURL($website->url);
        $website->url = Websites::cleanURL($website->url);
    }

    /**
     * Handle the website "created" event.
     *
     * @param  \App\Models\Website $website
     * @return void
     */
    public function created(Website $website)
    {
        event(new Created($website));
    }
}
