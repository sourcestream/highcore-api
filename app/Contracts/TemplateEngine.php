<?php namespace Highcore\Contracts;

use Highcore\Models\Template;

interface TemplateEngine {

    /**
     * Get a list of parameters defined in the template
     *
     * @param Template $template
     * @return array
     */
    public function getParams(Template $template);

    /**
     * Get array of components defined in the template
     *
     * @param Template $template
     * @return array
     */
    public function getComponents(Template $template);

    /**
     * Get CloudFormation Template
     *
     * @param Template $template
     * @param string $stack_definition
     * @param bool $update
     * @return array
     */
    public function getTemplate(Template $template, $stack_definition, $update = true);

    /**
     * Get array of components defined in the template
     *
     * @param Template $template
     * @return array
     */
    public function getInputDefinition(Template $template);

    /**
     * Updates the template repository
     *
     * @param Template $template
     * @return void
     */
    public function updateTemplate(Template $template);

    /**
     * Deletes local template files
     *
     * @param Template $template
     * @return void
     */
    public function cleanupTemplate(Template $template);
}
