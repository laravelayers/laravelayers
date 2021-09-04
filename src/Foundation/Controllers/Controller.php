<?php

namespace Laravelayers\Foundation\Controllers;

use App\Http\Controllers\Controller as BaseController;
use Laravelayers\Contracts\Foundation\Services\Service as ServiceContract;
use Laravelayers\Foundation\Services\Service;

/**
 * The base class for controllers.
 */
class Controller extends BaseController
{
    use AuthorizesRequests;

    /**
     * Service instance.
     *
     * @var Service|ServiceContract
     */
    protected $service;

    /**
     * Get the number of repository items to return per page.
     *
     * @return int|null
     */
    public function getPerPage()
    {
        return $this->service->getPerPage();
    }

    /**
     * Get the name and direction of the current sorting.
     *
     * @return array
     */
    public function getSorting()
    {
        return $this->service->getSorting();
    }

    /**
     * Get the status name and comparison operator.
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->service->getStatus();
    }
}
