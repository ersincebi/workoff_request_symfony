<?php

namespace App\Controller;

use App\Constants\EmployeeKeys;
use App\Constants\WorkoffKeys;
use App\Entity\Employees;
use App\Entity\Workoffs;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class WorkoffsController extends AbstractController
{
	private const PRESENT = "present";

	private const ABSENT = "absent";

	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Request $request
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/workoff/add', name: 'app_workoffs_add', methods: "POST")]
    public function add(Request $request): JsonResponse
    {
	    try {
			$workoff = new Workoffs();
		    $workoff->setBeginDate(new \DateTime($request->request->get(WorkoffKeys::BEGIN_DATE)));
		    $workoff->setEndDate(new \DateTime($request->request->get(WorkoffKeys::END_DATE)));

			$this->entityManager->persist($workoff);

		    $employee = $this->entityManager->getRepository(Employees::class)
			    ->findOneBy([EmployeeKeys::TC_NO => $request->request->get(WorkoffKeys::TC_NO)]);

			$employee->addWorkoff($workoff);

		    $this->entityManager->flush();

		    return $this->json([
			    "message" => "Workoff added for employee " . $employee->getName()
		    ]);
	    } catch(Exception $exception) {
		    return $this->json([
			    "message" => "Unable to add workoff for employee " . $employee->getName(),
			    "error" => $exception->getMessage()
		    ]);
	    }
    }

	/**
	 * @param Request $request
	 * @param string $tcNo
	 * @param string $beginDate
	 * @param string $endDate
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/workoff/{tcNo}/{beginDate}/{endDate}', name: 'app_workoffs_update', methods: "PUT")]
	public function edit(Request $request, string $tcNo, string $beginDate, string $endDate): JsonResponse
	{
		try {
			$workoff = $this->entityManager->createQueryBuilder()
				->select('w')
				->from(Workoffs::class, 'w')
				->join(Employees::class, 'e')
				->where('e.' . EmployeeKeys::TC_NO . '=:tc_no')
				->andWhere("w." . WorkoffKeys::BEGIN_DATE . "=:begin")
				->andWhere("w." . WorkoffKeys::END_DATE . "=:end")
				->setMaxResults(1)
				->setParameters([
					':tc_no' => $tcNo,
					':begin' =>(new \DateTime($beginDate))->format('Y-m-d H:i:s'),
					':end' => (new \DateTime($endDate))->format('Y-m-d H:i:s')
				])
				->getQuery()
				->getResult();

			$workoff[0]->setBeginDate(new \DateTime($request->request->get(WorkoffKeys::BEGIN_DATE)));
			$workoff[0]->setEndDate(new \DateTime($request->request->get(WorkoffKeys::END_DATE)));

			$this->entityManager->persist($workoff);

			$this->entityManager->flush();

			return $this->json([
				"message" => "Workoff updated for employee " . $workoff[0]->getTcNo()->getName()
			]);
		} catch(Exception $exception) {
			return $this->json([
				"message" => "Unable to update workoff for employee " . $workoff[0]->getTcNo()->getName(),
				"error" => $exception->getMessage()
			]);
		}
	}

	/**
	 * @param Request $request
	 * @param string $tcNo
	 * @param string $beginDate
	 * @param string $endDate
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/workoff/{tcNo}/{beginDate}/{endDate}', name: 'app_workoffs_delete', methods: "DELETE")]
	public function delete(Request $request, string $tcNo, string $beginDate, string $endDate): JsonResponse
	{
		try {
			$employee = $this->entityManager->getRepository(Employees::class)
				->findOneBy([EmployeeKeys::TC_NO => $tcNo]);

			$workoff = $this->entityManager->createQueryBuilder()
				->delete()
				->from(Workoffs::class, 'w')
				->where('w.' . WorkoffKeys::TC_NO . '=:tc_no')
				->andWhere("w." . WorkoffKeys::BEGIN_DATE . "=:begin")
				->andWhere("w." . WorkoffKeys::END_DATE . "=:end")
				->setMaxResults(1)
				->setParameters([
					':tc_no' => $employee->getId(),
					':begin' =>(new \DateTime($beginDate))->format('Y-m-d H:i:s'),
					':end' => (new \DateTime($endDate))->format('Y-m-d H:i:s')
				])
				->getQuery()
				->execute();

			return $this->json([
				"message" => "Workoff removed for employee " . $employee->getName()
			]);
		} catch(Exception $exception) {
			return $this->json([
				"message" => "Unable to remove workoff for employee " . $employee->getName(),
				"error" => $exception->getMessage()
			]);
		}
	}

	/**
	 * @param Request $request
	 * @param string $beginDate
	 * @param string $endDate
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/workoff/{beginDate}/{endDate}', name: 'app_workoffs_filter_by_date_range', methods: "GET")]
	public function getEmployeesListByDateRange(Request $request, string $beginDate, string $endDate): JsonResponse
	{
		try {
			$workoff = $this->entityManager->createQueryBuilder()
				->select('e')
				->from(Workoffs::class, 'w')
				->join(Employees::class, 'e')
				->where("w." . WorkoffKeys::BEGIN_DATE . ">=:begin")
				->orWhere("w." . WorkoffKeys::END_DATE . "<=:end")
				->setParameters([
					':begin' =>(new \DateTime($beginDate))->format('Y-m-d H:i:s'),
					':end' => (new \DateTime($endDate))->format('Y-m-d H:i:s')
				])
				->getQuery()
				->execute();

			return $this->json([
				"message" => "",
				"data" => self::fetchEmployeeData($workoff)
			]);
		} catch(Exception $exception) {
			return $this->json([
				"message" => "Unable to find workoff for date range {$beginDate} - {$endDate}",
				"error" => $exception->getMessage()
			]);
		}
	}

	/**
	 * @param Request $request
	 * @param string $name
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/workoff/{name}', name: 'app_workoffs_filter_by_date_range', methods: "GET")]
	public function getEmployeeByName(Request $request, string $name): JsonResponse
	{
		try {
			$workoff = $this->entityManager->createQueryBuilder()
				->select('w')
				->from(Workoffs::class, 'w')
				->join(Employees::class, 'e')
				->where("e." . EmployeeKeys::NAME . " LIKE :name")
				->orWhere("e." . EmployeeKeys::SURNAME . " LIKE :name")
				->orWhere("CONCAT(e.name, e.surname) LIKE :name")
				->setParameter(':name', "%{$name}%")
				->getQuery()
				->execute();

			return $this->json([
				"message" => "",
				"data" => self::fetchWorkoffData($workoff)
			]);
		} catch(Exception $exception) {
			return $this->json([
				"message" => "Unable to find any workoff for employee {$name}",
				"error" => $exception->getMessage()
			]);
		}
	}

	/**
	 * @param Request $request
	 * @param string $workoffStatus
	 * @return JsonResponse
	 * @throws Exception
	 */
	#[Route('/api/v1/workoff/{workoffStatus}/status', name: 'app_workoffs_filter_by_date_range', methods: "GET")]
	public function getEmployeesListByWorkoffStatus(Request $request, string $workoffStatus): JsonResponse
	{
		try {
			$workoff = $this->entityManager->createQueryBuilder()
				->select('e')
				->from(Workoffs::class, 'w')
				->join(Employees::class, 'e');

			if (self::PRESENT) {
				$workoff->where("w." . WorkoffKeys::BEGIN_DATE . " <= :now")
					->orWhere("w." . WorkoffKeys::END_DATE . " >= :now");
			} else if (self::ABSENT) {
				$workoff->where("w." . WorkoffKeys::BEGIN_DATE . " >= :now")
					->orWhere("w." . WorkoffKeys::END_DATE . " <= :now");
			}

			$workoff->setParameter(':now', new \DateTime('now'));

			return $this->json([
				"message" => "",
				"data" => self::fetchEmployeeData($workoff->getQuery()->execute())
			]);
		} catch(Exception $exception) {
			return $this->json([
				"message" => "Unable to find {$workoffStatus} employees",
				"error" => $exception->getMessage()
			]);
		}
	}

	/**
	 * @param mixed $data
	 * @return array
	 */
	private static function fetchEmployeeData(mixed $data): array
	{
		return array_map(
			static function($item) {
				return [
					EmployeeKeys::TC_NO => $item->getTcNo(),
					EmployeeKeys::SGK_NO => $item->getSgkNo(),
					EmployeeKeys::NAME => $item->getName(),
					EmployeeKeys::SURNAME => $item->getSurname(),
					EmployeeKeys::BEGIN_DATE => $item->getBeginDate(),
					EmployeeKeys::QUIT_DATE => $item->getQuitDate()
				];
			}, $data);
	}

	/**
	 * @param mixed $data
	 * @return array
	 */
	private static function fetchWorkoffData(mixed $data): array
	{
		return array_map(
			static function($item) {
				return [
					EmployeeKeys::TC_NO => $item->getTcNo()->getTcNo(),
					EmployeeKeys::NAME => $item->getTcNo()->getName(),
					EmployeeKeys::SURNAME => $item->getTcNo()->getSurname(),
					WorkoffKeys::BEGIN_DATE => $item->getBeginDate(),
					WorkoffKeys::END_DATE => $item->getEndDate()
				];
			}, $data);
	}
}
