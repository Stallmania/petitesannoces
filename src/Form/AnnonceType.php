<?php

namespace App\Form;

use App\Entity\Annonces;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;


class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title',TextType::class)
            //->add('slug')
            ->add('content', CKEditorType::class) // Ce champ sera remplacé par un éditeur WYSIWYG
            //->add('created_at')
            //->add('active')
            //->add('users')
            ->add('categories',EntityType::class,[
                'class'=> Categories::class])
                
            ->add('images',FileType::class,[
                'label'=> false,
                'multiple'=> true,
                'required'=> false,
                'mapped'=> false]);

/*             ->add('Envoyer',SubmitType::class); */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
