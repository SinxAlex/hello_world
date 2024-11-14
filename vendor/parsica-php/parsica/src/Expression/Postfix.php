<?php declare(strict_types=1);
/*
 * This file is part of the Parsica library.
 *
 * Copyright (c) 2020 Mathias Verraes <mathias@verraes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Parsica\Parsica\Expression;

use Parsica\Parsica\Parser;
use function Parsica\Parsica\choice;
use function Parsica\Parsica\keepFirst;
use function Parsica\Parsica\pure;

/**
 * @internal
 * @template TSymbol
 * @template TExpressionAST
 */
final class Postfix implements ExpressionType
{
    /** @psalm-var non-empty-list<UnaryOperator<TSymbol, TExpressionAST>> */
    private array $operators;

    /**
     * @psalm-param non-empty-list<UnaryOperator<TSymbol, TExpressionAST>> $operators
     */
    function __construct(array $operators)
    {
        $this->operators = $operators;
    }

    public function buildPrecedenceLevel(Parser $previousPrecedenceLevel): Parser
    {
        $operatorParsers = [];
        foreach ($this->operators as $operator) {
            $operatorParsers[] =
                pure($operator->transform())
                    ->apply(keepFirst($previousPrecedenceLevel, $operator->symbol()))
                    ->label($operator->label());
        }

        return choice(...$operatorParsers)->or($previousPrecedenceLevel);
    }
}
