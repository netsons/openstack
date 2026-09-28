<?php

declare(strict_types=1);

namespace OpenStack\Common\Resource;

/**
 * Represents a resource that can be deleted.
 */
interface DeletableWithUserOptions
{
    /**
     * Permanently delete this resource.
     */
    public function delete(array $userOptions);
}
