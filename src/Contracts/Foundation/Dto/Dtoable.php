<?php

namespace Laravelayers\Contracts\Foundation\Dto;

/**
 * @see \Laravelayers\Foundation\Models\ModelToDto
 * @see \Laravelayers\Pagination\Paginator
 */
interface Dtoable
{
    /**
     * Convert the object instance in the DTO format.
     *
     * @return array
     */
    public function toDto();
}
