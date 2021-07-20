<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Query\Common\CriterionVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use EzSystems\EzPlatformSolrSearchEngine\Query\CriterionVisitor;

/**
 * Visits the ContentTypeIdentifier criterion.
 */
class ContentTypeIdentifierIn extends CriterionVisitor
{
    public function canVisit(Criterion $criterion): bool
    {
        return
            $criterion instanceof Criterion\ContentTypeIdentifier
            && (
                ($criterion->operator ?: Operator::IN) === Operator::IN ||
                Operator::EQ === $criterion->operator
            );
    }

    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null): string
    {
        return '('.
            implode(
                ' OR ',
                array_map(
                    static function ($value) {
                        return 'content_type_identifier_id:"'.$value.'"';
                    },
                    $criterion->value
                )
            ).
            ')';
    }
}
