<?php

use \Highcore\Models\Model;
use \Highcore\Models\Environment;
use \Highcore\Models\Stack;

class ModelTest extends TestCase {

    /** @var  Model */
    protected $stack_model;

    public function setUp()
    {
        parent::setUp();
        $input = json_decode(file_get_contents('tests/data/env1_create.json'), true);
        $this->stack_model = Stack::make($input);
        $this->stack_model->assign(Environment::make([
            'name' => 'integration',
            'parameters' => [
                [   "id" => "key_name",
                    "value" => "highcore-testing",
                ], [
                    "id" => "template_bucket",
                    "value" => "highcore-templates",
                ], [
                    "id" => "cloud_credentials",
                    "sensitive" => true,
                    "value" => [
                        "access_key" => "ZA8X506GX7X93K2VLH7M",
                        "secret_key" => "ekc5dmdQZwsd2X8O11Fu64i9puzZXIaEfsaoVO5OK55LcBLaTGgavguVuF78h25PlQBir0vKKfYSuTCFLr7eVyrAaC29G6Fdp5yO6779oa3lK7cCuNlb1wmKrxlFLZd5WddmZDu1c8XPHAtF4DS8OUaDNNJeU9KGhbGC1XqLRmWTs82XKVO3HZ2i1nBXeJA8bMOlrkXaTeQzwo5t8EwzEWFM7z84VR7sZyL8Yt7mrgRq9Wnt5QQnXV4aSn0P1R66jRCUAkzrPqKy9VjRjLgNyDme",
                    ]]],
            'project' => [
                'name' => 'highcore',
            ]
        ]));
    }

    /**
     * Model casting test
     *
     * @return void
     */
    public function testModelCast()
    {
        $this->assertInstanceOf('\Highcore\Models\Environment', $this->stack_model->environment);
        $this->assertInstanceOf('\Highcore\Models\Project', $this->stack_model->environment->project);
        $this->assertInstanceOf('\Highcore\Models\Collection', $this->stack_model->environment->parameters);
        $this->assertInstanceOf('\Highcore\Models\Parameter', $this->stack_model->environment->parameters->first());
    }

    /**
     * Model getters test
     *
     * @return void
     */
    public function testModelGet()
    {
        $project_data = $this->stack_model->get('environment.project.');
        $project_model = $this->stack_model->get('environment.project');
        $stack_data = $this->stack_model->get();
        $stack_name = $this->stack_model->name;

        $this->assertEquals(['name' => 'highcore'], $project_data);
        $this->assertInstanceOf('\Highcore\Models\Project', $project_model);
        $this->assertEquals(array_keys($this->stack_model->toArray()), array_keys($stack_data));
        $this->assertEquals($this->stack_model->get('name'), $stack_name);
    }

}
