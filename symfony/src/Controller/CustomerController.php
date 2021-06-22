<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Customers;

class CustomerController extends ApiController
{
    /**
     * @return Response
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
                'revenue' => number_format((float)$activeCustomer['revenue'], 2, '.', '')
            );
        }

        return $this->response($result, 200, null);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createCustomer(Request $request): Response
    {
        $parameters = json_decode($request->getContent(), true);
        $name = $parameters['name'];
        $username = $parameters['username'];
        $password = $parameters['password'];

        if($name && $username && $password) {

            $hash = $this->generatePasswordHash($password);

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

            $response = $this->response($customer, 200, null);

        } else {
            $response = $this->response([], 400, "Missing Parameter!");
        }

        return $response;
    }

    /**
     * @param int $id
     * @return Response
     */
    public function getCustomer(int $id): Response
    {
        $customer = $this->entityManager->getRepository(Customers::class)
            ->getCustomerById($id);

        if($customer) {
            $customer[0]['since'] = date_format($customer[0]['since'],"Y-m-d");
            $customer[0]['revenue'] = number_format((float)$customer[0]['revenue'], 2, '.', '');

            unset($customer[0]['username']);
            unset($customer[0]['password']);
            unset($customer[0]['status']);

            $response = $this->response(array_shift($customer), 200, null);
        } else{
            $response = $this->response([], 404, "Customer Not Found!");
        }

        return $response;
    }
}
