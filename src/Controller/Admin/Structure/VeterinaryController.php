<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Veterinary;
use App\Form\Structure\VeterinaryFormType;
use App\Security\EmailVerifier;
use App\Service\User\PasswordEncoderServices;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/veterinary", name="admin_veterinary_")
 */
class VeterinaryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PasswordEncoderServices
     */
    private $encoderServices;

    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * ClinicController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoderServices $encoderServices
     * @param EmailVerifier $emailVerifier
     * @param FlashBagInterface $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordEncoderServices $encoderServices,
                                EmailVerifier $emailVerifier, FlashBagInterface $flashBag)
    {
        $this->entityManager   = $entityManager;
        $this->encoderServices = $encoderServices;
        $this->emailVerifier   = $emailVerifier;
        $this->flashBag        = $flashBag;
    }

    /**
     * @Route("/index", name="index")
     *
     * @param Request $request
     * @param DataTableFactory $dataTableFactory
     *
     * @return Response
     */
    public function index(Request $request, DataTableFactory $dataTableFactory): Response
    {
        $table = $dataTableFactory->create()
            ->add('firstname', TextColumn::class, [
                'label'     => 'Nom du vétérinaire',
                'orderable' => true,
                'render'    => function ($value, $veterinary) {

                    $veterinaryName = 'Dr. ' . $value . ' ' . $veterinary->getLastname();

                    return '<a href="/admin/veterinary/edit/' . $veterinary->getId() . '">' . $veterinaryName . '</a>';
                }
            ])
            ->add('lastname', TextColumn::class, [
                'visible'   => false,
            ])
            ->add('number', TextColumn::class, [
                'label'     => 'Numéro d\'ordre',
                'orderable' => true,
            ])
            ->add('clinic', TextColumn::class, [
                'label'     => 'Clinique',
                'orderable' => false,
                'render'    => function ($value, $veterinary) {

                    if (null !== $veterinary->getClinic()) {
                        return $veterinary->getClinic()->getName();
                    }

                    return '';
                }
            ])
            ->add('sector', TextColumn::class, [
                'label'     => 'Secteur',
                'orderable' => false,
                'render'    => function ($value, $veterinary) {

                    if (!$veterinary->getSector()->isEmpty()) {

                        $sectors = [];
                        foreach ($veterinary->getSector() as $sector) {
                            $sectors[] = $sector->getName();
                        }

                        $sectors = implode('/', $sectors);

                        if (strlen($sectors) > 30) {

                            return substr($sectors, 0, 30) . '...';
                        }

                        return $sectors;
                    }

                    return '';
                }
            ])
            ->add('delete', TextColumn::class, [
                'label'   => 'Supprimer ?',
                'render'  => function($value, $veterinary) {
                    return $this->renderView('admin/veterinary/include/_delete-button.html.twig', [
                        'veterinary' => $veterinary,
                    ]);
                }
            ])
            ->addOrderBy('firstname')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Veterinary::class
            ])
        ;

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/veterinary/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param Request $request
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function newVeterinary(Request $request): Response
    {
        $veterinary = new Veterinary();

        $form = $this->createForm(VeterinaryFormType::class, $veterinary, [
            'enablePassword' => true,
            'isShow'         => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $veterinary);

            $this->entityManager->persist($veterinary);
            $this->entityManager->flush();

            /**
             * Generate a signed url and email it to the user
             */
            $this->emailVerifier->sendEmailConfirmation('app_register_verify_email', $veterinary,
                (new TemplatedEmail())
                    ->from(new Address('benjamin.manguet@gmail.com', 'Vetogiciel'))
                    ->to($veterinary->getEmail())
                    ->subject('Confirmation d\'adresse : Vetogiciel')
                    ->htmlTemplate('email/confirmation_email.html.twig')
            );

            $this->flashBag->add('warning', 'Merci de faire confirmer l\'adresse mail : ' . $veterinary->getEmail() . ' afin d\'accéder aux fonctionnalités.');

            return $this->redirectToRoute('admin_veterinary_index');
        }

        return $this->render('admin/veterinary/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => true,
            'isShow'         => false,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Veterinary $veterinary
     *
     * @return Response
     */
    public function editVeterinary(Request $request, Veterinary $veterinary): Response
    {
        $form = $this->createForm(VeterinaryFormType::class, $veterinary, [
            'enablePassword' => false,
            'isShow'         => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $veterinary);

            $this->entityManager->persist($veterinary);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_veterinary_index');
        }

        return $this->render('admin/veterinary/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => false,
            'isShow'         => true,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Veterinary $veterinary
     *
     * @return JsonResponse
     */
    public function delete(Veterinary $veterinary): JsonResponse
    {
        if (!$veterinary instanceof Veterinary) {
            return new JsonResponse('Veterinary Not Found', 404);
        }

        $this->entityManager->remove($veterinary);
        $this->entityManager->flush();

        return new JsonResponse('Veterinary deleted with success', 200);
    }
}