<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Job;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class JobTest extends ApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    const URL = '/api/jobs';
    const MODEL_CLASS = 'Job';
    const MODELS = 'jobs';
    const MODEL = 'job';

    public function testGetJobs(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', self::URL);

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/' . self::MODEL_CLASS,
            '@id' => '/api/' . self::MODELS,
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 50,
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        // Be sure that total items is the same as the number of items loaded by Alice
        $this->assertCount(50, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Job::class);
    }

    public function testGetOneJob(): void
    {
        $response = static::createClient()->request('GET', self::URL . '/1');

        $this->assertMatchesRegularExpression('~^/api/' . self::MODELS . '/\d+$~', $response->toArray()['@id']);
        $this->assertResponseIsSuccessful(self::MODEL . ' retrieve successful');
    }

    public function testCreateJob(): void
    {
        $response = static::createClient()->request('POST', self::URL, ['json' => [
            "name" => "Développeur web SymfonyTest - H/F",
            "company" => "/api/companies/1",
            "recruiter" => "/api/recruiters/1",
            "status" => 1,
            "contract" => 1,
            "contractDuration" => 1,
            "educationLevel" => 1,
            "experienceLevel" => 1,
            "startDate" => "2023-05-31T13:31:39+00:00",
            "startAsap" => 1,
            "salaryPrivacy" => true,
            "context" => "Mon context pour le JOB",
            "description" => "Ma description de JOB",
            "profile" => "Mon profile pour le JOB",
            "comment" => "Mon commentaire pour le JOB",
            "fullRemote" => true,
            "salaryLow" => 45000,
            "salaryHigh" => 60000
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/' . self::MODEL_CLASS,
            '@type' => self::MODEL_CLASS,
            "name" => "Développeur web SymfonyTest - H/F",
            "company" => "/api/companies/1",
            "recruiter" => "/api/recruiters/1",
            "status" => 1,
            "contract" => 1,
            "contractDuration" => 1,
            "educationLevel" => 1,
            "experienceLevel" => 1,
            "startDate" => "2023-05-31T13:31:39+00:00",
            "startAsap" => 1,
            "salaryPrivacy" => true,
            "context" => "Mon context pour le JOB",
            "description" => "Ma description de JOB",
            "profile" => "Mon profile pour le JOB",
            "comment" => "Mon commentaire pour le JOB",
            "fullRemote" => true,
            "salaryLow" => 45000,
            "salaryHigh" => 60000
        ]);
        $this->assertMatchesRegularExpression('~^/api/' . self::MODELS . '/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Job::class);
    }

    public function testUpdateJob(): void
    {
        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        // ISBN 9786644879585 has been generated by Alice when loading test fixtures.
        // Because Alice use a seeded pseudo-random number generator, we're sure that this ISBN will always be generated.
        $iri = $this->findIriBy(Job::class, ['id' => '1']);

        $client->request('PUT', $iri, ['json' => [
            "name" => "Développeur web SymfonyTest - H/F",
            "company" => "/api/companies/1",
            "recruiter" => "/api/recruiters/1",
            "status" => 1,
            "contract" => 1,
            "contractDuration" => 1,
            "educationLevel" => 1,
            "experienceLevel" => 1,
            "startDate" => "2023-05-31T13:31:39+00:00",
            "startAsap" => 1,
            "salaryPrivacy" => true,
            "context" => "Mon context pour le JOB Updated",
            "description" => "Ma description de JOB Updated",
            "profile" => "Mon profile pour le JOB Updated",
            "comment" => "Mon commentaire pour le JOB Updated",
            "fullRemote" => true,
            "salaryLow" => 50000,
            "salaryHigh" => 70000
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            "name" => "Développeur web SymfonyTest - H/F",
            "status" => 1,
            "contract" => 1,
            "contractDuration" => 1,
            "educationLevel" => 1,
            "experienceLevel" => 1,
            "startDate" => "2023-05-31T13:31:39+00:00",
            "startAsap" => 1,
            "salaryPrivacy" => true,
            "context" => "Mon context pour le JOB Updated",
            "description" => "Ma description de JOB Updated",
            "profile" => "Mon profile pour le JOB Updated",
            "comment" => "Mon commentaire pour le JOB Updated",
            "fullRemote" => true,
            "salaryLow" => 50000,
            "salaryHigh" => 70000,
            "updatedAt" => (new \DateTime())->format('Y-m-d\TH:i:sP'),
        ]);
    }

    public function testDeleteJob(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Job::class, ['id' => '1']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::getContainer()->get('doctrine')->getRepository(Job::class)->findOneBy(['id' => '1'])
        );
    }
}
