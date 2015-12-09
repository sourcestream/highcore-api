<?php namespace Highcore\Util\GitElephant;

use GitElephant\Command\Caller\Caller;
use GitElephant\Exception\InvalidRepositoryPathException;
use GitElephant\GitBinary;
use Symfony\Component\Process\Process;

class AuthenticatedCaller extends Caller
{
    /** @var GitBinary */
    protected $binary;
    protected $repositoryPath;
    protected $rawOutput;
    protected $outputLines;

    protected $env;

    public function __construct(GitBinary $binary, $repositoryPath, AuthenticationProvider $auth)
    {
        $this->binary         = $binary;
        if (!is_dir($repositoryPath)) {
            throw new InvalidRepositoryPathException($repositoryPath);
        }
        $this->repositoryPath = $repositoryPath;

        $this->env = $auth->getEnv();
    }

    public function execute($cmd, $git = true, $cwd = null, $acceptedExitCodes = array(0))
    {
        if ($git) {
            $cmd = $this->binary->getPath() . ' ' . $cmd;
        }

        $process = new Process($cmd, is_null($cwd) ? $this->repositoryPath : $cwd, $this->env);
        $process->setTimeout(15000);
        $process->run();
        if (!in_array($process->getExitCode(), $acceptedExitCodes)) {
            $text = 'Exit code: ' . $process->getExitCode();
            $text .= ' while executing: "' . $cmd;
            $text .= '" with reason: ' . $process->getErrorOutput();
            $text .= "\n" . $process->getOutput();
            throw new \RuntimeException($text);
        }
        $this->rawOutput = $process->getOutput();
        // rtrim values
        $values = array_map('rtrim', explode(PHP_EOL, $process->getOutput()));
        $this->outputLines = $values;

        return $this;
    }

    /**
     * returns the output of the last executed command as an array of lines
     *
     * @param bool $stripBlankLines remove the blank lines
     *
     * @return array
     */
    public function getOutputLines($stripBlankLines = false)
    {
        if ($stripBlankLines) {
            $output = array();
            foreach ($this->outputLines as $line) {
                if ('' !== $line) {
                    $output[] = $line;
                }
            }

            return $output;
        }

        return $this->outputLines;
    }

    /**
     * Get RawOutput
     *
     * @return string
     */
    public function getRawOutput()
    {
        return $this->rawOutput;
    }
}