<?php namespace Highcore\Contracts;

use Highcore\Models\Collection;
use Highcore\Models\Stack;
use Highcore\Models\Template;

interface CloudFormer {

    /**
     * Create new Cloud Stack
     *
     * @param  Stack  $stack
     * @return void
     */
    public function createStack(Stack $stack);

    /**
     * Generate template for Cloud Stack
     *
     * @param  Stack  $stack
     * @return string Generated template
     */
    public function createTemplate(Stack $stack);

    /**
     * Update Cloud Stack
     *
     * @param  Stack  $stack
     * @return Stack
     */
    public function updateStack(Stack $stack);

    /**
     * Compare new Template against actual one
     *
     * @param  Stack  $stack
     * @return string
     */
    public function diffTemplate(Stack $stack);

    /**
     * Triggers provisioning on applicable stack components
     *
     * @param Stack $stack
     * @return bool
     */
    public function provisionStack(Stack $stack);

    /**
     * Delete Cloud Stack
     *
     * @param  Stack  $stack
     * @return Stack
     */
    public function deleteStack(Stack $stack);

    /**
     * Describe Cloud Stack
     *
     * @param  Stack  $stack
     * @return Stack
     */
    public function describeStack(Stack $stack);

    /**
     * Get available parameters for Cloud Stack
     *
     * @param  Template  $template
     * @return Collection
     */
    public function getTemplateParams(Template $template);

    /**
     * Get available parameters for Cloud Stack
     *
     * @param  Template  $template
     * @return Collection
     */
    public function getTemplateComponents(Template $template);

}
