<?php

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class rex_cache_warmup_command extends rex_console_command
{
    protected function configure()
    {
        $this
            ->setName('cache_warmup:warmup')
            ->setDescription('Generates cache')
            ->setHelp('Generates cache files for all pages and used images in advance to improve the initial website performance.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getStyle($input, $output);
        $io->title('Cache-Warmup');

        ProgressBar::setFormatDefinition('custom', "%message:-21s%  [%bar%]  %current%/%max% ");


        $items = cache_warmup_selector::prepareCacheItems(false, false);
        foreach ($items as $k => $v) {

            $generator = false;
            $generatorClass = 'cache_warmup_generator_' . $k;
            if (class_exists($generatorClass)) {
                $generator = new $generatorClass();

                $progressBar = new ProgressBar($output, $v['count']);
                $progressBar->setFormat('custom');
                // $progressBar->setRedrawFrequency(100);
                $progressBar->setBarWidth(40);
                $progressBar->setMessage('Generating ' . $k . '…');
                $progressBar->start();

                foreach ($v['items'] as $item) {
                    $generator->generateCache(array($item));
                    $progressBar->advance();
                }

                $progressBar->finish();
                $io->newLine();
            }
        }

        $io->newLine(2);
        $io->success('Finished warmup.');
    }
}
