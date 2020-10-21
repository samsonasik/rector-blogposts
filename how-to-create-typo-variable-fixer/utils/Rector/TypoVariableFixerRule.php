<?php

declare(strict_types=1);

namespace Utils\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use Rector\Core\Rector\AbstractRector;
use Rector\Core\RectorDefinition\CodeSample;
use Rector\Core\RectorDefinition\RectorDefinition;

final class TypoVariableFixerRule extends AbstractRector
{
    public function getNodeTypes(): array
    {
        return [Variable::class];
    }

    public function refactor(Node $node): ?Node
    {
        // get the variable name
        $variableName = $this->getName($node);

        // get the library content
        $library = include 'utils/library.php';

        foreach ($library as $correctWord => $commonTypos) {
            if (! in_array($variableName, $commonTypos, true)) {
                continue;
            }

            $node->name = $correctWord;
            return $node;
        }

        return null;
    }

    public function getDefinition(): RectorDefinition
    {
        return new RectorDefinition(
            'Change Typo in variable', [
                new CodeSample(
                    // code before
                    '$previuos',
                    // code after
                    '$previous'
                ),
            ]
        );
    }
}