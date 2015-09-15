<?php
/**
 * Created by PhpStorm.
 * User: wal21
 * Date: 15/09/15
 * Time: 16:52
 */

namespace Src\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class DemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',null,array(
//            'mapped' => false
        ));
        $builder->add('date', 'datetime');
    }

    public function getName()
    {
        return 'demo';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Src\Entities\Demo',
        ));
    }
}