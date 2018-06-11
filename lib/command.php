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
            ->setName('cache:warmup')
            ->setDescription('Pre-generates cache files for pages and images')
            ->setHelp('Generates cache files for all pages and used images in advance to improve the initial website performance.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getStyle($input, $output);
        $io->title('Cache-Warmup');

        ProgressBar::setFormatDefinition('custom', "%message:-21s%  [%bar%]  %current%/%max% ");


        $warnings = [];
        set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$warnings) {
            $warnings[] = rex_error_handler::getErrorType($errno).": $errstr in $errfile on line $errline";
            return true;
        }, E_WARNING|E_USER_WARNING|E_NOTICE|E_USER_NOTICE|E_DEPRECATED|E_USER_DEPRECATED|E_STRICT);


        $items = cache_warmup_selector::prepareCacheItems(false, false);
        foreach ($items as $k => $v) {

            $generator = false;
            $generatorClass = 'cache_warmup_generator_' . $k;
            if (class_exists($generatorClass)) {
                $generator = new $generatorClass();

                if (!$io->isVerbose()) {
                    $progressBar = new ProgressBar($output, $v['count']);
                    $progressBar->setFormat('custom');
                    // $progressBar->setRedrawFrequency(100);
                    $progressBar->setBarWidth(40);
                    $progressBar->setMessage('Generating ' . $k . '…');
                    $progressBar->start();
                }

                $counter = 1;
                foreach ($v['items'] as $item) {

                    if ($io->isVerbose()) {
                        $io->writeln($counter . '/' . $v['count'] . ' - Generate image ' . $item[0] . ' with mediatype ' . $item[1]);
                    }

                    $generator->generateCache(array($item));

                    if (!$io->isVerbose()) {
                        $progressBar->advance();
                    }
                    $counter++;
                }

                if (!$io->isVerbose()) {
                    $progressBar->finish();
                }
                $io->newLine();
            }
        }

        $io->newLine();
        $io->success('Finished warmup.');


        $warnings = array_unique($warnings);
        dump($warnings);
    }
}
