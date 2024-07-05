<?php

use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\Operator\OperatorLinebreakFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoSuperfluousPhpdocTagsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Whitespace\NoExtraBlankLinesFixer;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedClassNameInAnnotationSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedExceptionsSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->sets([SetList::PSR_12, SetList::NAMESPACES]);
    $ecsConfig->ruleWithConfiguration(
        TrailingCommaInMultilineFixer::class,
        [
            'elements' => [
                'arguments',
                'parameters',
                'arrays',
                'match',
                ],
            ],
    );

    $ecsConfig->ruleWithConfiguration(ForbiddenFunctionsSniff::class, [
        'forbiddenFunctions' => [
            'dump' => null,
        ],
    ]);

    $ecsConfig->rules([
        FullyQualifiedExceptionsSniff::class,
        FullyQualifiedClassNameInAnnotationSniff::class,
        FunctionTypehintSpaceFixer::class,
        DeclareStrictTypesFixer::class,
        OrderedClassElementsFixer::class,
        NoEmptyPhpdocFixer::class,
        OperatorLinebreakFixer::class,
        NoExtraBlankLinesFixer::class
    ]);

    $ecsConfig->ruleWithConfiguration(
        NoSuperfluousPhpdocTagsFixer::class, ['allow_mixed' => true]
    );

    $ecsConfig->skip([]);
};
