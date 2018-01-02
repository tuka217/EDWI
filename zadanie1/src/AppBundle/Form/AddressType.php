<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 26.11.17
 * Time: 09:31
 */
namespace AppBundle\Form;

use AppBundle\Model\Address;
use  Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Download data'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
        ));
    }
}
