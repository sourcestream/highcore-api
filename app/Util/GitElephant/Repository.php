<?php namespace Highcore\Util\GitElephant;

use GitElephant\Command\BaseCommand;
use GitElephant\Command\Caller\Caller;
use GitElephant\Command\MainCommand;
use GitElephant\Command\RemoteCommand;
use GitElephant\GitBinary;
use GitElephant\Objects\Remote;
use Highcore\Util\GitElephant\Command\ResetCommand;

class Repository extends \GitElephant\Repository
{
    /** @var Caller */
    protected $caller;

    /**
     * @param $repoPath
     * @param $privateKeyPath
     * @param GitBinary|null $binary
     * @param null $name
     * @return self
     */
    public static function openAuthenticated($repoPath, $privateKeyPath, GitBinary $binary = null, $name = null)
    {
        if (is_null($binary)) {
            $binary = new GitBinary();
        }

        $repo = new static($repoPath, $binary, $name);
        $repo->setCaller(new AuthenticatedCaller($binary, $repoPath, $privateKeyPath));

        return $repo;
    }

    public function setCaller($caller){
        parent::setCaller($caller);
        $this->caller = $caller;
    }

    public function reset($commit = null, $mode = null, $cleanupUntracked = false)
    {
        $commit = $commit ?: 'HEAD';
        $mode = $mode ?: ResetCommand::RESET_HARD;
        $this->caller->execute(ResetCommand::getInstance($this)->reset($mode, $commit), true, null);

        if ($cleanupUntracked) {
            $this->caller->execute(ResetCommand::getInstance($this)->clean(), true, null);
        }

        return $this;
    }

    /**
     * @param string $name remote name
     * @param string $url  remote url
     *
     * @throws \RuntimeException
     * @throws \Symfony\Component\Process\Exception\LogicException
     * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Process\Exception\RuntimeException
     * @return Repository
     */
    public function rmRemote($name)
    {
        $this->caller->execute(sprintf('remote rm '.$name));

        return $this;
    }

    public function setRemote($name, $url, $add = false, $push = false)
    {
        if(isset($this->getRemotes()[$name])){
            $cmd = ['remote set-url'];
            $add && $cmd[] = '--add';
            $push && $cmd[] = '--push';
            $cmd[] = '%s %s'; //$name $url
            $this->caller->execute(sprintf(implode(' ', $cmd), $name, $url));
        } else {
            $this->addRemote($name, $url);
        }

        return $this;
    }

    public function getRemotes($queryRemotes = true)
    {
        $retval = [];
        foreach(parent::getRemotes($queryRemotes) as $r){
            /** @var $r Remote */
            $retval[$r->getName()] = $r;
        }

        return $retval;
    }

    public function isInitialized(){
        return is_dir($this->getPath().DIRECTORY_SEPARATOR.'.git');
    }
}