<?php declare(strict_types=1);

namespace App\Console;

use App\Presentation\Population;
use App\Services\Cleaner;
use App\Services\Parser;
use App\Services\Reporter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FileParserCommand
{
    /**
     * @var Cleaner
     */
    private Cleaner $cleaner;

    private Parser $parser;
    /**
     * @var Reporter
     */
    private Reporter $reporter;

    public function __construct(
        Cleaner $cleaner, Parser $parser, Reporter $reporter)
    {
        $this->cleaner = $cleaner;
        $this->parser = $parser;
        $this->reporter = $reporter;
    }

    public function __invoke(
        InputInterface $input, OutputInterface $output): ?int
    {
        $this->cleaner->clearRepositories();

        $file = $input->getArgument('file');
        $output->writeln(sprintf(
            'Подождите, идет обработка файла <info>%s</info>',
            $file));

        $result = $this->parser->__invoke($file);
        $payload = $this->reporter->__invoke();

        foreach ($payload as $item) {
            /* @var Population $item */
            $output->writeln(
                "{$item->getCity()}: {$item->getAmount()}");
        }

        return $result;
    }
}
