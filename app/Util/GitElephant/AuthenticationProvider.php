<?php namespace Highcore\Util\GitElephant;

class AuthenticationProvider
{
    public function __construct($privateKeyPath, $gitSsh = null)
    {
        $this->privateKeyPath = $privateKeyPath;
        $this->gitSsh = $gitSsh ?: app_path('scripts/git-ssh');
    }

    public function getEnv(){
        return [
            'GIT_SSH' => $this->gitSsh,
            'PKEY' => $this->privateKeyPath
        ];
    }
}