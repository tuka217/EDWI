<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 28.12.17
 * Time: 23:04
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DirectoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pathToDir', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Process book'))
        ;
    }
}
