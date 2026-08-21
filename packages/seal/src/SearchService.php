<?php

declare(strict_types=1);

namespace CmsIg\Seal;

use CmsIg\Seal\Search\SearchQuery;

class SearchService
{
    public function __construct(
        private readonly EngineInterface $engine
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
