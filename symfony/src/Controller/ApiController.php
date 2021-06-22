<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;

class ApiController extends AbstractController
{

    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var $factory */
    private $factory;

    /**
     * ApiController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;

        $this->factory = new PasswordHasherFactory([
            'common' => ['algorithm' => 'bcrypt'],
            'memory-hard' => ['algorithm' => 'sodium'],
        ]);
    }

    /**
     * @param $data
     * @param $statusCode
     * @param null $errorMessage
     * @return Response
     */
    protected function response($data, $statusCode, $errorMessage = null): Response
    {
        $response = new Response();

        if($statusCode == 200) {
            $response->setContent(json_encode([
                "status" => "success",
                "code" => 200,
                "message" => "OK",
                "data" => $data,
            ], JSON_PRETTY_PRINT));
        } else {
            $response->setContent(json_encode([
                "status" => "error",
                "code" => $statusCode,
                "message" => $errorMessage,
                "data" => [],
            ], JSON_PRETTY_PRINT));
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param string $password
     * @return string
     */
    protected function generatePasswordHash(string $password): string
    {
        $passwordHasher = $this->factory->getPasswordHasher('common');
        return $passwordHasher->hash($password);
    }

    /**
     * @param $hash
     * @param $password
     * @return bool
     */
    protected function verifyPassword($hash, $password): bool
    {
        $passwordHasher = $this->factory->getPasswordHasher('common');
        return $passwordHasher->verify($hash, $password);
    }
}