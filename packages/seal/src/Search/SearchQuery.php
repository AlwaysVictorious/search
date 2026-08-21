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

namespace CmsIg\Seal\Search;

use CmsIg\Seal\Search\Condition\Condition;
use CmsIg\Seal\Search\Facet\AbstractFacet;

class SearchQuery
{
    private bool $highlightFallback = false;

    public function __construct(private SearchBuilder $builder)
    {
    }

    public function filter(object $condition): static
    {
        $this->builder->addFilter($condition);

        return $this;
    }

    public function search(string $key): static
    {
        return $this->filter(Condition::search($key));
    }

    public function equal(string $field, string|int|float|bool $value): static
    {
        return $this->filter(Condition::equal($field, $value));
    }

    public function notEqual(string $field, string|int|float|bool $value): static
    {
        return $this->filter(Condition::notEqual($field, $value));
    }

    public function identifier(string $id): static
    {
        return $this->filter(Condition::identifier($id));
    }

    /**
     * @param list<string|int|float|bool> $values
     */
    public function in(string $field, array $values): static
    {
        return $this->filter(Condition::in($field, $values));
    }

    /**
     * @param list<string|int|float|bool> $values
     */
    public function notIn(string $field, array $values): static
    {
        return $this->filter(Condition::notIn($field, $values));
    }

    public function greaterThan(string $field, string|int|float|bool $value): static
    {
        return $this->filter(Condition::greaterThan($field, $value));
    }

    public function greaterThanEqual(string $field, string|int|float|bool $value): static
    {
        return $this->filter(Condition::greaterThanEqual($field, $value));
    }

    public function lessThan(string $field, string|int|float|bool $value): static
    {
        return $this->filter(Condition::lessThan($field, $value));
    }

    public function lessThanEqual(string $field, string|int|float|bool $value): static
    {
        return $this->filter(Condition::lessThanEqual($field, $value));
    }

    public function geoDistance(string $field, float $latitude, float $longitude, int $distance): static
    {
        return $this->filter(Condition::geoDistance($field, $latitude, $longitude, $distance));
    }

    /**
     * @see https://docs.mapbox.com/help/glossary/bounding-box/
     */
    public function geoBoundingBox(string $field, float $northLatitude, float $eastLongitude, float $southLatitude, float $westLongitude): static
    {
        return $this->filter(Condition::geoBoundingBox($field, $northLatitude, $eastLongitude, $southLatitude, $westLongitude));
    }

    public function or(object ...$conditions): static
    {
        return $this->filter(Condition::or(...$conditions));
    }

    public function and(object ...$conditions): static
    {
        return $this->filter(Condition::and(...$conditions));
    }

    public function limit(int $limit): static
    {
        $this->builder->limit($limit);

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->builder->offset($offset);

        return $this;
    }

    /**
     * @param 'asc'|'desc' $direction
     */
    public function addSortBy(string $field, string $direction): static
    {
        $this->builder->addSortBy($field, $direction);

        return $this;
    }

    /**
     * @param array<string> $fields
     */
    public function highlight(array $fields, string $preTag = '<mark>', string $postTag = '</mark>', bool $autoFallback = false): static
    {
        $this->builder->highlight($fields, $preTag, $postTag);
        $this->highlightFallback = $autoFallback;

        return $this;
    }

    public function addFacet(AbstractFacet $facet): static
    {
        $this->builder->addFacet($facet);

        return $this;
    }

    public function distinct(string|null $field): static
    {
        $this->builder->distinct($field);

        return $this;
    }

    public function getResult(): Result
    {
        return $this->builder->getResult();
    }

    public function getArrayResult(string $keyField = 'id'): array
    {
        $documents = iterator_to_array($this->builder->getResult(), false);

        if ($this->highlightFallback) {
            $documents = array_map(function (array $document): array {
                $formatted = $document['_formatted'] ?? [];
                unset($document['_formatted']);

                return array_merge($document, $formatted);
            }, $documents);
        }

        return array_column($documents, null, $keyField);
    }
}
