<?php namespace Highcore\Util\GitElephant\Command;

use GitElephant\Command\MainCommand;

class ResetCommand extends \GitElephant\Command\BaseCommand
{
    const RESET_SOFT = 'soft';
    const RESET_MIXED = 'mixed';
    const RESET_HARD = 'hard';
    const RESET_MERGE = 'merge';
    const RESET_KEEP = 'keep';

    public function reset($mode, $commit){
        $this->clearAll();
        $this->addCommandName(MainCommand::GIT_RESET);
        $this->addCommandArgument('--'.$mode);
        $this->addCommandSubject($commit);

        return $this->getCommand();
    }

    public function clean()
    {
        $this->clearAll();
        $this->addCommandName('clean');
        $this->addCommandArgument('-dfx');
        return $this->getCommand();
    }

}