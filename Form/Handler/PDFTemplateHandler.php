<?php

namespace Ibnab\Bundle\PmanagerBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

use Ibnab\Bundle\PmanagerBundle\Entity\PDFTemplate;

class PDFTemplateHandler
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ObjectManager
     */
    protected $translator;

    /**
     * @param FormInterface $form
     * @param Request       $request
     * @param ObjectManager $manager
     * @param Translator    $translator
     */
    public function __construct(FormInterface $form, Request $request, ObjectManager $manager, TranslatorInterface $translator)
    {
        $this->form       = $form;
        $this->request    = $request;
        $this->manager    = $manager;
        $this->translator = $translator;
    }

    /**
     * Process form
     *
     * @param  PDFTemplate $entity
     * @return bool True on successful processing, false otherwise
     */
    public function process(PDFTemplate $entity)
    {
        $this->form->setData($entity);

        if (in_array($this->request->getMethod(), array('POST', 'PUT'))) {
            // deny to modify system templates
            if ($entity->getIsSystem() && !$entity->getIsEditable()) {
                $this->form->addError(
                    new FormError($this->translator->trans('oro.email.handler.attempt_save_system_template'))
                );

                return false;
            }

            $this->form->submit($this->request);

            if ($this->form->isValid()) {
                // mark an email template creating by an user as editable
                if (!$entity->getId()) {
                    $entity->setIsEditable(true);
                }
                $this->manager->persist($entity);
                $this->manager->flush();

                return true;
            }
        }

        return false;
    }
}
