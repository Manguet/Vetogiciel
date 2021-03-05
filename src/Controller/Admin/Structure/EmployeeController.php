<?php

namespace App\Controller\Admin\Structure;

use App\Entity\Structure\Employee;
use App\Form\Structure\EmployeeType;
use App\Service\User\PasswordEncoderServices;
use Doctrine\ORM\EntityManagerInterface;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTableFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Benjamin Manguet <benjamin.manguet@gmail.com>
 *
 * @Route("/admin/employee", name="admin_employee_")
 */
class EmployeeController extends AbstractController
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
     * EmployeeController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param PasswordEncoderServices $encoderServices
     */
    public function __construct(EntityManagerInterface $entityManager, PasswordEncoderServices $encoderServices)
    {
        $this->entityManager   = $entityManager;
        $this->encoderServices = $encoderServices;
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
                'label'     => 'Nom de l\'employé',
                'orderable' => true,
                'render'    => function ($value, $employee) {

                    $employeeName = $value . ' ' . $employee->getLastname();

                    return '<a href="/admin/employee/edit/' . $employee->getId() . '">' . $employeeName . '</a>';
                }
            ])
            ->add('lastname', TextColumn::class, [
                'visible'   => false,
            ])
            ->add('sector', TextColumn::class, [
                'label'     => 'Secteur',
                'orderable' => false,
                'render'    => function ($value, $employee) {

                    if (!$employee->getSector()->isEmpty()) {

                        $sectors = [];
                        foreach ($employee->getSector() as $sector) {
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
            ->add('isManager', TextColumn::class, [
                'label'     => 'Manager ?',
                'orderable' => true,
                'render'    => function ($value, $employee) {

                    if ($value) {
                        return '<i class="far fa-check-circle"></i>';
                    }

                    return '';
                }

            ])
            ->add('delete', TextColumn::class, [
                'label'   => 'Supprimer ?',
                'render'  => function($value, $employee) {
                    return $this->renderView('admin/employee/include/_delete-button.html.twig', [
                        'employee' => $employee,
                    ]);
                }
            ])
            ->addOrderBy('lastname')
            ->createAdapter(ORMAdapter::class, [
                'entity' => Employee::class
            ])
        ;

        $table->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('admin/employee/index.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param Request $request
     *
     * @return Response
     */
    public function newEmployee(Request $request): Response
    {
        $employee = new Employee();

        $form = $this->createForm(EmployeeType::class, $employee, [
            'enablePassword' => true,
            'isShow'         => false,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $employee);

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_employee_index');
        }

        return $this->render('admin/employee/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => true,
            'isShow'         => false,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param Employee $employee
     *
     * @return Response
     */
    public function editVeterinary(Request $request, Employee $employee): Response
    {
        $form = $this->createForm(EmployeeType::class, $employee, [
            'enablePassword' => false,
            'isShow'         => true,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** Manually encode password */
            $this->encoderServices->encodePassword($form, $employee);

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->redirectToRoute('admin_employee_index');
        }

        return $this->render('admin/employee/new.html.twig', [
            'form'           => $form->createView(),
            'enablePassword' => false,
            'isShow'         => true,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete", methods={"POST"})
     *
     * @param Employee $employee
     *
     * @return JsonResponse
     */
    public function delete(Employee $employee): JsonResponse
    {
        if (!$employee instanceof Employee) {
            return new JsonResponse('Employee Not Found', 404);
        }

        $this->entityManager->remove($employee);
        $this->entityManager->flush();

        return new JsonResponse('Employee deleted with success', 200);
    }
}