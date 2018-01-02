<?php
/**
 * Created by PhpStorm.
 * User: ania
 * Date: 28.12.17
 * Time: 14:10
 */

namespace AppBundle\Form;


use AppBundle\Model\Hyperlink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HyperlinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address', TextType::class)
            ->add('numberOfRecursion', IntegerType::class)
            ->add('save', SubmitType::class, array('label' => 'Generate hyperlinks'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Hyperlink::class,
        ));
    }
}
