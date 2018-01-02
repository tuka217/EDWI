<?php

namespace AppBundle\Form;

use AppBundle\Helper\DirScanner;
use AppBundle\Model\Algorithm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlgorithmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var DirScanner $dirScanner */
        $dirScanner = $options['dir_scanner'];

        $builder
            ->add('k', TextType::class)
            ->add('thresh', TextType::class)
            ->add('fileName', ChoiceType::class, ['choices' => $dirScanner->scanDir()])
            ->add('save', SubmitType::class, array('label' => 'Run'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Algorithm::class,
        ));

        $resolver->setRequired('dir_scanner');
    }
}
