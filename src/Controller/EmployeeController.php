<?php

namespace App\Controller;

use App\Constants\EmployeeKeys;
use App\Entity\Employees;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends AbstractController
{
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
    #[Route('/api/v1/employee/add', name: 'app_add_api', methods: "POST")]
    public function add(Request $request): JsonResponse
    {
	    return $this->responseToEndUser($this->mapEntity($request, new Employees()), ["New Employee Created", "Unable to create employee"]);
    }

	/**
	 * @param Request $request
	 * @param string $tcNo
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/employee/{tcNo}/edit', name: 'app_edit_api', methods: "PUT")]
	public function edit(Request $request, string $tcNo): JsonResponse
	{
		$employee = $this->entityManager->getRepository(Employees::class)
			->findOneBy([EmployeeKeys::TC_NO => $tcNo]);

		return $this->responseToEndUser($this->mapEntity($request, $employee), ["Employee Updated", "Unable to update employee {$tcNo}"]);
	}

	/**
	 * @param Request $request
	 * @param string $tcNo
	 * @return JsonResponse
	 */
	#[Route('/api/v1/employee/{tcNo}/delete', name: 'app_delete_api', methods: "DELETE")]
	public function delete(Request $request, string $tcNo): JsonResponse
	{
		$employee = $this->entityManager->getRepository(Employees::class)
			->findOneBy([EmployeeKeys::TC_NO => $tcNo]);

		return $this->responseToEndUser($employee, ["Employee {$tcNo} deleted", "Unable to delete employee {$tcNo}"], TRUE);
	}

	/**
	 * @param Request $request
	 * @param mixed $entity
	 * @return mixed
	 * @throws Exception
	 */
	private function mapEntity(Request $request, mixed $entity): mixed
	{
		if ($request->request->get(EmployeeKeys::TC_NO) !== NULL) {
			$entity->setTcNo($request->request->get(EmployeeKeys::TC_NO));
		}

		if ($request->request->get(EmployeeKeys::SGK_NO) !== NULL) {
			$entity->setSgkNo($request->request->get(EmployeeKeys::SGK_NO));
		}

		if ($request->request->get(EmployeeKeys::NAME) !== NULL) {
			$entity->setName($request->request->get(EmployeeKeys::NAME));
		}

		if ($request->request->get(EmployeeKeys::SURNAME) !== NULL) {
			$entity->setSurname($request->request->get(EmployeeKeys::SURNAME));
		}

		if ($request->request->get(EmployeeKeys::BEGIN_DATE) !== NULL) {
			$entity->setBeginDate(new \DateTime($request->request->get(EmployeeKeys::BEGIN_DATE)));
		}

		if ($request->request->get(EmployeeKeys::QUIT_DATE) !== NULL) {
			$entity->setQuitDate(new \DateTime($request->request->get(EmployeeKeys::QUIT_DATE)));
		}

		return $entity;
	}

	/**
	 * @param mixed $employee
	 * @param array $messages
	 * @param bool $remove
	 * @return JsonResponse
	 */
	private function responseToEndUser(mixed $employee, array $messages, bool $remove = FALSE)
	{
		try {
			if(!$remove) {
				$this->entityManager->persist($employee);
			} else {
				$this->entityManager->remove($employee);
			}
			$this->entityManager->flush();

			return $this->json([
				"message" => $messages[0]
			]);
		} catch(Exception $exception) {
			return $this->json([
				"message" => $messages[1],
				"error" => $exception->getMessage()
			]);
		}
	}
}
