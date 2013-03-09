<?php
/**
 * @author Boris Guéry <guery.b@gmail.com>
 */

namespace Bgy\CoreTracker\Command;

use Bgy\CoreTracker\Sorter\CallSorterStrategy;
use Bgy\CoreTracker\Sorter\ClassNameSorterStrategy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Text\Table\Column;
use Zend\Text\Table\Row;
use Zend\Text\Table\Table;

class CoreStatsCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('bgy:CoreTracker:dump')
            ->setDescription('Dump a concatened file from a core dump')
            ->addArgument('core-dump', InputArgument::REQUIRED, 'The Core Dump file')
            ->addOption('sort-namespace', 'f', InputOption::VALUE_NONE, 'Sort by namespace')
            ->addOption('sort-calls',     'c', InputOption::VALUE_NONE, 'Sort by call count')
            ->addOption('reverse',        'r', InputOption::VALUE_NONE, 'Reverse sort order')
            ->addOption('threshold',      't', InputOption::VALUE_REQUIRED, 'Ignore when calls reach the threshold', -1)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ((false !== $input->getOption('sort-namespace') && false !== $input->getOption('sort-calls')) && ($input->getOption('sort-namespace') xor $input->getOption('sort-calls'))) {

            throw new \InvalidArgumentException('Sort attributes are exclusive');
        }

        $sorter = $input->getOption('sort-calls')
            ? new CallSorterStrategy($input->getOption('reverse'))
            : new ClassNameSorterStrategy($input->getOption('reverse'))
        ;

        $coredumpFile = $input->getArgument('core-dump');

        $threshold = (int) $input->getOption('threshold');

        if (!is_file($coredumpFile)) {

            throw new \RuntimeException(sprintf('"%s" is not a valid file.', $coredumpFile));
        }

        $dump = unserialize(file_get_contents($coredumpFile));

        if (empty($dump) || !isset($dump['collectedData'])) {

            $output->writeln('No collected data.');

            return;
        }

        $collectedData = $dump['collectedData'];

        $sorter->sort($collectedData);

        $table = new Table(array('columnWidths' => array(60, 5), 'padding' => 2));

        $rowCount = 0;
        foreach ($collectedData as $hash => $data) {

            if ($threshold > -1 && $data['calls'] < $threshold) {
                continue;
            }
            $row = new Row();
            $row->appendColumn(new Column($data['className'], Column::ALIGN_LEFT));
            $row->appendColumn(new Column((string) $data['calls'], Column::ALIGN_CENTER));
            $table->appendRow($row);
            ++$rowCount;
        }

        if ($rowCount) {
            $output->write($table->render());
        } else {
            $output->writeln('No data to display');
        }
    }
}
