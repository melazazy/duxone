<?php

namespace Tests\Feature\Accounting;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature test for ensuring the basic accessibility of Accounting module routes.
 *
 * This test class verifies that the primary routes for the Accounting module
 * are correctly defined and return a successful HTTP response for authenticated users.
 * It acts as a smoke test for the module's routing configuration.
 */
class AccountingRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The authenticated user for the tests.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Set up the test environment.
     *
     * This method is called before each test in the class.
     * It creates and authenticates a user.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /**
     * @test
     * Test that the accounting dashboard route is accessible.
     */
    public function it_can_access_the_accounting_dashboard_route(): void
    {
        // Note: This test will fail until the route is defined.
        // We will define it in a subsequent step.
        $response = $this->get('/accounting/dashboard');

        $response->assertStatus(200);
    }

    /**
     * @test
     * Test that the accounting invoices route is accessible.
     */
    public function it_can_access_the_accounting_invoices_route(): void
    {
        // Note: This test will fail until the route is defined.
        $response = $this->get('/accounting/invoices');

        $response->assertStatus(200);
    }

    /**
     * @test
     * Test that the accounting expenses route is accessible.
     */
    public function it_can_access_the_accounting_expenses_route(): void
    {
        // Note: This test will fail until the route is defined.
        $response = $this->get('/accounting/expenses');

        $response->assertStatus(200);
    }
}
