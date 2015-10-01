<?php



namespace App\Lib\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 *
 * @api
 */
class UniqueEntityValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }

        $entity = $constraint->entity;
        $field = $constraint->field;
        $doctrine = $constraint->doctrine;

        $repository = $doctrine->getEntityManager()->getRepository($entity);
        $used = $repository->findOneBy(array($field=>$value));

        if($used){
            $this->context->addViolation($constraint->message, array(
                '{{ value }}' => $this->formatValue($value),
            ));
        }
    }
}
