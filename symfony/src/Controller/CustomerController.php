<?php

namespace App\Controller;

use App\Controller\ApiController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Customers;
use App\Helper\ApiHelper;
use Throwable;

class CustomerController extends ApiController
{
    /**
     * @Route("/api/v1/customers", methods={"GET"})
     */
    public function getAllCustomers(): Response
    {
        $activeCustomers = $this->entityManager->getRepository(Customers::class)
            ->getActiveCustomers();

        $result = [];
        foreach ($activeCustomers as $activeCustomer) {
            $result[] = array(
                'id' => $activeCustomer['id'],
                'name' => $activeCustomer['name'],
                'since' => date_format($activeCustomer['since'],"Y-m-d"),
                'revenue' => ApiHelper::numberFormater($activeCustomer['revenue'])
            );
        }

        return $this->response($result, 200, null);
    }

    /**
     * @Route("/api/v1/customer/{id}", methods={"GET"})
     */
    public function getCustomer(int $id): Response
    {
        $customer = $this->entityManager->getRepository(Customers::class)
            ->getCustomerById($id);

        if($customer) {
            $customer[0]['since'] = date_format($customer[0]['since'],"Y-m-d");
            $customer[0]['revenue'] = ApiHelper::numberFormater($customer[0]['revenue']);

            unset($customer[0]['username']);
            unset($customer[0]['password']);
            unset($customer[0]['status']);

            $response = $this->response(array_shift($customer), 200, null);
        } else{
            $response = $this->response([], 404, "Customer Not Found!");
        }

        return $response;
    }

    /**
     * @Route("/api/v1/customer/create", methods={"PUT"})
     */
    public function createCustomer(Request $request): Response
    {
        try{
            $parameters = json_decode($request->getContent(), true);
            $name = $parameters['name'];
            $username = $parameters['username'];
            $password = $parameters['password'];

            if($name && $username && $password) {

                $hash = md5($password);

                $now = new \DateTime();

                $customer = new Customers();
                $customer->setUsername($username);
                $customer->setPassword($hash);
                $customer->setName($name);
                $customer->setSince($now);
                $customer->setRevenue(0.0);
                $customer->setStatus(1);

                $this->entityManager->persist($customer);
                $this->entityManager->flush();
                $createdId = $customer->getId();

                $response = $this->response(["created_customer_id" => $createdId], 200, null);

            } else {
                $response = $this->response([], 400, "Missing Parameter!");
            }
        } catch (Throwable $exception) {
            $response = $this->response([], $exception->getCode(), $exception->getMessage());
        }

        return $response;
    }

    /**
     * @Route("/api/v1/customer/update", methods={"POST"})
     */
    public function updateCustomer(Request $request): Response
    {
        $id = $request->query->get('id');
        $username = $request->query->get('username');
        $name = $request->query->get('name');
        $password = $request->query->get('password');
        $revenue = $request->query->get('revenue');
        $status = $request->query->get('status');

        if($id && $id > 0) {
            $customer = $this->entityManager->getRepository(Customers::class)
                ->findOneBy(['id' => $id]);

            if($customer) {
                if($username) {
                    $customer->setUsername($username);
                }

                if($name) {
                    $customer->setName($name);
                }

                if($password) {
                    $hash = md5($password);
                    $customer->setPassword($hash);
                }

                if($revenue) {
                    $customer->setRevenue($revenue);
                }

                if($status) {
                    $customer->setStatus($status);
                }

                $this->entityManager->flush();
                $response = $this->response(["updated_customer_id" => $id], 200, null);
            } else{
                $response = $this->response([], 404, "Customer Not Found!");
            }
        } else{
            $response = $this->response([], 404, "Incorrect Customer ID!");
        }

        return $response;
    }

    /**
     * @Route("/api/v1/customer/{id}", methods={"DELETE"})
     */
    public function deleteCustomer(int $id): Response
    {
        $customer = $this->entityManager->getRepository(Customers::class)
            ->findOneBy(['id' => $id]);

        if($customer) {
            $this->entityManager->remove($customer);
            $this->entityManager->flush();

            $response = $this->response(["deleted_customer_id" => $id], 200, null);
        } else {
            $response = $this->response([], 404, "Product Not Found!");
        }

        return $response;
    }
}
