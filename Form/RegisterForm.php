<?php

namespace RunetId\ApiClientBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RegisterForm
 */
class RegisterForm extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Email', 'Symfony\Component\Form\Extension\Core\Type\EmailType', [
                'label' => 'title.email',
            ])
            ->add('FirstName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => 'title.firstname',
            ])
            ->add('LastName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'label' => 'title.lastname',
            ])
            ->add('FatherName', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'required' => false,
                'label' => 'title.middlename',
            ])
            ->add('Phone', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'required' => false,
                'label' => 'title.phone',
            ])
            ->add('Company', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'required' => false,
                'label' => 'title.company',
            ])
            ->add('Position', 'Symfony\Component\Form\Extension\Core\Type\TextType', [
                'required' => false,
                'label' => 'title.company_position',
            ]);
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'RunetId\ApiClientBundle\Entity\NewUser',
        ]);
    }
}
