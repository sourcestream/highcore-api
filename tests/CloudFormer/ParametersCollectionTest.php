<?php

use \Highcore\Services\CloudFormer\ParametersCollection;
use \Highcore\Models\Collection;
use \Highcore\Models\Parameter;

class ParametersCollectionTest extends TestCase {

    /** @var  ParametersCollection */
    protected $cfn_params;
    /** @var  Collection */
    protected $stack_params;

    public function setUp()
    {
        parent::setUp();
        $this->cfn_params = ParametersCollection::make([
            'StackName' => 'test-stack',
            'Capabilities' => ['CAPABILITY_IAM'],
        ]);
        $this->stack_params = Collection::make([
            Parameter::make([
                'id' => 'cloud_credentials',
                'sensitive' => true,
                'value' => ['access_key' => 'test-key', 'secret_key' => 'xWva3qmvoAgCK0rUcN3gb9FJdLFXhSln1lCbKBl9se8GK9RpbIIcuhM98NGzN18Cv6dvjhL7rwgxYHuj7GDLzOYKTLEjNzExfAJEnZRPKJ94Pk9pgGhs7D9KVLcMYCl4plHsUkckijaXUYLJj2EojVjajIwkjLVkChosefq5RjVkyO08NSIzmsohRKBBTg6tpJys42FVmTNiFJ1a8PMoOgbmmK3xj2J1eEAybWrgcpwuu87YXDaBwbnhLuVWqqUTGf9jHTaaIxzG590fBEwNxOV8']
            ]),
        ], $class = null, $key_by = 'id');
    }

	public function testMerge()
	{
        $merged_params = $this->cfn_params->merge($this->stack_params);
        $all = $merged_params->toArray();
        $this->assertArrayHasKey('cloud_credentials', $all);
        $this->assertArrayHasKey('StackName', $all);
        $this->assertArrayHasKey('Capabilities', $all);
	}

	public function testSensitive()
	{
        $merged_params = $this->cfn_params->merge($this->stack_params);
        $all = $merged_params->sensitive()->toArray();
        $this->assertArrayHasKey('cloud_credentials', $all);
        $this->assertArrayNotHasKey('StackName', $all);
        $this->assertArrayNotHasKey('Capabilities', $all);
        // Assert immutability of the original collection
        $this->assertArrayNotHasKey('cloud_credentials', $this->cfn_params->toArray());
    }

	public function testInsensitive()
	{
        $merged_params = $this->cfn_params->merge($this->stack_params);
        $all = $merged_params->insensitive()->toArray();
        $this->assertArrayNotHasKey('cloud_credentials', $all);
        $this->assertArrayHasKey('StackName', $all);
        $this->assertArrayHasKey('Capabilities', $all);
	}

    public function testStudly()
    {
        $merged_params = $this->cfn_params->merge($this->stack_params);
        $all = $merged_params->studly()->toArray();
        $this->assertArrayHasKey('CloudCredentials', $all);
        $this->assertArrayNotHasKey('cloud_credentials', $all);
        $this->assertArrayHasKey('StackName', $all);
        $this->assertArrayHasKey('Capabilities', $all);
    }

    public function testFlat()
    {
        $merged_params = $this->cfn_params->merge($this->stack_params);
        $all = $merged_params->flat()->toArray();
        $this->assertArrayHasKey('cloud_credentials_access_key', $all);
        $this->assertArrayNotHasKey('cloud_credentials', $all);
    }

    public function testDecrypt()
    {
        $encrypted_key = 'hu8gZ19fINO3HrPUQkHgqdZ5qaLcKFg4kRgUISFOhQWiAWVYbyo53gE6JQ61gZ64tvclc0RtVghPZCGLvQSJvmQxFwdxzXF7mqvJdepFXhIdwJ6PMIE701kGmdjNoSoXFiOACb73c70DZQmzLA4sVulwcTtJcMWsRgpiC4PayrNWun9x0bWsvsDNNZ2YPZL8yf0paeyfLPsMONfqakSUrJfprYFtjbEkWcgx0hluvZpHj3Nl6AU3TFBJS6b8qFrp99qVsS7wBKAUNSUrDBzGjYNy';
        $decrypted_key = 'FOGdGgtmjWZufLyyCe91QG1njTKrylvn8wv0RaLmoCd7sEhDKAjJ6iHGXJPRrfdm2Mtw2nlpBjbukKBU';
        $credentials_param = Parameter::make([
            'id' => 'cloud_credentials',
            'sensitive' => true,
            'value' => ['access_key' => 'test-key', 'secret_key' => $encrypted_key]
        ]);
        $params = ParametersCollection::make([
            'StackName' => 'test-stack',
            'cloud_credentials' => $credentials_param,
        ]);
        $unencrypted_values = [
            'test-stack',
            'cloud_credentials',
            true,
            'test-key',
        ];
        foreach ($unencrypted_values as $value) {
            Crypt::shouldReceive('decrypt')->once()
                ->with($value)
                ->andThrow(new \Illuminate\Contracts\Encryption\DecryptException('Invalid data.'));
        }
        Crypt::shouldReceive('decrypt')->once()
            ->with($encrypted_key)
            ->andReturn($decrypted_key);
        $decrypted_params = $params->decrypted()->toArray();
        $this->assertEquals(array_get($decrypted_params, 'cloud_credentials.value.secret_key'), $decrypted_key);
    }

}
