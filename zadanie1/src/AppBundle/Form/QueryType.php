<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 29.12.17
 * Time: 20:42
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class QueryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keyWords', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Find book'))
        ;
    }
}
