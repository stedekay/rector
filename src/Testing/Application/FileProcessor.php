<?php declare(strict_types=1);

namespace Rector\Testing\Application;

use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use Rector\Contract\Parser\ParserInterface;
use Rector\NodeTraverser\CloningNodeTraverser;
use Rector\Printer\FormatPerservingPrinter;
use Rector\Rector\RectorCollector;
use SplFileInfo;

final class FileProcessor
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @var FormatPerservingPrinter
     */
    private $codeStyledPrinter;

    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * @var NodeTraverser
     */
    private $nodeTraverser;

    /**
     * @var RectorCollector
     */
    private $rectorCollector;

    /**
     * @var CloningNodeTraverser
     */
    private $cloningNodeTraverser;

    public function __construct(
        CloningNodeTraverser $cloningNodeTraverser,
        ParserInterface $parser,
        FormatPerservingPrinter $codeStyledPrinter,
        Lexer $lexer,
        NodeTraverser $nodeTraverser,
        RectorCollector $rectorCollector
    ) {
        $this->parser = $parser;
        $this->codeStyledPrinter = $codeStyledPrinter;
        $this->lexer = $lexer;
        $this->nodeTraverser = $nodeTraverser;
        $this->rectorCollector = $rectorCollector;
        $this->cloningNodeTraverser = $cloningNodeTraverser;
    }

    /**
     * @param string[] $rectorClasses
     */
    public function processFileWithRectors(SplFileInfo $file, array $rectorClasses): string
    {
        foreach ($rectorClasses as $rectorClass) {
            /** @var NodeVisitor $rector */
            $rector = $this->rectorCollector->getRector($rectorClass);
            $this->nodeTraverser->addVisitor($rector);
        }

        return $this->processFile($file);
    }

    /**
     * See https://github.com/nikic/PHP-Parser/issues/344#issuecomment-298162516.
     */
    public function processFile(SplFileInfo $file): string
    {
        $oldStmts = $this->parser->parseFile($file->getRealPath());
        $oldTokens = $this->lexer->getTokens();
        $newStmts = $this->cloningNodeTraverser->traverse($oldStmts);

        $newStmts = $this->nodeTraverser->traverse($newStmts);

        return $this->codeStyledPrinter->printToString($newStmts, $oldStmts, $oldTokens);
    }
}
