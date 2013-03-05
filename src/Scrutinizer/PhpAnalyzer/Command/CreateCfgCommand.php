<?php

namespace Scrutinizer\PhpAnalyzer\Command;

use JMS\PhpManipulator\PhpParser\ParseUtils;
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphvizSerializer;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\AbstractScopedCallback;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCfgCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('create-cfg')
            ->setDescription('Creates a control flow graph for the given scope.')
            ->addArgument('file', InputArgument::REQUIRED, 'The file that contains the PHP code.')
            ->addArgument('element', InputArgument::REQUIRED, 'The code element for which the CFG should be created. Currently, only class methods are supported in the form: ClassName::foomethod')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ( ! is_file($file = $input->getArgument('file'))) {
            throw new \InvalidArgumentException(sprintf('The file "%s" does not exist.', $file));
        }

        if (false === strpos($input->getArgument('element'), '::')) {
            throw new \InvalidArgumentException(sprintf('The element must be of the form "ClassName::methodName", but got "%s".', $input->getArgument('element')));
        }

        list($class, $method) = explode('::', $input->getArgument('element'));
        $ast = ParseUtils::parse(file_get_contents($file));
        NodeTraversal::traverseWithCallback($ast, $callback = new SearchCallback($class, $method));

        if (null === $cfg = $callback->getCfg()) {
            throw new \RuntimeException(sprintf('The code element "%s" was not found in the given file.', $input->getArgument('element')));
        }

        $output->writeln((new GraphvizSerializer())->serialize($cfg));
    }
}

class SearchCallback extends AbstractScopedCallback
{
    private $class;
    private $method;
    private $cfg;

    public function __construct($className, $methodName)
    {
        $this->class = $className;
        $this->method = $methodName;
    }

    public function enterScope(NodeTraversal $t)
    {
        $root = $t->getScopeRoot();

        if ( ! $root instanceof \PHPParser_Node_Stmt_ClassMethod) {
            return;
        }

        if ($root->name !== $this->method) {
            return;
        }

        $container = $root->getAttribute('parent')->getAttribute('parent');
        $className = implode("\\", $container->namespacedName->parts);

        if ($this->class !== substr($className, -1 * strlen($this->class))) {
            return;
        }

        $this->cfg = $t->getControlFlowGraph();
    }

    public function getCfg()
    {
        return $this->cfg;
    }
}