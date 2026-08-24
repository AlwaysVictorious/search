<?php

declare(strict_types=1);

/*
 * This file is part of the CMS-IG SEAL project.
 *
 * (c) Victor Kampen <alwaysvictorious@proton.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CmsIg\Seal;

use CmsIg\Seal\Search\SearchQuery;

class SearchService
{
    public function __construct(
        protected readonly EngineInterface $engine
    ) {
    }

    public function builder(string $index): SearchQuery
    {
        return new SearchQuery($this->engine->createSearchBuilder($index));
    }

    public function countDocuments(string $index): int
    {
        return $this->engine->countDocuments($index);
    }
}
