<?php


namespace App\Lib\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 *
 * @api
 */
class UniqueEntity extends Constraint
{
    public $message = 'This value is already used.';

    public $entity;
    public $field;
    public $doctrine;

}
