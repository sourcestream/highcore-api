<?php use Swagger\Annotations as SWG;

/**
 * @SWG\Info(
 *   title="Highcore API",
 *   version="2.0",
 *   description="Highcore Playground description",
 *   @SWG\License(
 *      name="MIT"
 *   )
 * )
 *
 * @SWG\SecurityScheme(
 *   type="basic",
 *   securityDefinition="highcore_auth"
 * )
 *
 * @SWG\Definition(definition="StackRef",
 *   @SWG\Property(property="name", type="string", description="A shallow Stack object with only 'name' to reference a real Stack")
 * )
 * @SWG\Definition(definition="Ui")
 * @SWG\Definition(definition="AwsStatus",
 *      @SWG\Property(property="operation", enum="['CREATE','UPDATE','ROLLBACK','DELETE']"),
 *      @SWG\Property(property="state",
 *          enum="['COMPLETE','FAILED','IN_PROGRESS', 'COMPLETE_CLEANUP_IN_PROGRESS','ROLLBACK_IN_PROGRESS','ROLLBACK_FAILED', 'ROLLBACK_COMPLETE', 'ROLLBACK_COMPLETE_CLEANUP_IN_PROGRESS']"),
 *      @SWG\Property(property="reason", type="string")
 * )
 * @SWG\Definition(definition="AwsOutput",
 *      @SWG\Property(property="OutputKey", type="string"),
 *      @SWG\Property(property="OutputValue", type="string"),
 *      @SWG\Property(property="Description", type="string")
 * )
 *
 *
 */

