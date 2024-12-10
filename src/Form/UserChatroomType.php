<?php

namespace App\Form;

use App\Entity\Chatroom;
use App\Entity\User;
use App\Entity\UserChatroom;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserChatroomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastRead', null, [
                'widget' => 'single_text'
            ])
            ->add('_user', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('_chatroom', EntityType::class, [
                'class' => Chatroom::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserChatroom::class,
        ]);
    }
}
