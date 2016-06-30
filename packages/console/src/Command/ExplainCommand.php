<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExplainCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('explain');
        $this->setDescription('Shows a list of installed coding standards.');
        //         echo '        -e            Explain a standard by showing the sniffs it includes'.PHP_EOL;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        /*
         * Method epxlain all standrs
         */


        /*            $standards = $this->config->standards;
            foreach ($standards as $standard) {
                $this->config->standards = array($standard);
                $ruleset = new Ruleset($this->config);
                $ruleset->explain();
            }

        */

        /*
         *
         * METHOD EXPLIAIN STANDARD
         *         $sniffs = array_keys($this->sniffs);
        sort($sniffs);

        ob_start();

        $lastStandard = null;
        $lastCount    = '';
        $sniffCount   = count($sniffs);

        // Add a dummy entry to the end so we loop
        // one last time and clear the output buffer.
        $sniffs[] = '';

        echo PHP_EOL."The $this->name standard contains $sniffCount sniffs".PHP_EOL;

        ob_start();

        foreach ($sniffs as $i => $sniff) {
            if ($i === $sniffCount) {
                $currentStandard = null;
            } else {
                $parts = explode('\\', $sniff);

                $currentStandard = $parts[2];
                if ($lastStandard === null) {
                    $lastStandard = $currentStandard;
                }
            }

            if ($currentStandard !== $lastStandard) {
                $sniffList = ob_get_contents();
                ob_end_clean();

                echo PHP_EOL.$lastStandard.' ('.$lastCount.' sniffs)'.PHP_EOL;
                echo str_repeat('-', (strlen($lastStandard.$lastCount) + 10));
                echo PHP_EOL;
                echo $sniffList;

                $lastStandard = $parts[2];
                $lastCount    = 0;

                if ($currentStandard === null) {
                    break;
                }

                ob_start();
            }

            echo '  '.$parts[2].'.'.$parts[4].'.'.substr($parts[5], 0, -5).PHP_EOL;
            $lastCount++;

         */
        $output->write('eee');
        // get all coding standrads
    }
}
