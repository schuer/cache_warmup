<?php

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class rex_cache_warmup_command_warmup extends rex_console_command
{
    protected function configure()
    {
        $this
            ->setName('cache_warmup:warmup')
            ->setDescription('Generates cache files for pages and images')
            ->setHelp('Generates cache files for all pages and used images in advance to improve the initial website performance.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getStyle($input, $output);

        $io->title('Cache-Warmup');


        ProgressBar::setFormatDefinition('custom', "%message:-22s%\n[%bar%] %percent:3s%%\n%current:6s%/%max:-6s% %elapsed:60s%    ");
        $progressBar = new ProgressBar($output, 28000);
        $progressBar->setFormat('custom');

        $progressBar->setBarWidth(100);
        $progressBar->setMessage('Step 1: Generating pages…');
        $progressBar->start();

        $progressBar->setRedrawFrequency(10);

        $i = 0;
        while ($i++ < 28000) {
            usleep(500);
            $progressBar->advance();

            if ($i == 8000) {
                $progressBar->setMessage('Step 2: Generating images…');
            }
        }

        $progressBar->finish();

        $io->newLine(2);


        $io->success('Finished warmup.');
    }
}
