<?php
namespace Govnokod\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\ValidatorInterface;

class UserType extends AbstractType
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $validator = $this->validator;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($validator, $options) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!$data) {
                $data = new $options['data_class'];
            }

            $errors = $validator->validate($data, $options['validation_groups']);
            foreach ($errors as $error) {
                $property_path = $error->getPropertyPath();

                switch ($property_path) {
                    case 'email':
                        $form->add('email');
                        break;

                    case 'username':
                        $form->add('username', null, array(
                            'max_length' => 20
                        ));
                        break;
                }
            }
        });
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Govnokod\UserBundle\Entity\User',
            'validation_groups' => array('FixUser'),
            'email_help_message' => null,
            'username_help_text' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['email_help_message'] = $options['email_help_message'];
    }

    public function getName()
    {
        return 'user';
    }
}
